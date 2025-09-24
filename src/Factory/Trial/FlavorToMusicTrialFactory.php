<?php

declare(strict_types=1);

namespace App\Factory\Trial;

use App\Entity\Trial\FlavorToMusicTrial;
use App\Factory\Stimulus\FlavorFactory;
use App\Factory\Stimulus\SongFactory;
use App\Repository\FlavorToMusicTrialRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<FlavorToMusicTrial>
 *
 * @method        FlavorToMusicTrial|Proxy                              create(array|callable $attributes = [])
 * @method static FlavorToMusicTrial|Proxy                              createOne(array $attributes = [])
 * @method static FlavorToMusicTrial|Proxy                              find(object|array|mixed $criteria)
 * @method static FlavorToMusicTrial|Proxy                              findOrCreate(array $attributes)
 * @method static FlavorToMusicTrial|Proxy                              first(string $sortBy = 'id')
 * @method static FlavorToMusicTrial|Proxy                              last(string $sortBy = 'id')
 * @method static FlavorToMusicTrial|Proxy                              random(array $attributes = [])
 * @method static FlavorToMusicTrial|Proxy                              randomOrCreate(array $attributes = [])
 * @method static FlavorToMusicTrialRepository|ProxyRepositoryDecorator repository()
 * @method static FlavorToMusicTrial[]|Proxy[]                          all()
 * @method static FlavorToMusicTrial[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static FlavorToMusicTrial[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static FlavorToMusicTrial[]|Proxy[]                          findBy(array $attributes)
 * @method static FlavorToMusicTrial[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static FlavorToMusicTrial[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static FlavorToMusicTrial[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        FlavorToMusicTrial&Proxy<FlavorToMusicTrial> create(array|callable $attributes = [])
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> createOne(array $attributes = [])
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> find(object|array|mixed $criteria)
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> findOrCreate(array $attributes)
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> first(string $sortBy = 'id')
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> last(string $sortBy = 'id')
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> random(array $attributes = [])
 * @phpstan-method static FlavorToMusicTrial&Proxy<FlavorToMusicTrial> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<FlavorToMusicTrial, EntityRepository<FlavorToMusicTrial>> repository()
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> all()
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> findBy(array $attributes)
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<FlavorToMusicTrial&Proxy<FlavorToMusicTrial>> randomSet(int $number, array $attributes = [])
 */
final class FlavorToMusicTrialFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return FlavorToMusicTrial::class;
    }

    protected function defaults(): array|callable
    {
        $flavors = FlavorFactory::randomSet(2);

        return [
            'song' => SongFactory::random(),
            'flavors' => $flavors,
            'choice' => $flavors[array_rand($flavors)],
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(FlavorToMusicTrial $flavorToMusicTrial): void {})
        ;
    }
}
