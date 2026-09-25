<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Factory\Trial\FlavorToMusicTrialFactory;
use App\Factory\Trial\MusicToFlavorTrialFactory;
use App\Factory\UserFactory;
use App\Story\S4AExperimentStory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Attribute\WithStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[WithStory(S4AExperimentStory::class)]
final class TrialCsvExportTest extends KernelTestCase
{
    use HasBrowser;
    use Factories;
    use ResetDatabase;

    private object $user;

    protected function setUp(): void
    {
        $this->user = UserFactory::new()->asAdmin()->create();
    }

    public function testMusicToFlavorTrialCsvExport(): void
    {
        MusicToFlavorTrialFactory::createMany(5);

        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/music-to-flavor-trial/export-csv')
            ->assertSuccessful()
            ->assertHeaderEquals('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeaderContains('Content-Disposition', 'attachment')
            ->assertHeaderContains('Content-Disposition', 'music_to_flavor_trials_')
        ;
    }

    public function testFlavorToMusicTrialCsvExport(): void
    {
        FlavorToMusicTrialFactory::createMany(5);

        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/flavor-to-music-trial/export-csv')
            ->assertSuccessful()
            ->assertHeaderEquals('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeaderContains('Content-Disposition', 'attachment')
            ->assertHeaderContains('Content-Disposition', 'flavor_to_music_trials_')
        ;
    }

    public function testMusicToFlavorTrialCsvExportWithDateFilter(): void
    {
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/music-to-flavor-trial/export-csv?date=2024-09-30')
            ->assertSuccessful()
            ->assertHeaderEquals('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeaderContains('Content-Disposition', 'attachment')
            ->assertHeaderContains('Content-Disposition', 'music_to_flavor_trials_2024-09-30')
        ;
    }

    public function testFlavorToMusicTrialCsvExportWithDateFilter(): void
    {
        $this->browser()
            ->actingAs($this->user)
            ->visit('/admin/flavor-to-music-trial/export-csv?date=2024-09-30')
            ->assertSuccessful()
            ->assertHeaderEquals('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeaderContains('Content-Disposition', 'attachment')
            ->assertHeaderContains('Content-Disposition', 'flavor_to_music_trials_2024-09-30')
        ;
    }
}