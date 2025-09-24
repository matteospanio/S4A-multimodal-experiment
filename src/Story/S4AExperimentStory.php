<?php

declare(strict_types=1);

namespace App\Story;

use App\Entity\Trial\Trial;
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
            30,
            fn(): array => ['flavor' => FlavorFactory::random()]
        );

        $experiment = ExperimentFactory::createOne([
            'title' => 'S4A Experiment',
            'description' => 'An experiment for S4A'
        ]);
        TaskFactory::createSequence([
            ['type' => Trial::MUSICS2SMELL, 'experiment' => $experiment],
            ['type' => Trial::SMELLS2MUSIC, 'experiment' => $experiment],
        ]);

        MusicToFlavorTrialFactory::createMany(200, ['task' => TaskFactory::repository()->findOneBy(['type' => 'music2aroma'])]);
        FlavorToMusicTrialFactory::createMany(200, ['task' => TaskFactory::repository()->findOneBy(['type' => 'aroma2music'])]);
    }
}
