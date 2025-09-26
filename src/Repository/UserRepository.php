<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Create a new user in the system.
     */
    public function create(string $username, string $password, bool $isAdmin = false): void
    {
        $roles = ['ROLE_USER'];
        if ($isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        $newUser = (new User())
            ->setUsername($username)
            ->setRoles($roles)
        ;

        $hashedPassword = $this->passwordHasher->hashPassword($newUser, $password);
        $newUser->setPassword($hashedPassword);

        // Ensure timestamps are set - fallback for when Gedmo listeners don't work in production
        // In production mode, Gedmo's TimestampableListener may not be properly registered,
        // causing created_at and updated_at to remain null, which violates NOT NULL constraints
        $now = new \DateTimeImmutable();
        if ($newUser->getCreatedAt() === null) {
            $newUser->setCreatedAt($now);
        }
        if ($newUser->getUpdatedAt() === null) {
            $newUser->setUpdatedAt($now);
        }

        $errors = $this->validator->validate($newUser);

        if (count($errors) > 0) {
            $errorMessage = (string) $errors;
            throw new \InvalidArgumentException($errorMessage);
        }

        $this->getEntityManager()->persist($newUser);
        $this->getEntityManager()->flush();
    }
}
