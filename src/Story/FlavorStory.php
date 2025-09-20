<?php

namespace App\Story;

use App\Factory\Stimulus\FlavorFactory;
use Zenstruck\Foundry\Story;

final class FlavorStory extends Story
{
    public function build(): void
    {
        FlavorFactory::createSequence([
            ['name' => 'Lemon', 'icon' => '🍋'],
            ['name' => 'Caramel', 'icon' => '🍬'],
            ['name' => 'Coffee', 'icon' => '☕'],
            ['name' => 'Apple', 'icon' => '🍏'],
        ]);
    }
}
