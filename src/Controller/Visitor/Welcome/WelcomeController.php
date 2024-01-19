<?php

namespace App\Controller\Visitor\Welcome;

use App\Entity\Search;
use App\Entity\MeetingRoom;
use App\Form\SearchFormType;
use App\Repository\MeetingRoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'visitor.welcome.index')]
    public function index(MeetingRoomRepository $meetingRoomRepository, Request $request): Response
    {

        $search = new Search();
        $search->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchFormType::class, $search);
        $form->handleRequest($request);
        $meetingrooms = $meetingRoomRepository->search($search);

        return $this->render('pages/visitor/welcome/index.html.twig', [
            'meetingrooms' => $meetingrooms,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/meetingroom/{id}/show', name: 'visitor.meetingroom.show', methods: ['GET'])]
    public function show(MeetingRoom $meetingRoom): Response
    {
        return $this->render('pages/visitor/welcome/show.html.twig', [

            'meetingroom' => $meetingRoom,
        ]);
    }
}
