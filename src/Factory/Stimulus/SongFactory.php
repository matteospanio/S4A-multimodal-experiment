<?php

namespace App\Factory\Stimulus;

use App\Entity\Stimulus\Song;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Song>
 *
 * @method        Song|Proxy                              create(array|callable $attributes = [])
 * @method static Song|Proxy                              createOne(array $attributes = [])
 * @method static Song|Proxy                              find(object|array|mixed $criteria)
 * @method static Song|Proxy                              findOrCreate(array $attributes)
 * @method static Song|Proxy                              first(string $sortBy = 'id')
 * @method static Song|Proxy                              last(string $sortBy = 'id')
 * @method static Song|Proxy                              random(array $attributes = [])
 * @method static Song|Proxy                              randomOrCreate(array $attributes = [])
 * @method static SongRepository|ProxyRepositoryDecorator repository()
 * @method static Song[]|Proxy[]                          all()
 * @method static Song[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Song[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Song[]|Proxy[]                          findBy(array $attributes)
 * @method static Song[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Song[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static Song[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Song&Proxy<Song> create(array|callable $attributes = [])
 * @phpstan-method static Song&Proxy<Song> createOne(array $attributes = [])
 * @phpstan-method static Song&Proxy<Song> find(object|array|mixed $criteria)
 * @phpstan-method static Song&Proxy<Song> findOrCreate(array $attributes)
 * @phpstan-method static Song&Proxy<Song> first(string $sortBy = 'id')
 * @phpstan-method static Song&Proxy<Song> last(string $sortBy = 'id')
 * @phpstan-method static Song&Proxy<Song> random(array $attributes = [])
 * @phpstan-method static Song&Proxy<Song> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Song, EntityRepository<Song>> repository()
 * @phpstan-method static list<Song&Proxy<Song>> all()
 * @phpstan-method static list<Song&Proxy<Song>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Song&Proxy<Song>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Song&Proxy<Song>> findBy(array $attributes)
 * @phpstan-method static list<Song&Proxy<Song>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Song&Proxy<Song>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Song&Proxy<Song>> randomSet(int $number, array $attributes = [])
 */
final class SongFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Song::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'flavor' => FlavorFactory::new(),
            'prompt' => self::faker()->text(),
            'url' => self::faker()->url(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Song $song): void {})
        ;
    }
}
