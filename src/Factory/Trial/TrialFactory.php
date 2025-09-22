<?php

namespace App\Factory\Trial;

use App\Entity\Trial\Trial;
use App\Repository\TrialRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Trial>
 *
 * @method        Trial|Proxy                              create(array|callable $attributes = [])
 * @method static Trial|Proxy                              createOne(array $attributes = [])
 * @method static Trial|Proxy                              find(object|array|mixed $criteria)
 * @method static Trial|Proxy                              findOrCreate(array $attributes)
 * @method static Trial|Proxy                              first(string $sortBy = 'id')
 * @method static Trial|Proxy                              last(string $sortBy = 'id')
 * @method static Trial|Proxy                              random(array $attributes = [])
 * @method static Trial|Proxy                              randomOrCreate(array $attributes = [])
 * @method static TrialRepository|ProxyRepositoryDecorator repository()
 * @method static Trial[]|Proxy[]                          all()
 * @method static Trial[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Trial[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Trial[]|Proxy[]                          findBy(array $attributes)
 * @method static Trial[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Trial[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static Trial[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Trial&Proxy<Trial> create(array|callable $attributes = [])
 * @phpstan-method static Trial&Proxy<Trial> createOne(array $attributes = [])
 * @phpstan-method static Trial&Proxy<Trial> find(object|array|mixed $criteria)
 * @phpstan-method static Trial&Proxy<Trial> findOrCreate(array $attributes)
 * @phpstan-method static Trial&Proxy<Trial> first(string $sortBy = 'id')
 * @phpstan-method static Trial&Proxy<Trial> last(string $sortBy = 'id')
 * @phpstan-method static Trial&Proxy<Trial> random(array $attributes = [])
 * @phpstan-method static Trial&Proxy<Trial> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Trial, EntityRepository<Trial>> repository()
 * @phpstan-method static list<Trial&Proxy<Trial>> all()
 * @phpstan-method static list<Trial&Proxy<Trial>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Trial&Proxy<Trial>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Trial&Proxy<Trial>> findBy(array $attributes)
 * @phpstan-method static list<Trial&Proxy<Trial>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Trial&Proxy<Trial>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Trial&Proxy<Trial>> randomSet(int $number, array $attributes = [])
 */
final class TrialFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Trial::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Trial $trial): void {})
        ;
    }
}
