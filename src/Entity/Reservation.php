<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?MeetingRoom $meetingroom = null;

    #[Assert\NotBlank(message: "Le titre de la reservation est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Le titre doit contenir uniquement des lettres, des chiffres le tiret du milieu et l\'undescore.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThan('today UTC')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan('today UTC')]
    #[Assert\GreaterThan(
        propertyPath: "startDate",
        message: "La date de fin doit etre supperieur à la date de début"
    )]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;


    #[Assert\Length(
        max: 255,
        maxMessage: "Le motif d'annulation ne doit pas dépasser {{ limit }} caractères.",
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: "Le motif d'annulation doit contenir uniquement des lettres, des chiffres le tiret du milieu et l'undescore.",
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Statistic::class)]
    private Collection $statistics;

    #[ORM\Column(nullable: true)]
    private ?float $totalPrice = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(nullable: true)]
    private ?float $dailyPrice = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentStatus = null;



    public function __construct()
    {
        $this->paymentStatus = "Pas Payée";
        $this->statut = "En Attente";
        $this->statistics = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): static
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    /**
     * @return Collection<int, Statistic>
     */
    public function getStatistics(): Collection
    {
        return $this->statistics;
    }

    public function addStatistic(Statistic $statistic): static
    {
        if (!$this->statistics->contains($statistic)) {
            $this->statistics->add($statistic);
            $statistic->setReservation($this);
        }

        return $this;
    }

    public function removeStatistic(Statistic $statistic): static
    {
        if ($this->statistics->removeElement($statistic)) {
            // set the owning side to null (unless already changed)
            if ($statistic->getReservation() === $this) {
                $statistic->setReservation(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getDailyPrice(): ?float
    {
        return $this->dailyPrice;
    }

    public function setDailyPrice(?float $dailyPrice): static
    {
        $this->dailyPrice = $dailyPrice;

        return $this;
    }

    public function calculateTotalPrice()
    {
        $diff = $this->endDate->diff($this->startDate);
        $nbHours = $diff->h + ($diff->days * 24);

        return $this->dailyPrice * $nbHours;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setReservation($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getReservation() === $this) {
                $payment->setReservation(null);
            }
        }

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }
}
