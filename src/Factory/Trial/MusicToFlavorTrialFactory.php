<?php

declare(strict_types=1);

namespace App\Factory\Trial;

use App\Entity\Trial\MusicToFlavorTrial;
use App\Factory\Stimulus\FlavorFactory;
use App\Factory\Stimulus\SongFactory;
use App\Repository\MusicToFlavorTrialRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<MusicToFlavorTrial>
 *
 * @method        MusicToFlavorTrial|Proxy                              create(array|callable $attributes = [])
 * @method static MusicToFlavorTrial|Proxy                              createOne(array $attributes = [])
 * @method static MusicToFlavorTrial|Proxy                              find(object|array|mixed $criteria)
 * @method static MusicToFlavorTrial|Proxy                              findOrCreate(array $attributes)
 * @method static MusicToFlavorTrial|Proxy                              first(string $sortBy = 'id')
 * @method static MusicToFlavorTrial|Proxy                              last(string $sortBy = 'id')
 * @method static MusicToFlavorTrial|Proxy                              random(array $attributes = [])
 * @method static MusicToFlavorTrial|Proxy                              randomOrCreate(array $attributes = [])
 * @method static MusicToFlavorTrialRepository|ProxyRepositoryDecorator repository()
 * @method static MusicToFlavorTrial[]|Proxy[]                          all()
 * @method static MusicToFlavorTrial[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static MusicToFlavorTrial[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static MusicToFlavorTrial[]|Proxy[]                          findBy(array $attributes)
 * @method static MusicToFlavorTrial[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static MusicToFlavorTrial[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static MusicToFlavorTrial[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        MusicToFlavorTrial&Proxy<MusicToFlavorTrial> create(array|callable $attributes = [])
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> createOne(array $attributes = [])
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> find(object|array|mixed $criteria)
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> findOrCreate(array $attributes)
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> first(string $sortBy = 'id')
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> last(string $sortBy = 'id')
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> random(array $attributes = [])
 * @phpstan-method static MusicToFlavorTrial&Proxy<MusicToFlavorTrial> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<MusicToFlavorTrial, EntityRepository<MusicToFlavorTrial>> repository()
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> all()
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> findBy(array $attributes)
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<MusicToFlavorTrial&Proxy<MusicToFlavorTrial>> randomSet(int $number, array $attributes = [])
 */
final class MusicToFlavorTrialFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return MusicToFlavorTrial::class;
    }

    protected function defaults(): array|callable
    {
        $songs = SongFactory::randomSet(2);

        return [
            'flavor' => FlavorFactory::random(),
            'songs' => $songs,
            'choice' => $songs[array_rand($songs)],
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(MusicToFlavorTrial $musicToFlavorTrial): void {})
        ;
    }
}
