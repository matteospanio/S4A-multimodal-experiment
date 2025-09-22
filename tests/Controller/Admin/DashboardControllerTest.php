<?php

namespace App\Tests\Controller\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;

final class DashboardControllerTest extends KernelTestCase
{
    use HasBrowser;
    use Factories;

    protected function setUp(): void
    {
        $this->user = UserFactory::new()->asAdmin()->create();
    }

    public function testDashboardIndex(): void
    {
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/en')
            ->assertSuccessful()
            ->assertSee('S4A Experiment Dashboard')
            ->assertSee('Music → Aroma')
            ->assertSee('Aroma → Music')
            ->assertSee('Total Trials')
            ->assertSee('Trials Activity - Last 12 Hours')
            ->assertSee('Songs by Flavor Distribution')
            ->assertSee('Experiment Overview')
        ;
    }

    public function testDashboardContainsRequiredElements(): void
    {
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/en')
            ->assertSuccessful()
            // Check for statistics cards
            ->assertElementCount('.card', 7) // 4 stat cards + 2 chart cards + 1 overview card
            // Check for chart containers
            ->assertContains('canvas') // Charts should render canvas elements
            // Check for Bootstrap icons
            ->assertContains('bi-music-note-beamed')
            ->assertContains('bi-flask-florence')
            ->assertContains('bi-check2-square')
            ->assertContains('bi-graph-up-arrow')
        ;
    }
}
