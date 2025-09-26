<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class NewUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testNewUserCommandCreatesUserWithTimestamps(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:new:user');
        $commandTester = new CommandTester($command);

        // Execute the command with test inputs
        $commandTester->setInputs(['testuser', 'testpass123']);
        $exitCode = $commandTester->execute([]);

        // Assert command succeeded
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('User successfully created!', $commandTester->getDisplay());

        // Verify the user was created in the database with proper timestamps
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);
        
        $user = $userRepository->findOneBy(['username' => 'testuser']);
        
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertNotNull($user->getCreatedAt(), 'User should have created_at timestamp');
        $this->assertNotNull($user->getUpdatedAt(), 'User should have updated_at timestamp');
        
        // Verify timestamps are recent (within last minute)
        $now = new \DateTimeImmutable();
        $this->assertLessThan(60, $now->getTimestamp() - $user->getCreatedAt()->getTimestamp());
        $this->assertLessThan(60, $now->getTimestamp() - $user->getUpdatedAt()->getTimestamp());
    }

    public function testNewUserCommandWithAdminFlag(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:new:user');
        $commandTester = new CommandTester($command);

        // Execute the command with admin flag
        $commandTester->setInputs(['adminuser', 'adminpass123']);
        $exitCode = $commandTester->execute(['--admin' => true]);

        $this->assertEquals(0, $exitCode);

        // Verify the user was created with admin role
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);
        
        $user = $userRepository->findOneBy(['username' => 'adminuser']);
        
        $this->assertNotNull($user);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertNotNull($user->getCreatedAt(), 'Admin user should have created_at timestamp');
        $this->assertNotNull($user->getUpdatedAt(), 'Admin user should have updated_at timestamp');
    }

    public function testNewUserCommandFailsWithShortPassword(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:new:user');
        $commandTester = new CommandTester($command);

        // Execute the command with short password
        $commandTester->setInputs(['testuser', '123']);
        $exitCode = $commandTester->execute([]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('The password must be at least 6 characters long', $commandTester->getDisplay());
    }
}