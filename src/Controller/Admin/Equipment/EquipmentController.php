<?php

namespace App\Controller\Admin\Equipment;

use App\Entity\Equipment;
use App\Entity\Equipments;
use App\Entity\MeetingRoom;
use App\Form\EquipmentFormType;
use App\Form\MeetingRoomFormType;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class EquipmentController extends AbstractController
{
    #[Route('/equipments/list', name: 'admin.equipments.index')]
    public function index(EquipmentRepository $equipmentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = $request->query->get('search');

        // Si un terme de recherche est saisi, filtrez la liste des utilisateurs
        if ($search) {
            $equipments = $equipmentRepository->findBySearchTerm($search, $request->query->getInt('page', 1));
        } else {
            $equipments = $equipmentRepository->findBy([], ['createdAt' => 'DESC']);
            $equipments = $paginator->paginate($equipments, $request->query->getInt('page', 1), 6);
        }


        return $this->render('pages/admin/equipment/index.html.twig', [
            'equipments' => $equipments,
        ]);
    }

    #[Route('/equipments/create', name: 'admin.equipments.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentFormType::class, $equipment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($equipment);
            $em->flush();
            $this->addFlash('success', "L'equipement a été ajouté avec succès.");
            return $this->redirectToRoute('admin.equipments.index');
        }
        return $this->render("pages/admin/equipment/create.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/equipments/{id}/edit', name: 'admin.equipments.edit', methods: ['GET', 'PUT'])]
    public function edit(Equipment $equipment, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EquipmentFormType::class, $equipment, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($equipment);
            $em->flush();

            $this->addFlash("success", "L'equipement a été modifié avec succès.");
            return $this->redirectToRoute('admin.equipments.index');
        }

        return $this->render("pages/admin/equipment/edit.html.twig", [
            "form" => $form->createView(),
            'equipment' => $equipment,
        ]);
    }

    #[Route('/equipments/{id}/delete', name: 'admin.equipments.delete', methods: ['DELETE'])]
    public function delete(Equipment $equipment, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("delete_equipment_" . $equipment->getId(), $request->request->get('csrf_token'))) {
            $em->remove($equipment);
            $em->flush();

            $this->addFlash("success", "Cet equipement a été supprimé.");
        }

        return $this->redirectToRoute('admin.equipments.index');
    }
}
