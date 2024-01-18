<?php

namespace App\Entity;

use App\Repository\StatisticRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticRepository::class)]
class Statistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    #[ORM\Column(nullable: true)]
    private ?int $month = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalReservations = null;

    #[ORM\Column(nullable: true)]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'statistics')]
    private ?MeetingRoom $meetingroom = null;

    #[ORM\ManyToOne(inversedBy: 'statistics')]
    private ?Reservation $reservation = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getTotalReservations(): ?int
    {
        return $this->totalReservations;
    }

    public function setTotalReservations(?int $totalReservations): static
    {
        $this->totalReservations = $totalReservations;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getMeetingroom(): ?MeetingRoom
    {
        return $this->meetingroom;
    }

    public function setMeetingroom(?MeetingRoom $meetingroom): static
    {
        $this->meetingroom = $meetingroom;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }
}
