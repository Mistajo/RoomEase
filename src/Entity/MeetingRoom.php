<?php

namespace App\Entity;

use App\Repository\MeetingRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MeetingRoomRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Ce nom se salle existe déja')]
#[Vich\Uploadable]
class MeetingRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Le nom doit contenir uniquement des lettres, des chiffres le tiret du milieu de l\'undescore.',
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $Capacity = null;

    #[Assert\Length(
        max: 500,
        maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;


    #[Assert\File(
        maxSize: '4096k',
        extensions: ['jpeg', 'jpg', 'png', 'webp'],
        extensionsMessage: "Seuls les formats d'images jpeg, jpg, png, webp sont autorisés.",
    )]
    #[Vich\UploadableField(mapping: 'meetingrooms', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true,)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean')]
    private $isAvailable = true;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Search::class, mappedBy: 'meetingroom')]
    private Collection $searches;

    #[ORM\OneToMany(mappedBy: 'meetingroom', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'meetingRooms', cascade: ["persist"])]
    private Collection $equipment;

    #[ORM\Column(nullable: true)]
    private ?int $totalReservations = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastReservation = null;

    #[ORM\Column(nullable: true)]
    private ?int $durationTotalReservation = 0;

    #[ORM\OneToMany(mappedBy: 'meetingroom', targetEntity: Statistic::class)]
    private Collection $statistics;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;





    public function __construct()
    {
        $this->isAvailable = true;
        $this->searches = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->equipment = new ArrayCollection();
        $this->statistics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->Capacity;
    }

    public function setCapacity(string $Capacity): static
    {
        $this->Capacity = $Capacity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Search>
     */
    public function getSearches(): Collection
    {
        return $this->searches;
    }

    public function addSearch(Search $search): static
    {
        if (!$this->searches->contains($search)) {
            $this->searches->add($search);
            $search->addMeetingroom($this);
        }

        return $this;
    }

    public function removeSearch(Search $search): static
    {
        if ($this->searches->removeElement($search)) {
            $search->removeMeetingroom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setMeetingroom($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getMeetingroom() === $this) {
                $reservation->setMeetingroom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }

    public function incrementTotalReservations()
    {
        $this->totalReservations++;
    }

    public function getTimestampLastReservation()
    {
        if ($this->lastReservation) {
            return $this->lastReservation->getTimestamp();
        } else {
            return 0;
        }
    }

    public function updateLastReservation(Reservation $reservation)
    {
        $this->lastReservation = $reservation->getstartDate();
        $this->lastReservation = $reservation->getendDate();
    }

    public function incrementDureeTotaleReservation($duration)
    {
        $this->durationTotalReservation += $duration;
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

    public function getLastReservation(): ?\DateTimeInterface
    {
        return $this->lastReservation;
    }

    public function setLastReservation(?\DateTimeInterface $lastReservation): static
    {
        $this->lastReservation = $lastReservation;

        return $this;
    }

    public function getDurationTotalReservation(): ?int
    {
        return $this->durationTotalReservation;
    }

    public function setDurationTotalReservation(?int $durationTotalReservation): static
    {
        $this->durationTotalReservation = $durationTotalReservation;

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
            $statistic->setMeetingroom($this);
        }

        return $this;
    }

    public function removeStatistic(Statistic $statistic): static
    {
        if ($this->statistics->removeElement($statistic)) {
            // set the owning side to null (unless already changed)
            if ($statistic->getMeetingroom() === $this) {
                $statistic->setMeetingroom(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
