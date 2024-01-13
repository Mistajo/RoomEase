<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Reservation;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;
    private $twig;


    public function __construct(MailerInterface $mailer, Environment $twig,)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendReservationConfirmation(Reservation $reservation, User $user)
    {
        $email = (new TemplatedEmail());
        $email->from(new Address('roomease@gmail.com', 'Contact'))
            ->to($user->getEmail())
            ->subject('Confirmation de votre réservation')
            ->html($this->twig->render('Email/confirmation_reservation_email.html.twig', [
                'reservation' => $reservation,
            ]));

        $this->mailer->send($email);
    }

    public function sendReservationCancellationConfirmation(Reservation $reservation, User $user)
    {
        $email = (new TemplatedEmail());
        $email->from(new Address('roomease@gmail.com', 'Contact'))
            ->to($user->getEmail())
            ->subject('Annulation de votre réservation')
            ->html($this->twig->render('Email/cancellation_reservation_email.html.twig', [
                'reservation' => $reservation,
            ]));

        $this->mailer->send($email);
    }

    public function sendReservationReminder(Reservation $reservation, User $user)
    {
        $email = (new TemplatedEmail());
        $email
            ->from(new Address('roomease@gmail.com', 'Contact'))
            ->to($user->getEmail())
            ->subject('Rappel de réservation')
            ->html($this->twig->render('Email/reminder_email.html.twig', [
                'reservation' => $reservation,
            ]));

        $this->mailer->send($email);
    }
}
