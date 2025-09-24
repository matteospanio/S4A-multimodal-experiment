<?php

declare(strict_types=1);

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
readonly class Mathematician
{
    public function __construct(
        private FlavorToMusicTrialRepository $flavorToMusicTrialRepository,
        private MusicToFlavorTrialRepository $musicToFlavorTrialRepository,
    )
    {
    }

    /**
     * Calculate statistics for Task 1 (MUSICS2SMELL): for a given flavor,
     * show which songs were chosen and their percentages.
     *
     * @param Flavor $flavor The flavor that was presented to participants
     * @param MusicToFlavorTrial $trial The specific trial to filter songs by (if provided)
     * @return array{labels: array<string>, data: array<float>, backgroundColors: array<string>, borderColors: array<string>, expectedSongId: int|null}
     */
    public function getMusicToFlavorStatistics(Flavor $flavor, MusicToFlavorTrial $trial): array
    {
        $flavorId = $flavor->getId();

        // If trial is provided, filter by the songs in that trial
        $songIds = [];
        foreach ($trial->getSongs() as $song) {
            $songIds[] = $song->getId();
        }

        $choiceStats = $this->musicToFlavorTrialRepository->getChoiceStatisticsByFlavorAndSongs($flavorId, $songIds);
        $totalTrials = $this->musicToFlavorTrialRepository->countTrialsByFlavorAndSongs($flavorId, $songIds);

        // If no statistics yet, create empty entries for each song in the trial
        if ($choiceStats === [] || $totalTrials === 0) {
            $choiceStats = [];
            foreach ($trial->getSongs() as $song) {
                $choiceStats[] = [
                    'choice_id' => (string)$song->getId(),
                    'song_id' => $song->getId(),
                    'choice_flavor_name' => $song->getFlavor()?->getName() ?? 'Unknown',
                    'count' => 0
                ];
            }

            $totalTrials = 1; // Prevent division by zero
        }

        if ($choiceStats === []) {
            return [
                'labels' => [],
                'data' => [],
                'backgroundColors' => [],
                'borderColors' => [],
                'expectedSongId' => null
            ];
        }

        // Get the expected song (the one from the trial whose flavor matches the trial's flavor)
        $expectedSongId = null;
        foreach ($trial->getSongs() as $song) {
            if ($song->getFlavor() && $song->getFlavor()->getId() === $flavor->getId()) {
                $expectedSongId = $song->getId();
                break;
            }
        }

        // Build the result arrays
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];

        foreach ($choiceStats as $stat) {
            $labels[] = sprintf('Song #%d (%s)', $stat['song_id'], $stat['choice_flavor_name']);
            $data[] = round(($stat['count'] / $totalTrials) * 100, 1);

            // Highlight the expected song with a different color
            if ($expectedSongId && (int)$stat['song_id'] === $expectedSongId) {
                $backgroundColors[] = 'rgba(255, 99, 132, 0.6)'; // Red for expected
                $borderColors[] = 'rgba(255, 99, 132, 1.0)';
            } else {
                $backgroundColors[] = 'rgba(54, 162, 235, 0.6)'; // Blue for others
                $borderColors[] = 'rgba(54, 162, 235, 1.0)';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'borderColors' => $borderColors,
            'expectedSongId' => $expectedSongId
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
        $songId = $song->getId();
        $choiceStats = $this->flavorToMusicTrialRepository->getChoiceStatisticsBySong($songId);
        $totalTrials = $this->flavorToMusicTrialRepository->countTrialsBySong($songId);

        if ($choiceStats === [] || $totalTrials === 0) {
            return [
                'labels' => [],
                'data' => [],
                'backgroundColors' => [],
                'borderColors' => [],
                'expectedFlavorId' => null
            ];
        }

        $expectedFlavor = $song->getFlavor();
        $expectedFlavorId = $expectedFlavor?->getId();

        // Build the result arrays
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];

        foreach ($choiceStats as $stat) {
            $labels[] = sprintf('%s %s', $stat['choice_icon'], $stat['choice_name']);
            $data[] = round(($stat['count'] / $totalTrials) * 100, 1);

            // Highlight the expected flavor with a different color
            if ($expectedFlavorId && (int)$stat['choice_id'] === $expectedFlavorId) {
                $backgroundColors[] = 'rgba(255, 99, 132, 0.6)'; // Red for expected
                $borderColors[] = 'rgba(255, 99, 132, 1.0)';
            } else {
                $backgroundColors[] = 'rgba(54, 162, 235, 0.6)'; // Blue for others
                $borderColors[] = 'rgba(54, 162, 235, 1.0)';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'borderColors' => $borderColors,
            'expectedFlavorId' => $expectedFlavorId
        ];
    }
}
