<?php

namespace App\Controller\Admin\Reservation;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/réservation/{id}/delete', name: 'admin.reservation.delete', methods: ['DELETE'])]
    public function delete(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        // si le token de sécurité est valide
        if ($this->isCsrfTokenValid('delete-reservation-' . $reservation->getId(), $request->request->get('csrf_token'))) {
            // on prepare la requete de suppression d'une réservation
            $em->remove($reservation);
            //  on envoie la requete
            $em->flush();
            // on retourne un message de success
            $this->addFlash('success', 'La réservation a été supprimée avec succès');
        }
        // on redirige vers la page des réservations
        return $this->redirectToRoute('admin.reservation.index');
    }
}
