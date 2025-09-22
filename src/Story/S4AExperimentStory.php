<?php

namespace App\Story;

use App\Factory\ExperimentFactory;
use App\Factory\Stimulus\FlavorFactory;
use App\Factory\Stimulus\SongFactory;
use App\Factory\TaskFactory;
use App\Factory\Trial\FlavorToMusicTrialFactory;
use App\Factory\Trial\MusicToFlavorTrialFactory;
use Zenstruck\Foundry\Story;

final class S4AExperimentStory extends Story
{
    public function build(): void
    {
        FlavorStory::load();
        SongFactory::createMany(
            20,
            function () {
                return ['flavor' => FlavorFactory::random()];
            }
        );

        $experiment = ExperimentFactory::createOne([
            'title' => 'S4A Experiment',
            'description' => 'An experiment for S4A'
        ]);
        TaskFactory::createSequence([
            ['type' => 'music2aroma', 'experiment' => $experiment],
            ['type' => 'aroma2music', 'experiment' => $experiment],
        ]);

        MusicToFlavorTrialFactory::createMany(200);
        FlavorToMusicTrialFactory::createMany(200);
    }
}
