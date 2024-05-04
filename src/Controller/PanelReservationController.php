<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class PanelReservationController extends AbstractController
{
    #[Route('/panel-reservation', name: 'app_panel_reservation')]
    public function index(): Response
    {
        return $this->render('panel_reservation/index.html.twig', [
            
        ]);
    }
}
