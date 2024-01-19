<?php

namespace App\Controller\Admin\MeetingRoom;

use App\Entity\Search;
use App\Entity\MeetingRoom;
use App\Form\SearchFormType;
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
    public function index(MeetingRoomRepository $meetingRoomRepository, Request $request): Response
    {
        $search = new Search();
        $search->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchFormType::class, $search);
        $form->handleRequest($request);
        $meetingrooms = $meetingRoomRepository->search($search);

        return $this->render('pages/admin/meetingroom/index.html.twig', [
            'meetingrooms' => $meetingrooms,
            'form' => $form->createView(),
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
            return $this->redirectToRoute('admin.meetingroom.index');
        }
        return $this->render("pages/admin/meetingroom/create.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/meetingroom/{id}/show', name: 'admin.meetingroom.show', methods: ['GET'])]
    public function show(MeetingRoom $meetingRoom): Response
    {
        return $this->render('pages/admin/meetingroom/show.html.twig', [

            'meetingroom' => $meetingRoom,
        ]);
    }

    #[Route('/meetingroom/{id}/edit', name: 'admin.meetingroom.edit', methods: ['GET', 'PUT'])]
    public function edit(MeetingRoom $meetingRoom, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MeetingRoomFormType::class, $meetingRoom, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($meetingRoom);
            $em->flush();

            $this->addFlash("success", "La salle a été modifiée avec succès.");
            return $this->redirectToRoute('admin.meetingroom.index');
        }

        return $this->render("pages/admin/meetingroom/edit.html.twig", [
            "form" => $form->createView(),
            'meetingroom' => $meetingRoom,
        ]);
    }

    #[Route('/meetingroom/{id}/delete', name: 'admin.meetingroom.delete', methods: ['DELETE'])]
    public function delete(MeetingRoom $meetingRoom, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("delete_meetingroom_" . $meetingRoom->getId(), $request->request->get('csrf_token'))) {
            $em->remove($meetingRoom);
            $em->flush();

            $this->addFlash("success", "Cette salle a été supprimée.");
        }

        return $this->redirectToRoute('admin.meetingroom.index');
    }
}
