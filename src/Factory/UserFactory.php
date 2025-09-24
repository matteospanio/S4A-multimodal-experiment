<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<User>
 *
 * @method        User|Proxy                              create(array|callable $attributes = [])
 * @method static User|Proxy                              createOne(array $attributes = [])
 * @method static User|Proxy                              find(object|array|mixed $criteria)
 * @method static User|Proxy                              findOrCreate(array $attributes)
 * @method static User|Proxy                              first(string $sortBy = 'id')
 * @method static User|Proxy                              last(string $sortBy = 'id')
 * @method static User|Proxy                              random(array $attributes = [])
 * @method static User|Proxy                              randomOrCreate(array $attributes = [])
 * @method static UserRepository|ProxyRepositoryDecorator repository()
 * @method static User[]|Proxy[]                          all()
 * @method static User[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[]                          findBy(array $attributes)
 * @method static User[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                          randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        User&Proxy<User> create(array|callable $attributes = [])
 * @phpstan-method static User&Proxy<User> createOne(array $attributes = [])
 * @phpstan-method static User&Proxy<User> find(object|array|mixed $criteria)
 * @phpstan-method static User&Proxy<User> findOrCreate(array $attributes)
 * @phpstan-method static User&Proxy<User> first(string $sortBy = 'id')
 * @phpstan-method static User&Proxy<User> last(string $sortBy = 'id')
 * @phpstan-method static User&Proxy<User> random(array $attributes = [])
 * @phpstan-method static User&Proxy<User> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<User, EntityRepository<User>> repository()
 * @phpstan-method static list<User&Proxy<User>> all()
 * @phpstan-method static list<User&Proxy<User>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<User&Proxy<User>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<User&Proxy<User>> findBy(array $attributes)
 * @phpstan-method static list<User&Proxy<User>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<User&Proxy<User>> randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<User&Proxy<User>> randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(
        private readonly ?UserPasswordHasherInterface $passwordHasher = null,
    )
    {
        parent::__construct();
    }

    public function asAdmin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    public function asUser(): self
    {
        return $this->with(['roles' => ['ROLE_USER']]);
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'password' => self::faker()->password(),
            'roles' => [],
            'username' => self::faker()->userName(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user): void {
                if ($this->passwordHasher !== null) {
                    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                }
            })
        ;
    }
}
