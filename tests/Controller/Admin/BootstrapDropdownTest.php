<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BootstrapDropdownTest extends KernelTestCase
{
    public $user;

    use HasBrowser;
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        $this->user = UserFactory::new()->asAdmin()->create();
    }

    public function testBootstrapJavaScriptIsLoadedInDevelopment(): void
    {
        // Test that Bootstrap JavaScript is properly loaded in dev environment
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/en')
            ->assertSuccessful()
            // Check that Bootstrap JavaScript elements are present
            ->assertContains('data-bs-toggle') // Bootstrap 5 data attributes
            ->assertContains('dropdown') // Dropdown classes
            // Verify the admin.js is loaded which imports Bootstrap
            ->assertContains('admin')
        ;
    }

    public function testBootstrapJavaScriptIsLoadedInProduction(): void
    {
        // This test will help us verify production environment behavior
        // We'll need to simulate production asset compilation
        
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        $_ENV['APP_ENV'] = 'prod';
        
        try {
            $this->browser()
                ->actingAs($this->user)
                ->visit('/admin/en')
                ->assertSuccessful()
                // In production, dropdown functionality should still work
                ->assertContains('data-bs-toggle') // Bootstrap 5 data attributes should be present
                ->assertContains('dropdown') // Dropdown classes should be present
                // The key test: Bootstrap JavaScript should be available and functional
                ->execute('return typeof bootstrap !== "undefined"') // Check if Bootstrap JS object exists
            ;
        } finally {
            if ($originalEnv !== null) {
                $_ENV['APP_ENV'] = $originalEnv;
            } else {
                unset($_ENV['APP_ENV']);
            }
        }
    }

    public function testEasyAdminDropdownMenuElements(): void
    {
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/en')
            ->assertSuccessful()
            // Check for EasyAdmin menu elements that use dropdowns
            ->assertSee('Trials') // The submenu that uses Bootstrap dropdown
            // Verify that the trials submenu has proper Bootstrap structure
            ->assertContains('dropdown-toggle')
            ->assertContains('dropdown-menu')
            // Check for specific dropdown items
            ->assertSee('Music to Aroma')
            ->assertSee('Aroma to Music')
        ;
    }
}