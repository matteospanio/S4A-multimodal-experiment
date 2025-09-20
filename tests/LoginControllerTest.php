<?php

namespace App\Tests;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginControllerTest extends KernelTestCase
{
    use HasBrowser;
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        UserFactory::createOne(['username' => 'example', 'password' => 'password']);
    }

    public function testLogin(): void
    {
        $this->browser()
            ->interceptRedirects()
            ->visit('/login')
            ->assertSuccessful()
            ->fillField('_username', 'doesNotExist')
            ->fillField('_password', 'password')
            ->click('button[type="submit"]')
            ->assertRedirectedTo('/login')
            ->followRedirect()
            ->assertSeeIn('.alert-danger', 'Invalid credentials.')
        ;

        $this->browser()
            ->interceptRedirects()
            ->visit('/login')
            ->assertSuccessful()
            ->fillField('_username', 'example')
            ->fillField('_password', 'bad-password')
            ->click('button[type="submit"]')
            ->assertRedirectedTo('/login')
            ->followRedirect()
            ->assertSeeIn('.alert-danger', 'Invalid credentials.')
        ;

        $this->browser()
            ->interceptRedirects()
            ->visit('/login')
            ->assertSuccessful()
            ->fillField('_username', 'example')
            ->fillField('_password', 'password')
            ->click('button[type="submit"]')
            ->assertRedirectedTo('/')
            ->followRedirect()
            ->assertNotSee('.alert-danger')
            ->assertSuccessful()
        ;
    }
}
