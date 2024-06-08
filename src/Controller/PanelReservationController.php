<?php

namespace App\Controller;

use App\Repository\CarteRepository;
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
    public function index(ReservationRepository $reservations): Response
    {
        return $this->render('panel_reservation/index.html.twig', [
            'reservations_futur' => $reservations->findByDateAfter('30-05-2024'),
        ]);
    }

    #[Route('/panel-reservation', name: 'app_panel_reservation.post', methods: ['POST'])]
    public function postIndex(Request $request, EntityManagerInterface $em, ReservationRepository $reservations, CarteRepository $carte): Response
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
                    'reservations_futur' => $reservations->findByDateAfter('30-05-2024'),
                ]);
            }
            return $this->render('logged_error_code/error400.html.twig', [
            'message' =>
                'Aucune Réservations n\'a été trouvé avec l\'identifiant fournit<br><br>Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>',
            ]);
        } else if ($payload->get('checkout-validation') != null &&
            $this->isCsrfTokenValid('checkout-reservation', $payload->get('token'))){
            $arr = [];
            $totalSum = 0;
            foreach ( $payload as $key => $value) {
                if(filter_var($key, FILTER_VALIDATE_INT) !== false){
                    $arr[$value] = $carte->find($key)->getPrix();
                    $totalSum += ($arr[$value] * $value);
                }
            }
            return $this->render('panel_reservation/checkout_completed.html.twig', [
                'articles' => '',
                'total' => ''
            ]);

        }

        // invalid csrf token / aucun bouton n'est cliqué une erreur est généré
        return $this->render('logged_error_code/error406.html.twig', [
            'message' => ''
        ]);
    }
}
