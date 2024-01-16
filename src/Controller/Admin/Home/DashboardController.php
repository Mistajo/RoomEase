<?php

namespace App\Controller\Admin\Home;

use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/home/dashboard', name: 'admin.home.dashboard')]
    public function index(MeetingRoomRepository $meetingRoomRepository, ReservationRepository $reservationRepository, ChartBuilderInterface $chartBuilder): Response
    {
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






        return $this->render('pages/admin/home/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
