<?php

namespace App\Factory;

use App\Entity\Experiment;
use App\Repository\ExperimentRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Experiment>
 *
 * @method        Experiment|Proxy                              create(array|callable $attributes = [])
 * @method static Experiment|Proxy                              createOne(array $attributes = [])
 * @method static Experiment|Proxy                              find(object|array|mixed $criteria)
 * @method static Experiment|Proxy                              findOrCreate(array $attributes)
 * @method static Experiment|Proxy                              first(string $sortBy = 'id')
 * @method static Experiment|Proxy                              last(string $sortBy = 'id')
 * @method static Experiment|Proxy                              random(array $attributes = [])
 * @method static Experiment|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ExperimentRepository|ProxyRepositoryDecorator repository()
 * @method static Experiment[]|Proxy[]                          all()
 * @method static Experiment[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Experiment[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Experiment[]|Proxy[]                          findBy(array $attributes)
 * @method static Experiment[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Experiment[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static Experiment[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Experiment&Proxy<Experiment> create(array|callable $attributes = [])
 * @phpstan-method static Experiment&Proxy<Experiment> createOne(array $attributes = [])
 * @phpstan-method static Experiment&Proxy<Experiment> find(object|array|mixed $criteria)
 * @phpstan-method static Experiment&Proxy<Experiment> findOrCreate(array $attributes)
 * @phpstan-method static Experiment&Proxy<Experiment> first(string $sortBy = 'id')
 * @phpstan-method static Experiment&Proxy<Experiment> last(string $sortBy = 'id')
 * @phpstan-method static Experiment&Proxy<Experiment> random(array $attributes = [])
 * @phpstan-method static Experiment&Proxy<Experiment> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Experiment, EntityRepository<Experiment>> repository()
 * @phpstan-method static list<Experiment&Proxy<Experiment>> all()
 * @phpstan-method static list<Experiment&Proxy<Experiment>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Experiment&Proxy<Experiment>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Experiment&Proxy<Experiment>> findBy(array $attributes)
 * @phpstan-method static list<Experiment&Proxy<Experiment>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Experiment&Proxy<Experiment>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Experiment&Proxy<Experiment>> randomSet(int $number, array $attributes = [])
 */
final class ExperimentFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Experiment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->text(50),
            'description' => self::faker()->optional()->text(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Experiment $experiment): void {})
        ;
    }
}
