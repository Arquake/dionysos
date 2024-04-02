<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request): Response
    {
        $submittedToken = $request->getPayload()->get('token');
        
        if ($this->isCsrfTokenValid('make-reservation', $submittedToken)){

        }


        return $this->render('reservation/index.html.twig', [
        ]);
    }
}
