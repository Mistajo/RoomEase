<?php

namespace App\Service;


use App\Entity\Statistic;
use App\Repository\MeetingRoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;

class StatisticService
{
    private $reservationRepository;
    private $meetingRoomRepository;
    private $entityManager;

    public function __construct(ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, MeetingRoomRepository $meetingRoomRepository,)
    {
        $this->reservationRepository = $reservationRepository;
        $this->meetingRoomRepository = $meetingRoomRepository;
        $this->entityManager = $entityManager;
    }

    public function collecterStatistiques()
    {
        $reservations = $this->reservationRepository->findAll();
        $data = [];

        foreach ($reservations as $reservation) {
            $meetingroom = $reservation->getMeetingroom()->getName();

            if (!isset($data[$meetingroom])) {
                $data[$meetingroom] = 0;
            }

            $data[$meetingroom]++;
        }

        foreach ($data as $meetingroom => $count) {
            $statistic = new Statistic();
            $meetingroomEntity = $this->meetingRoomRepository->findOneBy(['name' => $meetingroom]);
            $statistic->setCount($count);
            $statistic->setMeetingroom($meetingroomEntity);
            $this->entityManager->persist($statistic);
        }

        $this->entityManager->flush();
    }
}
