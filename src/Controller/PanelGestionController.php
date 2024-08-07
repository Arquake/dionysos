<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Category;
use App\Entity\Carte;
use App\Repository\CalendarRepository;
use App\Repository\CarteRepository;
use App\Repository\CategoryRepository;
use App\Repository\NonReservationEncaisseRepository;
use App\Repository\ReservationEncaisseRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class PanelGestionController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em, CalendarRepository $calendar, ReservationRepository $reservation, NonReservationEncaisseRepository $nonReservationEncaisseRepository, ReservationEncaisseRepository $reservationEncaisseRepository){
        $res = $calendar->findBeforeToday();
        foreach ( $res as $value){
            $em->remove($value);
        }
        $res = $nonReservationEncaisseRepository->findOlderThanFourYears();
        foreach ( $res as $value){
            $em->remove($value);
        }
        $res = $reservationEncaisseRepository->findOlderThanFourYears();
        foreach ( $res as $value){
            $em->remove($value);
        }
        $res = $reservation->findAtLeastOneDayBeforeToday();
        foreach ( $res as $value){
            $em->remove($value);
        }
        $em->flush();
        $this->em = $em;
    }
    #[Route('/panel-gestion', name: 'app_panel_gestion', methods: ['GET'])]
    public function index(CalendarRepository $calendar, CategoryRepository $category, CarteRepository $carte): Response
    {
        
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => $category->findAll(),
            'ouvert' => $calendar->findBy(['type' => 'ouvert']),
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'articles' => $carte->findAll(),
            'error' => false,
        ]);
    }

    #[Route('/panel-gestion', name: 'app_panel_gestion.post', methods:['POST'])]
    public function indexPost(Request $request, CalendarRepository $calendar, CategoryRepository $category, CarteRepository $carte): Response
    {
        $payload = $request->getPayload();
        if($payload->get('gestion-submit-suppression') != null){
            if ($this->isCsrfTokenValid('supprimer-calendrier', $payload->get('token'))){
                if ($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]) != null){
                    $this->em->remove($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]));
                    $this->em->flush();
                }
            }

        } else if ($this->isCsrfTokenValid('gestion-articles', $payload->get('token'))) {
            if ($payload->get('gestion-article-prix') != null && $payload->get('article-prix') > 0) {
                $carte->find($payload->get('gestion-article-prix'))
                    ->setPrix($payload->get('article-prix'))
                    ->setMarge($payload->get('article-marge'));
                $this->em->flush();
    
    
            }else if ($payload->get('gestion-article-quantite') != null) {
                $carte->find($payload->get('gestion-article-quantite'))
                      ->setQuantite($payload->get('article-stock'));
                $this->em->flush();
    
            }else if ($payload->get('gestion-article-submit-suppression') != null) {
                $object = $carte->find($payload->get('gestion-article-submit-suppression'));
                if ($object != null) {
                    $this->em->remove($object);
                    $this->em->flush();
                }
    
            }

        } else if ($payload->get('gestion-submit') != null) {
            switch($payload->get('gestion-submit')){
                case 'ajoutArticle':
                    $this->ajouterArticle($payload);
                    break;

                case 'ajoutCategorie':
                    $this->ajouterCategorie($category, $payload);
                    break;

                case 'ouvert':
                    $this->ouvrir($calendar, $payload);
                    break;

                case 'fermer':
                    $this->fermer($calendar, $payload);
                    break;
            }
        }
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => $category->findAll(),
            'ouvert' => $calendar->findBy(['type' => 'ouvert']),
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'articles' => $carte->findAll(),
        ]);
    }


    private function ajouterCategorie(CategoryRepository $category, $payload): void{
        if ($this->isCsrfTokenValid('ajouter-categorie', $payload->get('token'))){
            if($category->findBy(['nom' => $payload->get('nom')]) == []){
                $newCategory = (new Category())
                    ->setNom($payload->get('nom'));
                $this->em->persist($newCategory);
                $this->em->flush();
            }
        }
    }

    private function ouvrir(CalendarRepository $calendar, $payload): void{
        if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){
            if(new DateTime($payload->get('date')) >= (new DateTime('now')) && 
            $calendar->findBy(['date' => new \DateTime($payload->get('date'))]) 
            == []){
                $dateOuverture = (new Calendar())
                    ->setDate(new \DateTime($payload->get('date')))
                    ->setType('ouvert');
                $this->em->persist($dateOuverture);
                $this->em->flush();
            }
        }
    }

    private function fermer(CalendarRepository $calendar, $payload): void{
        if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){
            if(new DateTime($payload->get('date')) >= (new DateTime('now')) && 
            $calendar->findBy(['date' => new \DateTime($payload->get('date'))]) 
            == []){
                $dateOuverture = (new Calendar())
                    ->setDate(new \DateTime($payload->get('date')))
                    ->setType('fermer');
                $this->em->persist($dateOuverture);
                $this->em->flush();
            }
        }
    }

    private function ajouterArticle($payload): void{
        if ($this->isCsrfTokenValid('ajouter-article', $payload->get('token'))){
            $newArticle = (new Carte())
                ->setNom($payload->get('nom'))
                ->setPrix($payload->get('prix'))
                ->setMarge($payload->get('marge'))
                ->setCategorie($payload->get('categorie'))
                ->setQuantite($payload->get('stock'));
            $this->em->persist($newArticle);
            $this->em->flush();
        }
    }
}
