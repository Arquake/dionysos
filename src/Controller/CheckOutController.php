<?php

namespace App\Controller;

use App\Repository\CarteRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SERVEUR")'))]
class CheckOutController extends AbstractController
{
    #[Route('/checkout', name: 'app_check_out.post', methods: ['POST'])]
    public function indexPost(Request $request, ReservationRepository $reservationRepository, CarteRepository $carte): Response
    {
        $payload = $request->getPayload();

        if(!$this->isCsrfTokenValid('checkout-gestion-reservation', $payload->get('token'))){
            return $this->render('logged_error_code/error406.html.twig', [
                'message' => 'Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>'
            ]);
        }

        $reservation = $reservationRepository->find($payload->get('gestion-reservation-checkout-id'));

        if($reservation == null) {
            return $this->render('logged_error_code/error400.html.twig', [
                'message' => 'Aucune Réservations n\'a été trouvé avec l\'identifiant fournit<br><br>Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>',
            ]);
        }

        return $this->render('check_out/index.html.twig', [
            'reservation'=>$reservation,
            'carte'=>$carte->findAllOrderByCategorie(),
        ]);

    }

    #[Route('/checkout', name: 'app_check_out.get', methods: ['GET'])]
    public function indexGet(): Response
    {
        return $this->render('logged_error_code/error405.html.twig', [
            'message' => 'Veuillez vérifier que vous accédez bien au service depuis l\'onglet "<span class="font-bold italic underline">Réservations</span>"<br><br>'
        ]);
    }
}
