<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class PanelGestionController extends AbstractController
{
    #[Route('/panel-gestion', name: 'app_panel_gestion', methods: ['GET'])]
    public function index(Request $request): Response
    {
        
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => ['boissons','vins rouges', "vins rosés"],
            'ouvert' => ['2023-01-12','2023-04-14','2023-04-14','2023-04-14','2023-04-14','2023-04-14'],
            'fermer' => ['2023-01-06','2023-04-04']
        ]);
    }

    #[Route('/panel-gestion', name: 'app_panel_gestion.post', methods:['POST'])]
    public function indexPost(Request $request, EntityManager $em, CalendarRepository $calendar): Response
    {
        $payload = $request->getPayload();
        if($payload->get('gestion-submit-suppression') != null){

        } else if ($payload->get('gestion-submit') != null){
            switch($payload->get('gestion-submit')){
                case 'ajoutArticle':
                    if ($this->isCsrfTokenValid('ajouter-article', $payload->get('token'))){
                    
                    }
                    break;
                case 'ajoutCategorie':
                    if ($this->isCsrfTokenValid('ajouter-categorie', $payload->get('token'))){

                    }
                    break;
                case 'ouvert':
                    if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){
                        if(new DateTime($payload->get('date')) >= (new DateTime('now')) && $calendar->findOneBy($payload->get('date')) == null){
                            $dateOuverture = (new Calendar())
                                ->setDate($payload->get('date'))
                                ->setType('ouvert');
                            $em->persist();
                        }
                    }
                    break;
                case 'fermer':
                    if ($this->isCsrfTokenValid('modifier-calendrier', $payload->get('token'))){

                    }
                    break;
            }
        }
        
        return $this->render('panel_gestion/index.html.twig', [
            'categories' => ['boissons','vins rouges', "vins rosés"],
            'ouvert' => $calendar->findAll(['type' => 'ouvert']),
            'fermer' => $calendar->findAll(['type' => 'fermer'])
        ]);
    }

    private function ajouterArticle(){

    }
}
