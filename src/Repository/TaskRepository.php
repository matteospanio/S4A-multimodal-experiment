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

        $results = $this->createQueryBuilder('t')
            ->select('t.type, tr.createdAt, tr.id')
            ->leftJoin('t.trials', 'tr')
            ->where('tr.createdAt >= :since')
            ->setParameter('since', $since)
            ->getQuery()
            ->getArrayResult();

        // Group by type, date, hour
        $stats = [];
        foreach ($results as $row) {
            if (!$row['createdAt']) continue;
            $dt = new \DateTime($row['createdAt']->format('Y-m-d H:00:00'));
            $type = $row['type'];
            $date = $dt->format('Y-m-d');
            $hour = $dt->format('H');
            $key = $type . '|' . $date . '|' . $hour;
            if (!isset($stats[$key])) {
                $stats[$key] = [
                    'type' => $type,
                    'date_created' => $date,
                    'hour_created' => $hour,
                    'trial_count' => 0,
                ];
            }
            $stats[$key]['trial_count']++;
        }
        return array_values($stats);
    }
}
