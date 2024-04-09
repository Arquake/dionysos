<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class PanelConnectedController extends AbstractController
{
    #[Route('/panel-connected', name: 'app_panel_connected')]
    public function index(): Response
    {
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->render('panel_gestion/index.html.twig');
        }
        return $this->render('panel_gestion/index.html.twig');
    }
}
