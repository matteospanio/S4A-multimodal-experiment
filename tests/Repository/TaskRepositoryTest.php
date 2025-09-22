<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Factory\ExperimentFactory;
use App\Factory\Stimulus\FlavorFactory;
use App\Factory\Stimulus\SongFactory;
use App\Factory\TaskFactory;
use App\Factory\Trial\MusicToFlavorTrialFactory;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TaskRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetTaskTypeCountsReturnsArray(): void
    {
        $result = TaskFactory::repository()->getTaskTypeCounts();

        $this->assertIsArray($result);

        // Each element should have type and trial_count keys
        foreach ($result as $item) {
            $this->assertArrayHasKey('type', $item);
            $this->assertArrayHasKey('trial_count', $item);
            $this->assertIsString($item['type']);
            $this->assertIsNumeric($item['trial_count']);
        }
    }

    public function testGetHourlyTaskStatsReturnsArray(): void
    {
        $result = TaskFactory::repository()->getHourlyTaskStats();

        $this->assertIsArray($result);

        // Each element should have required keys for hourly stats
        foreach ($result as $item) {
            $this->assertArrayHasKey('type', $item);
            $this->assertArrayHasKey('hour_created', $item);
            $this->assertArrayHasKey('date_created', $item);
            $this->assertArrayHasKey('trial_count', $item);
            $this->assertIsString($item['type']);
            $this->assertIsNumeric($item['trial_count']);
        }
    }

    public function testGetHourlyTaskStatsReturnsOnlyLast12Hours(): void
    {
        $task = TaskFactory::createOne();
        SongFactory::createMany(10);
        FlavorFactory::createMany(10);
        MusicToFlavorTrialFactory::createMany(10, [
            'task' => $task,
        ]);
        $result = TaskFactory::repository()->getHourlyTaskStats();

        $cutoffTime = new \DateTime('-12 hours');

        foreach ($result as $item) {
            $itemDateTime = new \DateTime($item['date_created'] . ' ' . sprintf('%02d:00:00', $item['hour_created']));
            $this->assertGreaterThanOrEqual($cutoffTime, $itemDateTime,
                'Result should only include data from the last 12 hours');
        }
    }
}
