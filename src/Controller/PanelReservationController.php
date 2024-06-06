<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
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
    public function postIndex(Request $request, ReservationRepository $reservations): Response
    {

        $payload = $request->getPayload();

        if( $this->isCsrfTokenValid('gestion-articles', $payload->get('token')) &&
            $payload->get('gestion-reservation-submit-suppression') != null)
        {
                return $this->render('panel_reservation/index.html.twig', [
                    'error' => false,
                    'deleted' => true,
                    'reservations_futur' => $reservations->findByDateAfter('30-05-2024'),
                ]);
        }

        $payload = null;

        return $this->render('panel_reservation/index.html.twig', [
            'error' => true,
            'deleted' => false,
            'reservations_futur' => $reservations->findByDateAfter('30-05-2024'),
        ]);
    }
}
