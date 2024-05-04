<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, CalendarRepository $calendar): Response
    {
        $submittedToken = $request->getPayload()->get('token');
        
        if ($this->isCsrfTokenValid('make-reservation', $submittedToken)){

        }


        return $this->render('reservation/index.html.twig', [
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'ouvert' => $calendar->findBy(['type' => 'ouvert'])
        ]);
    }
}
