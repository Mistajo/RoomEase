<?php

namespace App\Controller\Admin\Meeting_Room;

use App\Entity\MeetingRoom;
use App\Form\MeetingRoomFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class MeetingRoomController extends AbstractController
{
    #[Route('/meeting_room/list', name: 'admin.meetingroom.index')]
    public function index(MeetingRoomRepository $meetingRoomRepository): Response
    {
        $meetingrooms = $meetingRoomRepository->findAll();
        return $this->render('pages/admin/meeting_room/index.html.twig', [
            'meetingrooms' => $meetingrooms
        ]);
    }

    #[Route('/MeetingRoom/create', name: 'admin.meetingroom.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $meetingroom = new MeetingRoom();

        $form = $this->createForm(MeetingRoomFormType::class, $meetingroom);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($meetingroom);
            $em->flush();
            $this->addFlash('success', 'La salle a été ajoutée avec succès.');
            return $this->redirectToRoute('admin.meeting_room.index');
        }
        return $this->render("pages/admin/meeting_room/create.html.twig", [
            'form' => $form->createView(),
        ]);
    }
}
