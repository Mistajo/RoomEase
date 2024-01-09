<?php

namespace App\Entity;

use App\Repository\SearchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: SearchRepository::class)]
class Search
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(nullable: true)]
    private ?int $minCapacity = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(nullable: true)]
    private ?int $maxCapacity = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(nullable: true)]
    private ?int $minPrice = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(nullable: true)]
    private ?int $maxPrice = null;


    #[Assert\Length(
        max: 255,
        maxMessage: 'La Localisation ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'La Localisation doit contenir uniquement des lettres, des chiffres le tiret du milieu de l\'undescore.',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'searches')]
    private ?User $user = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: MeetingRoom::class, inversedBy: 'searches')]
    private Collection $meetingroom;

    #[Assert\Length(
        max: 255,
        maxMessage: 'La Localisation ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Le nom doit contenir uniquement des lettres, des chiffres, le tiret du milieu et l\'undescore.',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'searches')]
    private Collection $equipments;

    public function __construct()
    {
        $this->meetingroom = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinCapacity(): ?int
    {
        return $this->minCapacity;
    }

    public function setMinCapacity(?int $minCapacity): static
    {
        $this->minCapacity = $minCapacity;

        return $this;
    }

    public function getMaxCapacity(): ?int
    {
        return $this->maxCapacity;
    }

    public function setMaxCapacity(?int $maxCapacity): static
    {
        $this->maxCapacity = $maxCapacity;

        return $this;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function setMinPrice(?int $minPrice): static
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?int $maxPrice): static
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, MeetingRoom>
     */
    public function getMeetingroom(): Collection
    {
        return $this->meetingroom;
    }

    public function addMeetingroom(MeetingRoom $meetingroom): static
    {
        if (!$this->meetingroom->contains($meetingroom)) {
            $this->meetingroom->add($meetingroom);
        }

        return $this;
    }

    public function removeMeetingroom(MeetingRoom $meetingroom): static
    {
        $this->meetingroom->removeElement($meetingroom);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }
}
