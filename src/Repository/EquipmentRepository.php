<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Equipments;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Equipments>
 *
 * @method Equipments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipments[]    findAll()
 * @method Equipments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Equipment::class);
        $this->paginator = $paginator;
    }

    //    /**
    //     * @return Equipments[] Returns an array of Equipments objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipments
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBySearchTerm($term, $page = 1, $perPage = 6)
    {
        $qb = $this->createQueryBuilder('e');

        if (is_string($term) && !empty(trim($term))) {
            $qb->andWhere('e.name LIKE :term')
                ->setParameter('term', "%$term%");
        }


        $query = $qb->getQuery();
        return $this->paginator->paginate($query, $page, $perPage);
    }
}
