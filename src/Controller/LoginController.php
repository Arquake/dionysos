<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    #[Route('/loger', name: 'app_loger')]
    public function index(Request $request): Response
    {
        $error=$request->query->get('error');

        return $this->render('login/index.html.twig', [
            'error' => $error == 1
        ]);
    }
}
