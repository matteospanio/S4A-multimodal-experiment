<?php

namespace App\Repository;

use App\Entity\Trial\MusicToFlavorTrial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicToFlavorTrial>
 */
class MusicToFlavorTrialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicToFlavorTrial::class);
    }

    /**
     * Get choice statistics for a specific flavor in MusicToFlavor trials
     * 
     * @param int $flavorId
     * @return array Array of ['choice_id', 'choice_name', 'choice_flavor_name', 'count']
     */
    public function getChoiceStatisticsByFlavor(int $flavorId): array
    {
        return $this->createQueryBuilder('m')
            ->select([
                'IDENTITY(m.choice) as choice_id',
                's.id as song_id',
                'f.name as choice_flavor_name',
                'COUNT(m.id) as count'
            ])
            ->leftJoin('m.choice', 's')
            ->leftJoin('s.flavor', 'f')
            ->where('m.flavor = :flavorId')
            ->andWhere('m.choice IS NOT NULL')
            ->setParameter('flavorId', $flavorId)
            ->groupBy('m.choice, s.id, f.name')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total number of trials for a specific flavor
     * 
     * @param int $flavorId
     * @return int
     */
    public function countTrialsByFlavor(int $flavorId): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.flavor = :flavorId')
            ->andWhere('m.choice IS NOT NULL')
            ->setParameter('flavorId', $flavorId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get choice statistics for a specific flavor, filtered by songs from a specific trial
     * 
     * @param int $flavorId
     * @param array $songIds Array of song IDs that were presented in the trial
     * @return array Array of ['choice_id', 'choice_name', 'choice_flavor_name', 'count']
     */
    public function getChoiceStatisticsByFlavorAndSongs(int $flavorId, array $songIds): array
    {
        if (empty($songIds)) {
            return [];
        }

        return $this->createQueryBuilder('m')
            ->select([
                'IDENTITY(m.choice) as choice_id',
                's.id as song_id',
                'f.name as choice_flavor_name',
                'COUNT(m.id) as count'
            ])
            ->leftJoin('m.choice', 's')
            ->leftJoin('s.flavor', 'f')
            ->where('m.flavor = :flavorId')
            ->andWhere('m.choice IS NOT NULL')
            ->andWhere('s.id IN (:songIds)')
            ->setParameter('flavorId', $flavorId)
            ->setParameter('songIds', $songIds)
            ->groupBy('m.choice, s.id, f.name')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total number of trials for a specific flavor, filtered by songs from a specific trial
     * 
     * @param int $flavorId
     * @param array $songIds Array of song IDs that were presented in the trial
     * @return int
     */
    public function countTrialsByFlavorAndSongs(int $flavorId, array $songIds): int
    {
        if (empty($songIds)) {
            return 0;
        }

        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.flavor = :flavorId')
            ->andWhere('m.choice IS NOT NULL')
            ->andWhere('m.choice IN (:songIds)')
            ->setParameter('flavorId', $flavorId)
            ->setParameter('songIds', $songIds)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return MusicToFlavorTrial[] Returns an array of MusicToFlavorTrial objects
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

    //    public function findOneBySomeField($value): ?MusicToFlavorTrial
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
