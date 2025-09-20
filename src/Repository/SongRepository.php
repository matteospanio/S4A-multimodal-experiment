<?php

namespace App\Repository;

use App\Entity\Stimulus\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    /**
     * Returns an array of arrays with 'flavor' and 'song_count' keys.
     */
    public function findGroupedByFlavor(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('f.name AS flavor, COUNT(s.id) AS song_count')
            ->join('s.flavor', 'f')
            ->groupBy('f.name')
            ->orderBy('f.name')
            ->getQuery()
        ;

        return $query->getResult();
    }

    //    /**
    //     * @return Song[] Returns an array of Song objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Song
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
