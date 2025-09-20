<?php

namespace App\Factory\Stimulus;

use App\Entity\Stimulus\Flavor;
use App\Repository\FlavorRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Flavor>
 *
 * @method        Flavor|Proxy                              create(array|callable $attributes = [])
 * @method static Flavor|Proxy                              createOne(array $attributes = [])
 * @method static Flavor|Proxy                              find(object|array|mixed $criteria)
 * @method static Flavor|Proxy                              findOrCreate(array $attributes)
 * @method static Flavor|Proxy                              first(string $sortBy = 'id')
 * @method static Flavor|Proxy                              last(string $sortBy = 'id')
 * @method static Flavor|Proxy                              random(array $attributes = [])
 * @method static Flavor|Proxy                              randomOrCreate(array $attributes = [])
 * @method static FlavorRepository|ProxyRepositoryDecorator repository()
 * @method static Flavor[]|Proxy[]                          all()
 * @method static Flavor[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Flavor[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Flavor[]|Proxy[]                          findBy(array $attributes)
 * @method static Flavor[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Flavor[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static Flavor[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Flavor&Proxy<Flavor> create(array|callable $attributes = [])
 * @phpstan-method static Flavor&Proxy<Flavor> createOne(array $attributes = [])
 * @phpstan-method static Flavor&Proxy<Flavor> find(object|array|mixed $criteria)
 * @phpstan-method static Flavor&Proxy<Flavor> findOrCreate(array $attributes)
 * @phpstan-method static Flavor&Proxy<Flavor> first(string $sortBy = 'id')
 * @phpstan-method static Flavor&Proxy<Flavor> last(string $sortBy = 'id')
 * @phpstan-method static Flavor&Proxy<Flavor> random(array $attributes = [])
 * @phpstan-method static Flavor&Proxy<Flavor> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Flavor, EntityRepository<Flavor>> repository()
 * @phpstan-method static list<Flavor&Proxy<Flavor>> all()
 * @phpstan-method static list<Flavor&Proxy<Flavor>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Flavor&Proxy<Flavor>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Flavor&Proxy<Flavor>> findBy(array $attributes)
 * @phpstan-method static list<Flavor&Proxy<Flavor>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Flavor&Proxy<Flavor>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Flavor&Proxy<Flavor>> randomSet(int $number, array $attributes = [])
 */
final class FlavorFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Flavor::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            'icon' => self::faker()->text(20),
            'name' => self::faker()->text(15),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Flavor $flavor): void {})
        ;
    }
}
