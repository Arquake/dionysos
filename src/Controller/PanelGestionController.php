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

        $session = $request->getSession();

        $poste = $session->get('poste');

        $validConnexion = false;

        if(($request->isMethod('GET') && $poste == null) || ($request->isMethod('POST') && !$validConnexion)){
            return $this->redirectToRoute('app_login',['error' => true]);
        }
        

        return $this->render('panel_gestion/index.html.twig', [
        ]);
    }
}
