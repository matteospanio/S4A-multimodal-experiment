<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stimulus\Song;
use App\Entity\Trial\FlavorToMusicTrial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FlavorToMusicTrial>
 */
class FlavorToMusicTrialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlavorToMusicTrial::class);
    }

    /**
     * Get choice statistics for a specific song in FlavorToMusic trials
     *
     * @param int $songId
     * @return array Array of ['choice_id', 'choice_name', 'choice_icon', 'count']
     */
    public function getChoiceStatisticsBySong(int $songId): array
    {
        return $this->createQueryBuilder('f')
            ->select([
                'IDENTITY(f.choice) as choice_id',
                'fl.name as choice_name',
                'fl.icon as choice_icon',
                'COUNT(f.id) as count'
            ])
            ->leftJoin('f.choice', 'fl')
            ->where('f.song = :songId')
            ->andWhere('f.choice IS NOT NULL')
            ->setParameter('songId', $songId)
            ->groupBy('f.choice, fl.name, fl.icon')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total number of trials for a specific song
     *
     * @param int $songId
     * @return int
     */
    public function countTrialsBySong(int $songId): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.song = :songId')
            ->andWhere('f.choice IS NOT NULL')
            ->setParameter('songId', $songId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return FlavorToMusicTrial[] Returns an array of FlavorToMusicTrial objects
     */
    public function findTrialsByMusicId(int $musicId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.song = :musicId')
            ->setParameter('musicId', $musicId)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countIncompleteTrials(): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.choice IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return FlavorToMusicTrial[] Returns an array of FlavorToMusicTrial objects
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

//    public function findOneBySomeField($value): ?FlavorToMusicTrial
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
