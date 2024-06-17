<?php

namespace App\Controller;

use App\Entity\NonReservationEncaisse;
use App\Entity\Reservation;
use App\Entity\ReservationEncaisse;
use App\Repository\CalendarRepository;
use App\Repository\CarteRepository;
use App\Repository\CategoryRepository;
use App\Repository\NonReservationEncaisseRepository;
use App\Repository\ReservationEncaisseRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManager;
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
    private ReservationRepository $reservations;
    private ReservationEncaisseRepository $reservationEncaisse;
    private NonReservationEncaisseRepository $nonReservation;
    public function __construct(ReservationRepository $reservations, ReservationEncaisseRepository $reservationEncaisse, NonReservationEncaisseRepository $nonReservation){
        $this->reservations = $reservations;
        $this->reservationEncaisse = $reservationEncaisse;
        $this->nonReservation = $nonReservation;
    }

    #[Route('/panel-reservation', name: 'app_panel_reservation.get', methods: ['GET'])]
    public function index(): Response
    {
        return $this->renderPanelReservation();
    }

    #[Route('/panel-reservation', name: 'app_panel_reservation.post', methods: ['POST'])]
    public function postIndex(Request $request, EntityManagerInterface $em, CarteRepository $carte, ): Response
    {
        $payload = $request->getPayload();

        // si le bouton supprimer est pressé
        if( $payload->get('gestion-reservation-submit-suppression') != null &&
            $this->isCsrfTokenValid('gestion-articles', $payload->get('token')))
        {
            $object = $this->reservations->find($payload->get('gestion-reservation-submit-suppression'));
            if ($object != null) {
                $em->remove($object);
                $em->flush();
                return $this->renderPanelReservation(true);
            }
            return $this->generateError(400);
        } else if ($payload->get('confirmer-creation-reservation') != null &&
            $this->isCsrfTokenValid('creation-reservation', $payload->get('token')))
        {
            if( $payload->get('couverts') >= 1 &&
                $payload->get('prenom') != '' &&
                $payload->get('nom') != '' &&
                $payload->get('indicatif') != '' &&
                $payload->get('numero') != '' &&
                filter_var($payload->get('email'), FILTER_VALIDATE_EMAIL) &&
                $payload->get('confirmer-creation-reservation') != ''
            ){
                $this->createReservation($em, $payload);
                return $this->renderPanelReservation(false, true);
            }

            return $this->generateError(400);
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


        if ($payload->get('checkout-validation') != null &&
            $this->isCsrfTokenValid('checkout-reservation', $payload->get('token'))){
            return $this->handleEncaisserReservation($em, $payload, $arr, $totalSum, $totalMarge);

        } else if ($payload->get('checkout-validation') != null &&
            $this->isCsrfTokenValid('checkout-non-reservation', $payload->get('token'))){
            return $this->handleEncaisserNonReservation($em, $payload, $arr, $totalSum, $totalMarge);
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

    #[Route('/visualisation-reservation-passe', name: 'visualisation-reservation-passe.post', methods: ['POST'])]
    public function visualisationPostIndex(Request $request): Response {
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
            'reservation' => $this->reservationEncaisse->find($payload->get('gestion-visualisation-past'))
        ]);
    }

    #[Route('/visualisation-reservation-passe', name: 'visualisation-reservation-passe.get', methods: ['GET'])]
    public function visualisationGetIndex(): Response {
        return $this->generateError(406);
    }

    #[Route('/encaisser', name: 'encaisser.post', methods: ['POST'])]
    public function encaisserIndexPost(CarteRepository $carte, CategoryRepository $categoryRepository): Response {
        return $this->render('panel_reservation/encaisser.html.twig', [
            'carte'=>$carte->findAllOrderByCategorie($categoryRepository->findAllSortedById()),
        ]);
    }

    #[Route('/encaisser', name: 'encaisser.get', methods: ['GET'])]
    public function encaisserIndexGet(): Response {
        return $this->generateError(406);
    }

    private function renderPanelReservation(bool $deleted = false, bool $added = false): Response{
        return $this->render('panel_reservation/index.html.twig', [
            'added' => $added,
            'deleted' => $deleted,
            'reservations_futur' => $this->reservations->findByDateAfter(date('d-m-Y')),
            'reservations_du_jour' => $this->reservations->findTodaysReservations(),
            'reservations_passe' => $this->reservationEncaisse->reservationOrderedByDate(),
            'encaissement_passe' => $this->nonReservation->orderByDate(),
        ]);
    }

    #[Route('/encaisser-visualisation', name: 'encaisser-visualisation.post', methods: ['POST'])]
    public function encaisserVisualisationIndexPost(Request $request): Response {
        $payload = $request->getPayload();
        return $this->render('panel_reservation/visualisation_encaissement.html.twig', [
            'encaissement' => $this->nonReservation->find($payload->get('gestion-visualisation-encaissement'))
        ]);
    }

    #[Route('/creation-reservation', name: 'creation_reservation.get', methods: ['GET'])]
    public function creationReservationGet(): Response {
        return $this->generateError(406);
    }

    #[Route('/creation-reservation', name: 'creation_reservation.post', methods: ['POST'])]
    public function creationReservationPost(CalendarRepository $calendar): Response {
        return $this->render('panel_reservation/reservationCustom.html.twig', [
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'ouvert' => $calendar->findBy(['type' => 'ouvert'])
        ]);
    }

    private function handleEncaisserReservation(EntityManagerInterface $em, $payload, $articles, $totalSum, $totalMarge): Response{
        if($this->reservationEncaisse->reservationIDAlreadyExist($payload->get('checkout-validation'))){
            return $this->generateError(409);
        }

        $reservationSelected = $this->reservations->find($payload->get('checkout-validation'));
        $reservationEncaisse = new ReservationEncaisse();
        $reservationEncaisse->setIdReservation($payload->get('checkout-validation'))
            ->setDate(new \DateTime(null, new \DateTimeZone('Europe/Paris')))
            ->setArticles($articles)
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
            'articles' => $articles,
            'total' => $totalSum,
            'numeroReservation' => $payload->get('checkout-validation'),
        ]);
    }

    private function handleEncaisserNonReservation(EntityManagerInterface $em, $payload, $articles, $totalSum, $totalMarge): Response{
        $encaissement = new NonReservationEncaisse();
        $encaissement->setDate(new \DateTime(null, new \DateTimeZone('Europe/Paris')))
            ->setMidi($payload->get('select-value') == 'matin')
            ->setDate(new \DateTime(null, new \DateTimeZone('Europe/Paris')))
            ->setCouverts($payload->get('couverts'))
            ->setArticle($articles)
            ->setTotal($totalSum)
            ->setMarge($totalMarge);
        $em->persist($encaissement);
        $em->flush();
        return $this->render('panel_reservation/checkout_completed_non_reservation.html.twig', [
            'articles' => $articles,
            'total' => $totalSum,
            'encaissement' => $encaissement,
        ]);
    }

    private function createReservation(EntityManagerInterface $em, $payload){
        $date = (new \DateTime($payload->get('date'))) ->setTime(substr($payload->get('heure'),4,2), substr($payload->get('heure'),7,2));
        $newArticle = (new Reservation())
            ->setNom($payload->get('nom'))
            ->setPrenom($payload->get('prenom'))
            ->setCivilite($payload->get('civilite'))
            ->setEmail($payload->get('email'))
            ->setTelephone('+' . $payload->get('indicatif') . ' ' . $payload->get('numero'))
            ->setComments($payload->get('commentaires'))
            ->setHorraire($date)
            ->setEmplacement($payload->get('salle'))
            ->setDate($date)
            ->setCouverts($payload->get('couverts'));
        $em->persist($newArticle);
        $em->flush();
    }
}
