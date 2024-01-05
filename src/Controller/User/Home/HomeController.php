<?php

namespace App\Controller\User\Home;

use App\Entity\MeetingRoom;
use App\Repository\MeetingRoomRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
