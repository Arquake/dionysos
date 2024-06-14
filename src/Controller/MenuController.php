<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Repository\CarteRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(CarteRepository $repository, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $plats = $repository->findAllOrderByCategorie($categoryRepository->findAllSortedById());

        return $this->render('menu/index.html.twig', [
            'plats' => $plats
        ]);
    }
}
