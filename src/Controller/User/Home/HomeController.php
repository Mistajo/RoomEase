<?php

namespace App\Controller\User\Home;

use App\Entity\Search;
use App\Entity\MeetingRoom;
use App\Entity\Reservation;
use App\Form\SearchFormType;
use App\Service\MailerService;
use App\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRoomRepository;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'user.home.index')]
    public function index(MeetingRoomRepository $meetingRoomRepository, ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = new Search();
        $search->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchFormType::class, $search);
        $form->handleRequest($request);
        $meetingRooms = $meetingRoomRepository->search($search);

        // // Paginer les salles de réunion
        // $pagination = $paginator->paginate(
        //     $meetingRoomRepository->findAll(), // Récupérer toutes les salles
        //     $request->query->getInt('page', 1), // Numéro de page - par défaut la première page
        //     6 // Nombre d'éléments par page
        // );

        // // Vérifier si le formulaire a été soumis ou non
        // if ($form->isSubmitted() && $form->isValid()) {
        //     // Paginer les salles de réunion trouvées avec la recherche
        //     $pagination = $paginator->paginate(
        //         $meetingRoomRepository->search($search), // Récupérer les salles correspondantes à la recherche
        //         $request->query->getInt('page', 1), // Numéro de page - par défaut la première page
        //         5 // Nombre d'éléments par page
        //     );
        // }

        return $this->render(
            'pages/user/home/index.html.twig',
            [
                'meetingRooms' => $meetingRooms,
                'form' => $form->createView(),


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
                'title' => 'Reservé',
                'start' => $reservation->getstartDate()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getendDate()->format('Y-m-d\TH:i:s'),
                'backgroundColor' => 'red',
                'borderColor' => 'red',

            ];
        }

        return new JsonResponse($events);
    }

    #[Route('/meetingroom/{id}/reservation', name: 'user.meetingroom.reservation')]
    public function reservation(MeetingRoom $meetingroom, Request $request, EntityManagerInterface $em, MailerService $mailer): Response
    {

        // Création d'une nouvelle réservation
        $user = $this->getUser();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $reservation->getStartDate();
            $endDate = $reservation->getEndDate();
            $meetingRoomId = $meetingroom->getId();
            $reservation->setUser($this->getUser());
            $reservation->setMeetingRoom($meetingroom);
            $reservation->setStatut('Reservé');

            $lastReservation = $em->getRepository(Reservation::class)
                ->findOneBy(['meetingroom' => $meetingRoomId], ['endDate' => 'DESC']);

            $existingReservation = $em->getRepository(Reservation::class)->findOneBy([
                'startDate' => $startDate,
                'endDate' => $endDate,
                'meetingroom' => $meetingRoomId,

            ]);

            if ($existingReservation) {
                $this->addFlash('error', "Cette salle n'est plus disponible à la réservation pour cette période.");
            } elseif ($lastReservation && $lastReservation->getEndDate() >= $startDate) {
                $this->addFlash('error', "Cette salle n'est plus disponible à la réservation pour cette période.");
            } else {
                $em->persist($reservation);
                $em->flush();
                $mailer->sendReservationConfirmation($reservation, $user);

                $this->addFlash('success', 'La réservation a été créée avec succès.');
                return $this->redirectToRoute('user.home.index');
                // return $this->redirectToRoute('user.vehicle.payment.stripe', ['reservationid' => $reservation->getId()]);
            }
        }
        // Affichage du formulaire de réservation
        return $this->render('pages/user/home/reservation.html.twig', [
            'meetingroom' => $meetingroom,
            'form' => $form->createView(),

        ]);
    }
}
