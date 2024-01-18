<?php

namespace App\Controller\Admin\Home;

use App\Entity\Statistic;
use App\Service\StatisticService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use App\Repository\StatisticRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/home/dashboard', name: 'admin.home.dashboard')]

    public function index(MeetingRoomRepository $meetingRoomRepository, ReservationRepository $reservationRepository, ChartBuilderInterface $chartBuilder, StatisticService $statisticService, StatisticRepository $statisticRepository): Response
    {


        $statistics = $statisticRepository->findAll();
        $reservations = $reservationRepository->findAll();
        $meetingRooms = $meetingRoomRepository->findAll();

        $labels = [];
        $data = [];

        foreach ($reservations as $reservation) {
            $labels[] = $reservation->getStartDate()->format('Y-m-d');
            $data[] = $reservation->getMeetingRoom()->getCapacity();
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Reservations',
                    'backgroundColor' => '#79f879',
                    'data' => $data,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);



        $labels1 = [];
        $values1 = [];

        foreach ($statistics as $statistic) {

            $meetingroomEntity = $statistic->getMeetingroom();
            $labels1[] = $meetingroomEntity->getName();
            $values1[] = $statistic->getCount();
        }

        // Construction du graphique
        $chart2 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart2->setData([
            'labels' => $labels1,
            'datasets' => [
                [
                    'label' => 'Nombre de réservations',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                    'data' => $values1,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);



        return $this->render('pages/admin/home/index.html.twig', [
            'chart' => $chart,
            'chart2' => $chart2,
        ]);
    }
}
