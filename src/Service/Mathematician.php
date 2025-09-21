<?php

namespace App\Service;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\Trial;
use App\Repository\FlavorToMusicTrialRepository;
use App\Repository\MusicToFlavorTrialRepository;

/**
 * This service is responsible to compute statistics about the experiment results.
 * Its main purpose is to compute percentages and averages passing them to the frontend.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
class Mathematician
{
    public function __construct(
        private readonly FlavorToMusicTrialRepository $flavorToMusicTrialRepository,
        private readonly MusicToFlavorTrialRepository $musicToFlavorTrialRepository,
    )
    {
    }

    /**
     * Calculate statistics for Task 1 (MUSICS2SMELL): for a given flavor, 
     * show which songs were chosen and their percentages.
     * 
     * @param Flavor $flavor The flavor that was presented to participants
     * @return array{labels: array<string>, data: array<float>, backgroundColors: array<string>, borderColors: array<string>, expectedSongId: int|null}
     */
    public function getMusicToFlavorStatistics(Flavor $flavor): array
    {
        // Get all trials for this flavor
        $trials = $this->musicToFlavorTrialRepository->findBy(['flavor' => $flavor]);
        
        if (empty($trials)) {
            return [
                'labels' => [],
                'data' => [],
                'backgroundColors' => [],
                'borderColors' => [],
                'expectedSongId' => null
            ];
        }

        // Count choices for each song
        $songChoices = [];
        $totalTrials = count($trials);
        $expectedSong = null;

        /** @var MusicToFlavorTrial $trial */
        foreach ($trials as $trial) {
            $choice = $trial->getChoice();
            if ($choice) {
                $songId = $choice->getId();
                $songChoices[$songId] = ($songChoices[$songId] ?? 0) + 1;
                
                // Find the expected song (the one associated with this flavor)
                if ($choice->getFlavor() === $flavor) {
                    $expectedSong = $choice;
                }
            }
        }

        // If no expected song was found from choices, get it from the flavor's songs
        if (!$expectedSong && !$flavor->getSongs()->isEmpty()) {
            $expectedSong = $flavor->getSongs()->first();
        }

        // Build the result arrays
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];

        foreach ($songChoices as $songId => $count) {
            // Get song details (we need to find the song somehow)
            foreach ($trials as $trial) {
                if ($trial->getChoice()?->getId() === $songId) {
                    $song = $trial->getChoice();
                    $labels[] = sprintf('Song #%d (%s)', $songId, $song->getFlavor()->getName());
                    $data[] = round(($count / $totalTrials) * 100, 1);
                    
                    // Highlight the expected song with a different color
                    if ($expectedSong && $song->getId() === $expectedSong->getId()) {
                        $backgroundColors[] = 'rgba(255, 99, 132, 0.6)'; // Red for expected
                        $borderColors[] = 'rgba(255, 99, 132, 1.0)';
                    } else {
                        $backgroundColors[] = 'rgba(54, 162, 235, 0.6)'; // Blue for others
                        $borderColors[] = 'rgba(54, 162, 235, 1.0)';
                    }
                    break;
                }
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'borderColors' => $borderColors,
            'expectedSongId' => $expectedSong?->getId()
        ];
    }

    /**
     * Calculate statistics for Task 2 (SMELLS2MUSIC): for a given song,
     * show which flavors were chosen and their percentages.
     * 
     * @param Song $song The song that was presented to participants
     * @return array{labels: array<string>, data: array<float>, backgroundColors: array<string>, borderColors: array<string>, expectedFlavorId: int|null}
     */
    public function getFlavorToMusicStatistics(Song $song): array
    {
        // Get all trials for this song
        $trials = $this->flavorToMusicTrialRepository->findBy(['song' => $song]);
        
        if (empty($trials)) {
            return [
                'labels' => [],
                'data' => [],
                'backgroundColors' => [],
                'borderColors' => [],
                'expectedFlavorId' => null
            ];
        }

        // Count choices for each flavor
        $flavorChoices = [];
        $totalTrials = count($trials);
        $expectedFlavor = $song->getFlavor();

        /** @var FlavorToMusicTrial $trial */
        foreach ($trials as $trial) {
            $choice = $trial->getChoice();
            if ($choice) {
                $flavorId = $choice->getId();
                $flavorChoices[$flavorId] = ($flavorChoices[$flavorId] ?? 0) + 1;
            }
        }

        // Build the result arrays
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];

        foreach ($flavorChoices as $flavorId => $count) {
            // Get flavor details
            foreach ($trials as $trial) {
                if ($trial->getChoice()?->getId() === $flavorId) {
                    $flavor = $trial->getChoice();
                    $labels[] = sprintf('%s %s', $flavor->getIcon(), $flavor->getName());
                    $data[] = round(($count / $totalTrials) * 100, 1);
                    
                    // Highlight the expected flavor with a different color
                    if ($expectedFlavor && $flavor->getId() === $expectedFlavor->getId()) {
                        $backgroundColors[] = 'rgba(255, 99, 132, 0.6)'; // Red for expected
                        $borderColors[] = 'rgba(255, 99, 132, 1.0)';
                    } else {
                        $backgroundColors[] = 'rgba(54, 162, 235, 0.6)'; // Blue for others
                        $borderColors[] = 'rgba(54, 162, 235, 1.0)';
                    }
                    break;
                }
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'borderColors' => $borderColors,
            'expectedFlavorId' => $expectedFlavor?->getId()
        ];
    }
}
