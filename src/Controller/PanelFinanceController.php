<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted("ROLE_ADMIN")]
class PanelFinanceController extends AbstractController
{
    #[Route('/panel-finance', name: 'app_panel_finance')]
    public function index(): Response
    {
        return $this->render('panel_finance/index.html.twig', [
            'controller_name' => 'PanelFinanceController',
        ]);
    }
}
