<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Repository\CarteRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(CarteRepository $repository, EntityManagerInterface $em): Response
    {
        $plats = $repository->findAllOrderByCategorie();

        /*$plat = new Carte();
        $plat   -> setNom("Bouteille d'eau")
                -> setPrix(1.20)
                -> setQuantite(4)
                -> setCategorie('Boissons');

        $em->persist($plat);
        $em->flush();

        $em->remove($plats[0]);
        $em->flush();*/

        return $this->render('menu/index.html.twig', [
            'plats' => $plats
        ]);
    }
}
