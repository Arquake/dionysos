<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class PanelGestionController extends AbstractController
{
    #[Route('/panel-gestion', name: 'app_panel_gestion')]
    public function index(Request $request): Response
    {
        return $this->render('panel_gestion/index.html.twig', [
        ]);
    }
}
