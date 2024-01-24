<?php

namespace App\Controller\User\Home;

use Stripe\Price;
use Stripe\Stripe;
use App\Entity\Search;
use App\Entity\Payment;
use App\Entity\MeetingRoom;
use App\Entity\Reservation;
use App\Form\SearchFormType;
use Stripe\Checkout\Session;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

            // vérification de l'heure de début
            $startHour = $startDate->format('H');
            if ($startHour < 7 || $startHour >= 19) {
                // l'heure de début n'est pas valide
                throw new \Exception('La réservation ne peut pas débuter avant 7h ou après 19h.');
            }

            // vérification de l'heure de fin
            $endHour = $endDate->format('H');
            if ($endHour < 7 || $endHour > 19) {
                // l'heure de fin n'est pas valide
                throw new \Exception('La réservation ne peut pas se terminer après 19h.');
            }


            $reservation->setUser($this->getUser());
            $reservation->setMeetingRoom($meetingroom);
            $totalPrice = $reservation->calculateTotalPrice();
            $reservation->setTotalPrice($totalPrice);
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


                return $this->redirectToRoute('user.meetingroom.reservation_recap', ['reservationid' => $reservation->getId()]);
                // return $this->redirectToRoute('user.vehicle.payment.stripe', ['reservationid' => $reservation->getId()]);
            }
        }
        // Affichage du formulaire de réservation
        return $this->render('pages/user/home/reservation.html.twig', [
            'meetingroom' => $meetingroom,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'user.reservation.edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $em, MeetingRoom $meetingroom, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $meetingroom = $reservation->getMeetingroom();


        $form = $this->createForm(ReservationFormType::class, $reservation, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', "La reservation a été modifiée.");

            return $this->redirectToRoute('user.home.index');
        }

        return $this->render("pages/user/home/edit.html.twig", [
            'form' => $form->createView(),
            'reservation' => $reservation,


        ]);
    }

    #[Route('/reservation/{reservationid}/recap', name: 'user.meetingroom.reservation_recap')]
    public function reservationRecap(int $reservationid, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->find($reservationid);

        // Vérification que la réservation existe
        if (!$reservation) {
            throw $this->createNotFoundException('Cette réservation n\'existe pas.');
        }

        return $this->render('pages/user/home/reservation_recap.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/reservation/{reservationid}/create-session-stripe', name: 'user.reservation.payment.stripe')]
    public function StripePayment($reservationid, EntityManagerInterface $em, UrlGeneratorInterface $generator): RedirectResponse
    {
        $reservationRepository = $em->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($reservationid);
        if (!$reservation) {
            throw $this->createNotFoundException("La réservation demandée n'existe pas.");
        }
        Stripe::setApiKey($_ENV['Stripe_API_SECRET']);
        $price = Price::create([
            'unit_amount' => $reservation->getTotalPrice() * 100, // Total amount multiplied by 100 (in cents)
            'currency' => 'EUR', // Use your preferred currency (e.g., 'eur', 'usd')
            'product_data' => [
                'name' => 'Réservation de véhicule #' . $reservation->getId(),
            ],
        ]);
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price' => $price->id,
                'quantity' => 1,
            ]],
            'success_url' => $generator->generate('user.payment.success', [
                'reservationid' => $reservation->getId(),
                'userid' => $reservation->getUser()->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),

            'cancel_url' => $generator->generate('user.payment.error', [
                'reservationid' => $reservation->getId(),
                'userid' => $reservation->getUser()->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/user/reservation/{reservationid}/success', name: 'user.payment.success')]
    public function paymentSuccess($reservationid, EntityManagerInterface $em, MeetingRoomRepository $meetingRoomRepository, Request $request, PaginatorInterface $paginator)
    {
        // $vehiclesAvailable = $vehicleRepository->findBY(['isAvailable' => true], ['availableAt' => 'DESC']);


        $payment = $em->getRepository(Payment::class)->findOneBy(
            ['reservation' =>
            $reservationid]
        );
        $reservation = $em->getRepository(Reservation::class)->find($reservationid);
        $totalPrice = $reservation->getTotalPrice();

        $payment = new Payment();

        $payment->setUser($this->getUser());
        $payment->setTotalPrice($totalPrice);
        $payment->setMethodOfPayment('Card');
        $payment->setReservation($reservation);
        $reservation->setPaymentStatus('Payée');
        $PaymentStatus = $reservation->getPaymentStatus();
        // Vérifier si le paiement est un succès
        if ($PaymentStatus === 'Payée') {
            $em->persist($payment);
            $em->persist($reservation);
            $em->flush();
            // Ajouter le message flash
            $this->addFlash("success", "Merci pour votre réservation.");
            // Rediriger vers une autre page
            return $this->redirectToRoute('user.home.index');
        }

        // Si le paiement n'est pas un succès, vous pouvez également afficher un message flash pour indiquer l'échec
        $this->addFlash('error', "Votre Paiement à échoué. Merci de réassayer");
        // Rediriger vers une autre page
        return $this->redirectToRoute('user.reservation.payment.stripe');

        return $this->render('pages/user/home/index.html.twig', []);
    }

    #[Route('/user/reservation/{reservationid}/error', name: 'user.payment.error')]
    public function paymentError($reservationid, EntityManagerInterface $em, MeetingRoomRepository $meetingRoomRepository, Request $request, PaginatorInterface $paginator)
    {

        $payment = $em->getRepository(Payment::class)->findOneBy(
            ['reservation' =>
            $reservationid]
        );
        $reservation = $em->getRepository(Reservation::class)->find($reservationid);
        $totalPrice = $reservation->getTotalPrice();

        $payment = new Payment();

        $payment->setUser($this->getUser());
        $payment->setTotalPrice($totalPrice);
        $payment->setMethodOfPayment('Card');
        $payment->setReservation($reservation);
        $reservation->setPaymentStatus('Non payée');
        $PaymentStatus = $reservation->getPaymentStatus();
        // Vérifier si le paiement est un succès
        if ($PaymentStatus === 'Payée') {
            $em->persist($payment);
            $em->persist($reservation);
            $em->flush();
            // Ajouter le message flash
            $this->addFlash("success", "Merci pour votre réservation.");
            // Rediriger vers une autre page
            return $this->redirectToRoute('user.home.index');
        }

        // Si le paiement n'est pas un succès, vous pouvez également afficher un message flash pour indiquer l'échec
        $this->addFlash('error', "Votre Paiement à échoué. Merci de réassayer");
        // Rediriger vers une autre page
        return $this->redirectToRoute('user.home.index');

        return $this->render('pages/user/home/index.html.twig', [

            'reservation' => $reservation,
        ]);

        return $this->render('pages/user/home/recap_reservation.html.twig', []);
    }
}
