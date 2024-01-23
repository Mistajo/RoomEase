<?php

namespace App\Controller\Admin\Reservation;

use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class ReservationController extends AbstractController
{
    #[Route('/reservation/list', name: 'admin.reservation.index')]
    public function index(ReservationRepository $reservationRepository, MeetingRoomRepository $meetingRoomRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = $request->query->get('search');

        // Si un terme de recherche est saisi, filtrez la liste des utilisateurs
        if ($search) {
            $reservations = $reservationRepository->search($search, $request->query->getInt('page', 1));
        } else {
            $reservations = $reservationRepository->findBy([], ['createdAt' => 'DESC']);
            $reservations = $paginator->paginate($reservations, $request->query->getInt('page', 1), 6);
        }
        return $this->render('pages/admin/reservation/index.html.twig', [
            'reservations' => $reservations,
            'meetingRooms' => $meetingRoomRepository->findAll(),
        ]);
    }
}