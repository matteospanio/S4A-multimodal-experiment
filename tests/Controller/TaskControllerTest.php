<?php

namespace App\Tests\Controller;

use App\Story\S4AExperimentStory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Attribute\WithStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TaskControllerTest extends KernelTestCase
{
    use HasBrowser;
    use Factories;
    use ResetDatabase;

    #[WithStory(S4AExperimentStory::class)]
    public function testIndex(): void
    {
        $this->browser()
            ->visit('/task')
            ->assertSuccessful()
        ;
    }

    public function testFailingIndex(): void
    {
        $this->browser()
            ->visit('/task')
            ->assertStatus(500);
    }
}
