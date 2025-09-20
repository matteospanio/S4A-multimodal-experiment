<?php

namespace App\Story;

use App\Factory\Stimulus\FlavorFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\Persistence\flush_after;

final class FlavorStory extends Story
{
    public function build(): void
    {
        FlavorFactory::createSequence([
            ['name' => 'Lemon'],
            ['name' => 'Caramel'],
            ['name' => 'Coffee'],
            ['name' => 'Apple'],
        ]);
    }
}
