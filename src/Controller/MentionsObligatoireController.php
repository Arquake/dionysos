<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionsObligatoireController extends AbstractController
{
    #[Route('/mentions-obligatoire', name: 'app_mentions_obligatoire')]
    public function index(): Response
    {
        return $this->render('mentions_obligatoire/index.html.twig', [
            'controller_name' => 'MentionsObligatoireController',
        ]);
    }
}
