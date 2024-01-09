<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Equipments;
use Doctrine\Persistence\ManagerRegistry;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
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
}
