<?php

namespace App\Repository;

use App\Entity\Stimulus\Flavor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flavor>
 */
class FlavorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flavor::class);
    }

    /**
     * @return array Returns an array of Flavor names and icons
     */
    public function getAllFlavorsIcons(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f.name, f.icon')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Flavor[] Returns an array of Flavor objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Flavor
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
