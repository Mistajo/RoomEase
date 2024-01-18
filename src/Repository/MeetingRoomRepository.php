<?php

namespace App\Repository;

use App\Entity\Search;
use App\Entity\MeetingRoom;
use Doctrine\ORM\QueryBuilder;
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
    public function search(Search $search, string $order = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->leftJoin('r.equipment', 'e')
            ->setMaxResults(20);

        $this->applySearchFilters($qb, $search);
        // $this->applyOrder($qb, $order);

        return $qb->getQuery()->getResult();
    }

    protected function applySearchFilters(QueryBuilder $qb, Search $search): void
    {
        if ($search->getName()) {
            $qb->andWhere('r.name LIKE :name')
                ->setParameter('name', "%{$search->getName()}%");
        }

        if ($search->getEquipments()->count() > 0) {
            $qb->andWhere('e.id IN (:equipments)')
                ->setParameter('equipments', $search->getEquipments());
        }

        if ($search->getMinCapacity()) {
            $qb->andWhere('r.Capacity >= :min_capacity')
                ->setParameter('min_capacity', $search->getMinCapacity());
        }

        if ($search->getMaxCapacity()) {
            $qb->andWhere('r.Capacity <= :max_capacity')
                ->setParameter('max_capacity', $search->getMaxCapacity());
        }

        if ($search->getMinPrice()) {
            $qb->andWhere('r.price >= :min_price')
                ->setParameter('min_price', $search->getMinPrice());
        }

        if ($search->getMaxPrice()) {
            $qb->andWhere('r.price <= :max_price')
                ->setParameter('max_price', $search->getMaxPrice());
        }
    }

    // protected function applyOrder(QueryBuilder $qb, string $order = null): void
    // {
    //     switch ($order) {
    //         case 'name_asc':
    //             $qb->orderBy('r.name', 'ASC');
    //             break;

    //         case 'name_desc':
    //             $qb->orderBy('r.name', 'DESC');
    //             break;

    //         case 'capacity_asc':
    //             $qb->orderBy('r.capacity', 'ASC');
    //             break;

    //         case 'capacity_desc':
    //             $qb->orderBy('r.capacity', 'DESC');
    //             break;

    //         case 'price_asc':
    //             $qb->orderBy('r.price', 'ASC');
    //             break;

    //         case 'price_desc':
    //             $qb->orderBy('r.price', 'DESC');
    //             break;
    //     }
    // }
}