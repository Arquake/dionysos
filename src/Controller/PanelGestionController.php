<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
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
    public function index(Request $request, CalendarRepository $calendar): Response
    {
        
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => ['boissons','vins rouges', "vins rosés"],
            'ouvert' => $calendar->findBy(['type' => 'ouvert']),
            'fermer' => $calendar->findBy(['type' => 'fermer'])
        ]);
    }

    #[Route('/panel-gestion', name: 'app_panel_gestion.post', methods:['POST'])]
    public function indexPost(Request $request, EntityManagerInterface $em, CalendarRepository $calendar): Response
    {
        $payload = $request->getPayload();
        if($payload->get('gestion-submit-suppression') != null){
            if ($this->isCsrfTokenValid('supprimer-calendrier', $payload->get('token'))){
                if ($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]) != null){
                    $em->remove($calendar->findOneBy(['date' => new \DateTime($payload->get('gestion-submit-suppression'))]));
                    $em->flush();
                }
            }

        } else if ($payload->get('gestion-submit') != null){
            switch($payload->get('gestion-submit')){
                case 'ajoutArticle':
                    if ($this->isCsrfTokenValid('ajouter-article', $payload->get('token'))){
                    
                    }
                    break;

                case 'ajoutCategorie':
                    if ($this->isCsrfTokenValid('ajouter-categorie', $payload->get('token'))){
                        if($calendar->findBy(['nom' => $payload->get('nom')]) == []){
                            $dateOuverture = (new Calendar())
                                ->setDate(new \DateTime($payload->get('date')))
                                ->setType('ouvert');
                            $em->persist($dateOuverture);
                            $em->flush();
                        }
                    }
                    break;

                case 'ouvert':
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
                    break;

                case 'fermer':
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
                    break;
            }
        }
        
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => ['boissons','vins rouges', "vins rosés"],
            'ouvert' => $calendar->findBy(['type' => 'ouvert']),
            'fermer' => $calendar->findBy(['type' => 'fermer'])
        ]);
    }
}
