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
        $qb = $this->createQueryBuilder('r');
        // ->andWhere('r.location LIKE :location')
        // ->setParameter('location', '%' . $search->getLocation() . '%');

        if ($search->getMinCapacity()) {
            $qb->andWhere('r.Capacity >= :minCapacity')
                ->setParameter('minCapacity', $search->getMinCapacity());
        }

        if ($search->getMaxCapacity()) {
            $qb->andWhere('r.Capacity <= :maxCapacity')
                ->setParameter('maxCapacity', $search->getMaxCapacity());
        }

        if ($search->getMinPrice()) {
            $qb->andWhere('r.minprice >= :minPrice')
                ->setParameter('minPrice', $search->getMinPrice());
        }
        if ($search->getMaxPrice()) {
            $qb->andWhere('r.maxprice <= :maxPrice')
                ->setParameter('maxPrice', $search->getMaxPrice());
        }

        return $qb->getQuery()->getResult();
    }
}
