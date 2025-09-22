<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Count completed trials by task type
     * @return array Returns array with type and count
     */
    public function getTaskTypeCounts(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.type, COUNT(tr.id) as trial_count')
            ->leftJoin('t.trials', 'tr')
            ->groupBy('t.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get tasks with trial counts for the last 12 hours grouped by hour and type
     * @return array Returns hourly statistics 
     */
    public function getHourlyTaskStats(): array
    {
        $since = new \DateTime('-12 hours');
        
        return $this->createQueryBuilder('t')
            ->select('t.type, 
                     HOUR(tr.createdAt) as hour_created,
                     DATE(tr.createdAt) as date_created, 
                     COUNT(tr.id) as trial_count')
            ->leftJoin('t.trials', 'tr')
            ->where('tr.createdAt >= :since')
            ->setParameter('since', $since)
            ->groupBy('t.type, hour_created, date_created')
            ->orderBy('date_created, hour_created')
            ->getQuery()
            ->getResult();
    }
}
