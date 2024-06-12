<?php

namespace App\Controller;

use App\Repository\NonReservationEncaisseRepository;
use App\Repository\ReservationEncaisseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[IsGranted("ROLE_ADMIN")]
class PanelFinanceController extends AbstractController
{
    private array $months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    private array $days = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
    private int $currentMonth;
    private string $currentYear;
    private ChartBuilderInterface $chartBuilder;
    private ReservationEncaisseRepository $reservations;
    private NonReservationEncaisseRepository $nonReservationEncaisses;

    public function __construct(ChartBuilderInterface $chartBuilder, ReservationEncaisseRepository $reservations, NonReservationEncaisseRepository $nonReservationEncaisses){
        $this->currentMonth = (int) (new \DateTime(null, new \DateTimeZone('Europe/Paris')))->format('m');
        $this->currentYear = (new \DateTime(null, new \DateTimeZone('Europe/Paris')))->format('Y');

        $offset = $this->currentMonth % count($this->months);
        // Split the array into two parts
        $part1 = array_slice($this->months, $offset);
        $part2 = array_slice($this->months, 0, $offset);

        // Merge the two parts
        $this -> months = array_merge($part1, $part2);

        $this->reservations = $reservations;
        $this->chartBuilder = $chartBuilder;
        $this->nonReservationEncaisses = $nonReservationEncaisses;
    }

    #[Route('/panel-finance', name: 'app_panel_finance')]
    public function index(): Response
    {
        return $this->render('panel_finance/index.html.twig', [
            'revenu_reservation_chart' => $this->generateReservationRevenuChart(),
            'couverts_reservation_chart' => $this->generateReservationCouvertsChart(),
            'revenu_encaissement_chart' => $this->generateEncaissementRevenuChart(),
            'couverts_encaissement_chart' => $this->generateEncaissementCouvertsChart(),
        ]);
    }


    private function sumPrixOfArrays($array){
        $sum = 0;
        foreach($array as $value){
            $sum += $value->getTotal();
        }
        return $sum;
    }

    private function sumMargeOfArray($array){
        $sum = 0;
        foreach($array as $value){
            $sum += $value->getMarge();
        }
        return $sum;
    }

    private function generateReservationRevenuChart(){
        $revenuMensuel = [];
        $margeBrut = [];
        $yearPassed = false;

        for($i = 1 ; $i < 13; $i++){
            if($yearPassed){
                $items = $this->reservations->findReservationsByMonth($this->currentYear,($this->currentMonth+$i)%12);
                $revenuMensuel[] = $this->sumPrixOfArrays($items);
                $margeBrut[] = $this->sumMargeOfArray($items);
            } else {
                if (($i + $this->currentMonth)%12 == 0 ) {
                    $yearPassed = true;
                }
                $items = $this->reservations->findReservationsByMonth((int)$this->currentYear + 1,$this->currentMonth+$i);
                $revenuMensuel[] = $this->sumPrixOfArrays($items);
                $margeBrut[] = $this->sumMargeOfArray($items);
            }
        }

        $revenuChart = $this->chartBuilder->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => $this->months,
                'datasets' => [
                    [
                        'label' => 'revenu mensuel (en €)',
                        'backgroundColor' => 'rgb(255, 99, 132, .4)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $revenuMensuel,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'marge brut (en €)',
                        'backgroundColor' => 'rgb(95, 69, 255, .4)',
                        'borderColor' => 'rgb(95, 69, 255)',
                        'data' => $margeBrut,
                        'tension' => 0.4,
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
            ]);

        return $revenuChart;
    }

    private function generateReservationCouvertsChart(){
        $couvertsChart = $this->chartBuilder->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => $this->days,
                'datasets' => [
                    [
                        'label' => 'midi',
                        'backgroundColor' => 'rgb(255, 99, 132, .4)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $this->reservations->averageMorningCovers(),
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'dîner',
                        'backgroundColor' => 'rgb(95, 69, 255, .4)',
                        'borderColor' => 'rgb(95, 69, 255)',
                        'data' => $this->reservations->averageEveningCovers(),
                        'tension' => 0.4,
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
            ]);

        return $couvertsChart;
    }

    private function generateEncaissementRevenuChart(){
        $revenuMensuel = [];
        $margeBrut = [];
        $yearPassed = false;

        for($i = 1 ; $i < 13; $i++){
            if($yearPassed){
                $items = $this->nonReservationEncaisses->findEncaissementsByMonth($this->currentYear,($this->currentMonth+$i)%12);
                $revenuMensuel[] = $this->sumPrixOfArrays($items);
                $margeBrut[] = $this->sumMargeOfArray($items);
            } else {
                if (($i + $this->currentMonth)%12 == 0 ) {
                    $yearPassed = true;
                }
                $items = $this->nonReservationEncaisses->findEncaissementsByMonth((int)$this->currentYear + 1,$this->currentMonth+$i);
                $revenuMensuel[] = $this->sumPrixOfArrays($items);
                $margeBrut[] = $this->sumMargeOfArray($items);
            }
        }

        $revenuChart = $this->chartBuilder->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => $this->months,
                'datasets' => [
                    [
                        'label' => 'revenu mensuel (en €)',
                        'backgroundColor' => 'rgb(255, 99, 132, .4)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $revenuMensuel,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'marge brut (en €)',
                        'backgroundColor' => 'rgb(95, 69, 255, .4)',
                        'borderColor' => 'rgb(95, 69, 255)',
                        'data' => $margeBrut,
                        'tension' => 0.4,
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
            ]);

        return $revenuChart;
    }

    private function generateEncaissementCouvertsChart(){
        $couvertsChart = $this->chartBuilder->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => $this->days,
                'datasets' => [
                    [
                        'label' => 'midi',
                        'backgroundColor' => 'rgb(255, 99, 132, .4)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $this->nonReservationEncaisses->averageMorningCovers(),
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'dîner',
                        'backgroundColor' => 'rgb(95, 69, 255, .4)',
                        'borderColor' => 'rgb(95, 69, 255)',
                        'data' => $this->nonReservationEncaisses->averageEveningCovers(),
                        'tension' => 0.4,
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
            ]);

        return $couvertsChart;
    }
}
