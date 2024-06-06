<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation', methods: ['GET'])]
    public function index(Request $request, CalendarRepository $calendar): Response
    {
        $submittedToken = $request->getPayload()->get('token');
        
        if ($this->isCsrfTokenValid('make-reservation', $submittedToken)){

        }


        return $this->render('reservation/index.html.twig', [
            'vientDeReserver' => false,
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'ouvert' => $calendar->findBy(['type' => 'ouvert'])
        ]);
    }

    #[Route('/reservation', name: 'app_reservation.post', methods: ['POST'])]
    public function indexPost(Request $request, CalendarRepository $calendar, EntityManagerInterface $em): Response
    {
        $reservationReussi = false;
        $payload = $request->getPayload();
        $submittedToken = $payload->get('token');
        
        if ($this->isCsrfTokenValid('make-reservation', $submittedToken)){
            if($payload->get('cgu') == 'on' &&
            9 > $payload->get('couverts') &&
            $payload->get('couverts') >= 1 &&
            $payload->get('prenom') != '' &&
            $payload->get('nom') != '' &&
            $payload->get('indicatif') != '' &&
            $payload->get('numero') != '' &&
            filter_var($payload->get('email'), FILTER_VALIDATE_EMAIL) &&
            $payload->get('confirmer')
            ){
                $this->createReservation($em, $payload);
                $reservationReussi = true;
            }
        }


        return $this->render('reservation/index.html.twig', [
            'vientDeReserver' => true,
            'reservationReussi' => $reservationReussi,
            'fermer' => $calendar->findBy(['type' => 'fermer']),
            'ouvert' => $calendar->findBy(['type' => 'ouvert'])
        ]);
    }

    private function createReservation(EntityManagerInterface $em, $payload){
        $date = (new \DateTime($payload->get('date'))) ->setTime(substr($payload->get('heure'),4,2), substr($payload->get('heure'),7,2));
        $newArticle = (new Reservation())
                ->setNom($payload->get('nom'))
                ->setPrenom($payload->get('prenom'))
                ->setCivilite($payload->get('civilite'))
                ->setEmail($payload->get('email'))
                ->setTelephone('+' . $payload->get('indicatif') . ' ' . $payload->get('numero'))
                ->setComments($payload->get('commentaires'))
                ->setHorraire($date)
                ->setEmplacement($payload->get('salle'))
                ->setDate($date)
                ->setCouverts($payload->get('couverts'));
        $em->persist($newArticle);
        $em->flush();
    }
}
