<?php

namespace App\Controller\User\Home;

use App\Entity\MeetingRoom;
use App\Repository\MeetingRoomRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'user.home.index')]
    public function index(MeetingRoomRepository $meetingRoomRepository): Response
    {
        $meetingrooms = $meetingRoomRepository->findAll();
        return $this->render(
            'pages/user/home/index.html.twig',
            [
                'meetingrooms' => $meetingrooms,
            ]
        );
    }

    #[Route('/meetingroom/{id}/show', name: 'user.meetingroom.show', methods: ['GET'])]
    public function show(MeetingRoom $meetingRoom): Response
    {
        return $this->render('pages/user/home/show.html.twig', [

            'meetingroom' => $meetingRoom,
        ]);
    }

    #[Route('/meetingroom/{id}/events', name: 'user.meetingroom.events', methods: ['GET'])]
    public function events(MeetingRoom $meetingRoom, Request $request): JsonResponse
    {
        // Récupération des réservations de la salle de réunion avec l'ID correspondant
        $reservations = $meetingRoom->getReservations();

        // Formatage des données en un tableau d'événements compréhensible par FullCalendar
        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'title' => $reservation->getTitle(),
                'start' => $reservation->getstartDate()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getendDate()->format('Y-m-d\TH:i:s')
            ];
        }

        return new JsonResponse($events);
    }
}
