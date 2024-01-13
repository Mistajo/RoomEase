<?php

namespace App\Controller\User\Reservation;

use App\Entity\Reservation;
use App\Service\MailerService;
use App\Form\ReservationDeleteFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

#[Route('/user')]
class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'user.reservation.index')]
    public function index(ReservationRepository $reservationRepository, MeetingRoomRepository $meetingRoomRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $reservation = $reservationRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );
        $reservations = $paginator->paginate(
            $reservation,

            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        return $this->render('pages/user/reservation/index.html.twig', [
            'reservations' => $reservations,
            'meetingRooms' => $meetingRoomRepository->findAll(),
        ]);
    }

    #[Route('/reservation/{id}/delete', name: 'user.reservation.delete')]
    public function delete(Reservation $reservation, Request $request, EntityManagerInterface $em, MailerService $mailer): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ReservationDeleteFormType::class, $reservation, [
            "method" => "PUT"
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setstatut('Annulé');
            $em->persist($reservation);
            $em->flush();
            $mailer->sendReservationCancellationConfirmation($reservation, $user);

            $this->addFlash('success', 'La Réservation a été annulée avec succès');

            return $this->redirectToRoute('user.reservation.index');
        }

        return $this->render('pages/user/reservation/delete.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
