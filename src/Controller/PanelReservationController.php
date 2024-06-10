<?php

namespace App\Controller;

use App\Entity\ReservationEncaisse;
use App\Repository\CarteRepository;
use App\Repository\ReservationEncaisseRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class PanelReservationController extends AbstractController
{

    #[Route('/panel-reservation', name: 'app_panel_reservation.get', methods: ['GET'])]
    public function index(ReservationRepository $reservations, ReservationEncaisseRepository $reservationEncaisse): Response
    {
        return $this->render('panel_reservation/index.html.twig', [
            'reservations_futur' => $reservations->findByDateAfter(date('d-m-Y')),
            'reservations_passe' => $reservationEncaisse->reservationOrderedByDate()
        ]);
    }

    #[Route('/panel-reservation', name: 'app_panel_reservation.post', methods: ['POST'])]
    public function postIndex(Request $request, EntityManagerInterface $em, ReservationRepository $reservations, CarteRepository $carte, ReservationEncaisseRepository $reservationEncaisse): Response
    {
        $payload = $request->getPayload();

        // si le bouton supprimer est pressé
        if( $payload->get('gestion-reservation-submit-suppression') != null &&
            $this->isCsrfTokenValid('gestion-articles', $payload->get('token')))
        {
            $object = $reservations->find($payload->get('gestion-reservation-submit-suppression'));
            if ($object != null) {
                $em->remove($object);
                $em->flush();
                return $this->render('panel_reservation/index.html.twig', [
                    'deleted' => true,
                    'reservations_futur' => $reservations->findByDateAfter(date('d-m-Y')),
                    'reservations_passe' => $reservationEncaisse->reservationOrderedByDate()
                ]);
            }
            return $this->generateError(400);
        } else if ($payload->get('checkout-validation') != null &&
            $this->isCsrfTokenValid('checkout-reservation', $payload->get('token'))){
            if($reservationEncaisse->reservationIDAlreadyExist($payload->get('checkout-validation'))){
                return $this->generateError(409);
            }
            $arr = [];
            $totalSum = 0;
            $totalMarge = 0;
            foreach ( $payload as $key => $value) {
                if(filter_var($key, FILTER_VALIDATE_INT) !== false){
                    $item = $carte->find($key);
                    $arr[$key] = ['nom'=>$item->getNom(),'prix'=>$item->getPrix(),'categorie'=>$item->getCategorie(),'quantite'=>$value,'marge'=>$item->getMarge()];
                    $totalSum += ($item->getPrix() * $value);
                    $totalMarge += ($item->getMarge() * $value);
                }
            }
            if(count($arr) == 0){
                return $this->generateError(409);
            }
            $reservationSelected = $reservations->find($payload->get('checkout-validation'));
            $reservationEncaisse = new ReservationEncaisse();
            $reservationEncaisse->setIdReservation($payload->get('checkout-validation'))
                ->setDate(new \DateTime(null, new \DateTimeZone('Europe/Paris')))
                ->setArticles($arr)
                ->setNom($reservationSelected->getNom())
                ->setPrenom($reservationSelected->getPrenom())
                ->setCouverts($reservationSelected->getCouverts())
                ->setDateReservation($reservationSelected->getDate())
                ->setTelephone($reservationSelected->getTelephone())
                ->setEmail($reservationSelected->getEmail())
                ->setComments($reservationSelected->getComments())
                ->setHorraire($reservationSelected->getHorraire())
                ->setTotal($totalSum)
                ->setMarge($totalMarge);
            $em->persist($reservationEncaisse);
            $em->remove($reservationSelected);
            $em->flush();
            return $this->render('panel_reservation/checkout_completed.html.twig', [
                'articles' => $arr,
                'total' => $totalSum,
                'numeroReservation' => $payload->get('checkout-validation'),
            ]);

        }

        return $this->generateError(406);
    }


    private function generateError($errorCode): Response
    {
        switch ($errorCode) {
            case 409 :
                // conflict data
                return $this->render('logged_error_code/error409.html.twig', [
                    'message' =>
                        'Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>',
                ]);
            case 406:
                // invalid csrf token / aucun bouton n'est cliqué une erreur est généré
                return $this->render('logged_error_code/error406.html.twig', [
                    'message' => ''
                ]);
            default:
                // standard error
                return $this->render('logged_error_code/error400.html.twig', [
                    'message' =>
                        'Aucune Réservations n\'a été trouvé avec l\'identifiant fournit<br><br>Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>',
                ]);
        }
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN")'))]
    #[Route('/visualisation-reservation-passe', name: 'visualisation-reservation-passe.post', methods: ['POST'])]
    public function visualisationPostIndex(Request $request, ReservationEncaisseRepository $reservationEncaisse): Response {
        $payload = $request->getPayload();
        if(!$this->isCsrfTokenValid('visualisation-past-reservation', $payload->get('token'))){
            return $this->generateError(406);
        }

        if ($payload->get('gestion-visualisation-past') == null) {
            return $this->generateError(409);
        }
        $item = $payload->get('gestion-visualisation-past');
        if($item == null){
            return $this->generateError(400);
        }
        return $this->render('panel_reservation/visualisation.html.twig', [
            'reservation' => $reservationEncaisse->find($payload->get('gestion-visualisation-past'))
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN")'))]
    #[Route('/visualisation-reservation-passe', name: 'visualisation-reservation-passe.get', methods: ['GET'])]
    public function visualisationGetIndex(): Response {
        return $this->generateError(406);
    }
}
