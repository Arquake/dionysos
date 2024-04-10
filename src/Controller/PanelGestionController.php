<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class PanelGestionController extends AbstractController
{
    #[Route('/panel-gestion', name: 'app_panel_gestion')]
    public function index(Request $request): Response
    {
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => ['boissons','vins rouges', "vins rosés"],
            'ouvert' => ['2023-01-12','2023-04-14','2023-04-14','2023-04-14','2023-04-14','2023-04-14'],
            'fermer' => ['2023-01-06','2023-04-04']
        ]);
    }
}
