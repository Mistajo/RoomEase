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
        $form = $this->createForm(SearchFormType::class, $search);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis ou non
        if ($form->isSubmitted() && $form->isValid()) {
            $meetingrooms = $meetingRoomRepository->Search($search);
        } else {
            // Pas de formulaire soumis - récupérer toutes les salles
            $meetingrooms = $meetingRoomRepository->findAll();
        }
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