<?php

namespace App\Factory;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Task>
 *
 * @method        Task|Proxy                              create(array|callable $attributes = [])
 * @method static Task|Proxy                              createOne(array $attributes = [])
 * @method static Task|Proxy                              find(object|array|mixed $criteria)
 * @method static Task|Proxy                              findOrCreate(array $attributes)
 * @method static Task|Proxy                              first(string $sortBy = 'id')
 * @method static Task|Proxy                              last(string $sortBy = 'id')
 * @method static Task|Proxy                              random(array $attributes = [])
 * @method static Task|Proxy                              randomOrCreate(array $attributes = [])
 * @method static TaskRepository|ProxyRepositoryDecorator repository()
 * @method static Task[]|Proxy[]                          all()
 * @method static Task[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Task[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Task[]|Proxy[]                          findBy(array $attributes)
 * @method static Task[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Task[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static Task[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Task&Proxy<Task> create(array|callable $attributes = [])
 * @phpstan-method static Task&Proxy<Task> createOne(array $attributes = [])
 * @phpstan-method static Task&Proxy<Task> find(object|array|mixed $criteria)
 * @phpstan-method static Task&Proxy<Task> findOrCreate(array $attributes)
 * @phpstan-method static Task&Proxy<Task> first(string $sortBy = 'id')
 * @phpstan-method static Task&Proxy<Task> last(string $sortBy = 'id')
 * @phpstan-method static Task&Proxy<Task> random(array $attributes = [])
 * @phpstan-method static Task&Proxy<Task> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Task, EntityRepository<Task>> repository()
 * @phpstan-method static list<Task&Proxy<Task>> all()
 * @phpstan-method static list<Task&Proxy<Task>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Task&Proxy<Task>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Task&Proxy<Task>> findBy(array $attributes)
 * @phpstan-method static list<Task&Proxy<Task>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Task&Proxy<Task>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Task&Proxy<Task>> randomSet(int $number, array $attributes = [])
 */
final class TaskFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Task::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'experiment' => ExperimentFactory::new(),
            'type' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
