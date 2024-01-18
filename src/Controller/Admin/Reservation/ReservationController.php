<?php

namespace App\Controller\Admin\Reservation;

use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ReservationController extends AbstractController
{
    #[Route('/reservation/list', name: 'admin.reservation.index')]
    public function index(ReservationRepository $reservationRepository, MeetingRoomRepository $meetingRoomRepository): Response
    {
        return $this->render('pages/admin/reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
            'meetingRooms' => $meetingRoomRepository->findAll(),
        ]);
    }
}
