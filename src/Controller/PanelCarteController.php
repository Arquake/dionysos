<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class PanelCarteController extends AbstractController
{
    #[Route('/panel-carte', name: 'app_panel_carte')]
    public function index(): Response
    {
        return $this->render('panel_carte/index.html.twig', [
            'controller_name' => 'PanelCarteController',
        ]);
    }
}
