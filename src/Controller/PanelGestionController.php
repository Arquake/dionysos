<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Category;
use App\Entity\Carte;
use App\Repository\CalendarRepository;
use App\Repository\CarteRepository;
use App\Repository\CategoryRepository;
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
    public function indexPost(Request $request, EntityManagerInterface $em, CalendarRepository $calendar, CategoryRepository $category, CarteRepository $carte): Response
    {
        $errorHappend = false;
        $payload = $request->getPayload();
        if($payload->get('gestion-submit-suppression') != null){
            if ($this->isCsrfTokenValid('supprimer-calendrier', $payload->get('token'))){
                if ($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]) != null){
                    $em->remove($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]));
                    $em->flush();
                }
            }

        } else if ($payload->get('gestion-article-prix') != null && $payload->get('article-prix') > 0) {
            $carte->find($payload->get('gestion-article-prix'))
                        ->setPrix($payload->get('article-prix'));
            $em->flush();


        }else if ($payload->get('gestion-article-submit-suppression') != null) {
            $object = $carte->find($payload->get('gestion-article-submit-suppression'));
            if ($object != null) {
                $em->remove($object);
                $em->flush();
            }

        } else if ($payload->get('gestion-submit') != null){
            switch($payload->get('gestion-submit')){
                case 'ajoutArticle':
                    $this->ajouterArticle($em, $payload);
                    break;

                case 'ajoutCategorie':
                    $this->ajouterCategorie($em, $category, $payload);
                    break;

                case 'ouvert':
                    $this->ouvrir($em, $calendar, $payload);
                    break;

                case 'fermer':
                    $this->fermer($em, $calendar, $payload);
                    break;
            }
        }
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => $category->findAll(),
            'ouvert' => $calendar->findBy(['type' => 'ouvert']),
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'articles' => $carte->findAll(),
            'error' => $errorHappend,
        ]);
    }



    private function ajouterCategorie(EntityManagerInterface $em, CategoryRepository $category, $payload){
        if ($this->isCsrfTokenValid('ajouter-categorie', $payload->get('token'))){
            if($category->findBy(['nom' => $payload->get('nom')]) == []){
                $newCategory = (new Category())
                    ->setNom($payload->get('nom'));
                $em->persist($newCategory);
                $em->flush();
            }
        }
    }


    private function ouvrir(EntityManagerInterface $em,  CalendarRepository $calendar, $payload){
        if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){
            if(new DateTime($payload->get('date')) >= (new DateTime('now')) && 
            $calendar->findBy(['date' => new \DateTime($payload->get('date'))]) 
            == []){
                $dateOuverture = (new Calendar())
                    ->setDate(new \DateTime($payload->get('date')))
                    ->setType('ouvert');
                $em->persist($dateOuverture);
                $em->flush();
            }
        }
    }


    private function fermer(EntityManagerInterface $em,  CalendarRepository $calendar, $payload){
        if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){
            if(new DateTime($payload->get('date')) >= (new DateTime('now')) && 
            $calendar->findBy(['date' => new \DateTime($payload->get('date'))]) 
            == []){
                $dateOuverture = (new Calendar())
                    ->setDate(new \DateTime($payload->get('date')))
                    ->setType('fermer');
                $em->persist($dateOuverture);
                $em->flush();
            }
        }
    }


    private function ajouterArticle(EntityManagerInterface $em, $payload){
        if ($this->isCsrfTokenValid('ajouter-article', $payload->get('token'))){
            $newArticle = (new Carte())
                ->setNom($payload->get('nom'))
                ->setPrix($payload->get('prix'))
                ->setCategorie($payload->get('categorie'))
                ->setQuantite($payload->get('stock'));
            $em->persist($newArticle);
            $em->flush();
        }
    }
}
