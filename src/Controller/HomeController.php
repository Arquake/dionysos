<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        //$employe = new User();
        //$employe -> setUsername('michel')->setRoles(['ROLE_SERVEUR'])->setPassword($hasher->hashPassword($employe,'0000'));
        //$em->persist($employe);
        //$em->flush();
        
        return $this->render('home/index.html.twig', [
        ]);
    }
}
