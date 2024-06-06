<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class CheckOutController extends AbstractController
{
    #[Route('/checkout', name: 'app_check_out')]
    public function index(Request $request, ReservationRepository $reservationRepository): Response
    {
        $payload = $request->getPayload();

        $reservation = $reservationRepository->find($payload->get('gestion-reservation-checkout-id'));


        if($this->isCsrfTokenValid('checkout-gestion-reservation', $payload->get('token'))){
            return $this->render('check_out/index.html.twig', [
                'notFound'=> false,
                'controller_name' => 'CheckOutController',
                'reservation'=>$reservation,
            ]);
        } else {
            return $this->render('check_out/index.html.twig', [
                'notFound'=> true,
            ]);
        }
    }
}
