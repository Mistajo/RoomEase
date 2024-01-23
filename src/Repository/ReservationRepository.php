<?php

namespace App\Repository;

use App\Entity\MeetingRoom;
use App\Entity\Reservation;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Reservation::class);
        $this->paginator = $paginator;
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getReservationsByRoom()
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as reservation_count, m.name as room_name')
            ->leftJoin('r.meetingroom', 'm')
            ->groupBy('m.id')
            ->getQuery();

        return $query->getResult();
    }

    public function search($text, $page = 1, $perPage = 6)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.meetingroom', 'mr');
        $qb->leftJoin('r.user', 'ur')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('r.id', ':text'),
                    $qb->expr()->like('r.title', ':text'),
                    $qb->expr()->like('mr.name', ':text'),
                    $qb->expr()->like('ur.lastName', ':text'),
                    $qb->expr()->like('ur.firstName', ':text')
                )
            )->setParameter(':text', '%' . $text . '%');

        $query = $qb->getQuery();
        return $this->paginator->paginate($query, $page, $perPage);
    }
}
