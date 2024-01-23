<?php

namespace App\Controller\Admin\Home;

use App\Entity\Statistic;
use App\Service\StatisticService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use App\Repository\StatisticRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/home/dashboard', name: 'admin.home.dashboard')]

    public function index(MeetingRoomRepository $meetingRoomRepository, UserRepository $userRepository, ReservationRepository $reservationRepository, ChartBuilderInterface $chartBuilder, StatisticService $statisticService, StatisticRepository $statisticRepository): Response
    {

        $statistics = $statisticRepository->findAll();
        $reservations = $reservationRepository->findAll();
        $meetingRooms = $meetingRoomRepository->findAll();
        $users = $userRepository->findAll();

        // Nombre de réservations par utilisateur
        $userLabels = [];
        $userReservations = [];

        foreach ($users as $user) {
            $userLabels[] = $user->getLastName();
            $userReservations[] = 0;
        }

        foreach ($userReservations as $key => $userReservation) {
            foreach ($reservations as $reservation) {
                if ($reservation->getUser() === $users[$key]) {
                    $userReservations[$key]++;
                }
            }
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $userLabels,
            'datasets' => [
                [
                    'label' => 'Nombre de réservations par utilisateur',
                    'backgroundColor' => '#79f8f8',
                    'data' => $userReservations,
                ],
            ],
        ]);

        //Nombre de réservations par salle
        $labels2 = [];
        $datas2 = [];

        foreach ($meetingRooms as $meetingRoom) {
            $labels2[] = $meetingRoom->getName();
            $datas2[] = 0;
        }

        foreach ($datas2 as $key => $data2) {
            foreach ($reservations as $reservation) {
                if ($reservation->getMeetingroom() === $meetingRooms[$key]) {
                    $datas2[$key]++;
                }
            }
        }

        $chart2 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart2->setData([
            'labels' => $labels2,
            'datasets' => [
                [
                    'label' => 'Nombre de réservations par salle',
                    'backgroundColor' => '#F7931E',
                    'data' => $datas2,
                ],
            ],
        ]);

        // Montant total des réservations

        $stats = [
            'total' => 0,
            'paid' => 0,
            'unpaid' => 0,
        ];

        foreach ($reservations as $reservation) {
            $stats['total'] += $reservation->getTotalPrice();
            if ($reservation->getPaymentStatus() == 'Payée') {
                $stats['paid'] += $reservation->getTotalPrice();
            } else {
                $stats['unpaid'] += $reservation->getTotalPrice();
            }
        }
        $chart3 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart3->setData([
            'labels' => ['Payée', 'Non payée'],
            'datasets' => [
                [
                    'label' => 'Montants en €',
                    'backgroundColor' => ['#00ff00', '#ff0000'],
                    'data' => [$stats['paid'], $stats['unpaid']],
                ],
            ],
        ]);


        //capacité par salle
        $salleLabels = [];
        $salleCapacites = [];

        foreach ($meetingRooms as $meetingroom) {
            $salleLabels[] = $meetingroom->getName();
            $salleCapacites[] = $meetingroom->getCapacity();
        }

        $chart4 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart4->setData([
            'labels' => $salleLabels,
            'datasets' => [
                [
                    'label' => 'Capacité par salle',
                    'backgroundColor' => '#f879f8',
                    'data' => $salleCapacites,
                ],
            ],
        ]);



        return $this->render('pages/admin/home/index.html.twig', [
            'chart' => $chart,
            'chart2' => $chart2,
            'chart3' => $chart3,
            'chart4' => $chart4,
        ]);
    }
}
