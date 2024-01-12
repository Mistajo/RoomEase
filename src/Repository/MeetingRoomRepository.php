<?php

namespace App\Repository;

use App\Entity\Search;
use App\Entity\MeetingRoom;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<MeetingRoom>
 *
 * @method MeetingRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingRoom[]    findAll()
 * @method MeetingRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingRoom::class);
    }

    //    /**
    //     * @return MeetingRoom[] Returns an array of MeetingRoom objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MeetingRoom
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Recherche les salles de réunion correspondant aux paramètres de recherche
     *
     * @param Search $searchLes critères de recherche
     * @return meetingRooms[] Les salles de réunion trouvées
     */
    public function search(Search $search): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->join('r.equipment', 'e');
        // ->andWhere('r.location LIKE :location')
        // ->setParameter('location', '%' . $search->getLocation() . '%');


        if (!empty($search->getMinCapacity())) {
            $qb->andWhere('r.Capacity >= :minCapacity')
                ->setParameter('minCapacity', $search->getMinCapacity());
        }

        if (!empty($search->getName())) {
            $qb->andWhere('r.name LIKE :name')
                ->setParameter('name', "%{$search->getName()}%");
        }

        if (!empty($search->getMaxCapacity())) {
            $qb->andWhere('r.Capacity <= :maxCapacity')
                ->setParameter('maxCapacity', $search->getMaxCapacity());
        }

        if (!empty($search->getEquipments())) {
            $qb = $qb
                ->andWhere('e.id IN (:equipments)')
                ->setParameter('equipments', $search->getEquipments());
        }


        if (!empty($search->getMinPrice())) {
            $qb->andWhere('r.minprice >= :minPrice')
                ->setParameter('minPrice', $search->getMinPrice());
        }
        if (!empty($search->getMaxPrice())) {
            $qb->andWhere('r.maxprice <= :maxPrice')
                ->setParameter('maxPrice', $search->getMaxPrice());
        }
        return $qb->getQuery()->getResult();
    }
}
