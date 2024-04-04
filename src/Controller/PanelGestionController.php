<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PanelGestionController extends AbstractController
{
    #[Route('/panel-gestion', name: 'app_panel_gestion')]
    public function index(Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('panel_gestion/index.html.twig', [
        ]);
    }
}
