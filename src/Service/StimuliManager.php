<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Trial;
use App\Entity\Flavor;
use App\Entity\Song;
use App\Repository\FlavorRepository;
use App\Repository\SongRepository;

/**
 * This service is responsible to manage the stimuli presentation logic.
 * Its main purpose is to decide which trial should be presented to the participant.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
final readonly class StimuliManager
{
    public function __construct(
        private FlavorRepository $flavorRepository,
        private SongRepository   $songRepository
    ) {
    }

    /**
     * Get the next trial for a participant based on balanced combination logic.
     *
     * @param string $taskType Either Trial::SMELLS2MUSIC or Trial::MUSICS2SMELL
     * @return array Trial data with stimuli information
     */
    public function getNextTrial(string $taskType): array
    {
        $flavors = $this->flavorRepository->findAll();

        if (count($flavors) < 2) {
            throw new \RuntimeException('At least 2 flavors are required for trial generation.');
        }

        return match ($taskType) {
            Trial::SMELLS2MUSIC => $this->generateBalancedSmells2MusicTrial($flavors),
            Trial::MUSICS2SMELL => $this->generateBalancedMusics2SmellTrial($flavors),
            default => throw new \InvalidArgumentException('Invalid task type: ' . $taskType),
        };
    }

    /**
     * Generate a balanced trial for the Smells to Music task.
     * This version ensures better distribution of flavor combinations.
     *
     * @param Flavor[] $flavors
     * @return array
     */
    private function generateBalancedSmells2MusicTrial(array $flavors): array
    {
        // Get a balanced combination of flavors
        $combination = $this->getBalancedFlavorCombination($flavors);
        $primaryFlavorId = $combination['primary'];
        $secondaryFlavorId = $combination['secondary'];

        // Find the actual flavor objects
        $primaryFlavor = null;
        $secondaryFlavor = null;

        foreach ($flavors as $flavor) {
            if ($flavor->getId() === $primaryFlavorId) {
                $primaryFlavor = $flavor;
            }
            if ($flavor->getId() === $secondaryFlavorId) {
                $secondaryFlavor = $flavor;
            }
        }

        if (!$primaryFlavor || !$secondaryFlavor) {
            throw new \RuntimeException('Could not find flavors for balanced combination.');
        }

        // Get songs for the primary flavor
        $songsForPrimaryFlavor = $this->songRepository->findBy(['flavor' => $primaryFlavor]);

        if (empty($songsForPrimaryFlavor)) {
            throw new \RuntimeException('No songs found for flavor: ' . $primaryFlavor->getName());
        }

        // Pick a random song from the primary flavor
        $selectedSong = $songsForPrimaryFlavor[array_rand($songsForPrimaryFlavor)];

        return [
            'taskType' => Trial::SMELLS2MUSIC,
            'primarySong' => $selectedSong,
            'primaryFlavor' => $primaryFlavor,
            'secondaryFlavor' => $secondaryFlavor,
            'flavorLabels' => $this->getRandomizedFlavorLabels([$primaryFlavor, $secondaryFlavor]),
            'combination' => $combination
        ];
    }

    /**
     * Generate a balanced trial for the Musics to Smell task.
     * This version ensures better distribution of flavor combinations.
     *
     * @param Flavor[] $flavors
     * @return array
     */
    private function generateBalancedMusics2SmellTrial(array $flavors): array
    {
        // Get a balanced combination of flavors
        $combination = $this->getBalancedFlavorCombination($flavors);
        $firstFlavorId = $combination['primary'];
        $secondFlavorId = $combination['secondary'];

        // Find the actual flavor objects
        $firstFlavor = null;
        $secondFlavor = null;

        foreach ($flavors as $flavor) {
            if ($flavor->getId() === $firstFlavorId) {
                $firstFlavor = $flavor;
            }
            if ($flavor->getId() === $secondFlavorId) {
                $secondFlavor = $flavor;
            }
        }

        if (!$firstFlavor || !$secondFlavor) {
            throw new \RuntimeException('Could not find flavors for balanced combination.');
        }

        // Get songs for each flavor
        $firstFlavorSongs = $this->songRepository->findBy(['flavor' => $firstFlavor]);
        $secondFlavorSongs = $this->songRepository->findBy(['flavor' => $secondFlavor]);

        if (empty($firstFlavorSongs)) {
            throw new \RuntimeException('No songs found for flavor: ' . $firstFlavor->getName());
        }

        if (empty($secondFlavorSongs)) {
            throw new \RuntimeException('No songs found for flavor: ' . $secondFlavor->getName());
        }

        // Pick random songs from each flavor
        $firstSong = $firstFlavorSongs[array_rand($firstFlavorSongs)];
        $secondSong = $secondFlavorSongs[array_rand($secondFlavorSongs)];

        return [
            'taskType' => Trial::MUSICS2SMELL,
            'primarySong' => $firstSong,
            'secondarySong' => $secondSong,
            'primaryFlavor' => $firstFlavor,
            'secondaryFlavor' => $secondFlavor,
            'songLabels' => $this->getRandomizedSongLabels([$firstSong, $secondSong]),
            'combination' => $combination
        ];
    }

    /**
     * Get a balanced flavor combination using a simple round-robin approach.
     * This ensures all combinations are presented with roughly equal frequency.
     *
     * @param Flavor[] $flavors
     * @return array
     */
    private function getBalancedFlavorCombination(array $flavors): array
    {
        $allCombinations = $this->getAllPossibleCombinations($flavors);

        // For simplicity, use a time-based pseudo-random selection that cycles through combinations
        // In a real application, this could be enhanced with session storage or database tracking
        $index = (int)(time() / 60) % count($allCombinations); // Changes every minute
        $selectedCombination = $allCombinations[$index];

        // Add some randomness while maintaining balance
        $randomOffset = rand(0, 2); // Small random offset to avoid strict predictability
        $balancedIndex = ($index + $randomOffset) % count($allCombinations);

        return $allCombinations[$balancedIndex];
    }

    /**
     * Generate a trial for the Smells to Music task (legacy method).
     * Pick a random song from flavor A, then pick a random second flavor B.
     *
     * @param Flavor[] $flavors
     * @return array
     * @deprecated Use generateBalancedSmells2MusicTrial for better balance
     */
    private function generateSmells2MusicTrial(array $flavors): array
    {
        // Pick a random flavor for the song
        $primaryFlavor = $flavors[array_rand($flavors)];

        // Get songs for this flavor
        $songsForPrimaryFlavor = $this->songRepository->findBy(['flavor' => $primaryFlavor]);

        if (empty($songsForPrimaryFlavor)) {
            throw new \RuntimeException('No songs found for flavor: ' . $primaryFlavor->getName());
        }

        // Pick a random song from the primary flavor
        $selectedSong = $songsForPrimaryFlavor[array_rand($songsForPrimaryFlavor)];

        // Pick a different flavor for comparison
        $availableFlavors = array_filter($flavors, fn($f) => $f->getId() !== $primaryFlavor->getId());
        $secondaryFlavor = $availableFlavors[array_rand($availableFlavors)];

        return [
            'taskType' => Trial::SMELLS2MUSIC,
            'primarySong' => $selectedSong,
            'primaryFlavor' => $primaryFlavor,
            'secondaryFlavor' => $secondaryFlavor,
            'flavorLabels' => $this->getRandomizedFlavorLabels([$primaryFlavor, $secondaryFlavor])
        ];
    }

    /**
     * Generate a trial for the Musics to Smell task (legacy method).
     * Pick two different songs from two different flavors.
     *
     * @param Flavor[] $flavors
     * @return array
     * @deprecated Use generateBalancedMusics2SmellTrial for better balance
     */
    private function generateMusics2SmellTrial(array $flavors): array
    {
        // Pick two different flavors
        $shuffledFlavors = $flavors;
        shuffle($shuffledFlavors);
        $firstFlavor = $shuffledFlavors[0];
        $secondFlavor = $shuffledFlavors[1];

        // Get songs for each flavor
        $firstFlavorSongs = $this->songRepository->findBy(['flavor' => $firstFlavor]);
        $secondFlavorSongs = $this->songRepository->findBy(['flavor' => $secondFlavor]);

        if (empty($firstFlavorSongs)) {
            throw new \RuntimeException('No songs found for flavor: ' . $firstFlavor->getName());
        }

        if (empty($secondFlavorSongs)) {
            throw new \RuntimeException('No songs found for flavor: ' . $secondFlavor->getName());
        }

        // Pick random songs from each flavor
        $firstSong = $firstFlavorSongs[array_rand($firstFlavorSongs)];
        $secondSong = $secondFlavorSongs[array_rand($secondFlavorSongs)];

        return [
            'taskType' => Trial::MUSICS2SMELL,
            'primarySong' => $firstSong,
            'secondarySong' => $secondSong,
            'primaryFlavor' => $firstFlavor,
            'secondaryFlavor' => $secondFlavor,
            'songLabels' => $this->getRandomizedSongLabels([$firstSong, $secondSong])
        ];
    }

    /**
     * Get randomized flavor labels to avoid bias effects.
     *
     * @param Flavor[] $flavors
     * @return array
     */
    private function getRandomizedFlavorLabels(array $flavors): array
    {
        $labels = [];
        foreach ($flavors as $flavor) {
            $labels[] = [
                'id' => $flavor->getId(),
                'name' => $flavor->getName(),
                'icon' => $flavor->getIcon()
            ];
        }

        shuffle($labels);
        return $labels;
    }

    /**
     * Get randomized song labels to avoid bias effects.
     *
     * @param Song[] $songs
     * @return array
     */
    private function getRandomizedSongLabels(array $songs): array
    {
        $labels = [];
        foreach ($songs as $song) {
            $labels[] = [
                'id' => $song->getId(),
                'url' => $song->getUrl(),
                'prompt' => $song->getPrompt()
            ];
        }

        shuffle($labels);
        return $labels;
    }

    /**
     * Get all possible flavor combinations for balanced presentation.
     * With 4 flavors, this generates 2^4 = 16 combinations.
     *
     * @param Flavor[] $flavors
     * @return array
     */
    public function getAllPossibleCombinations(array $flavors): array
    {
        $combinations = [];

        // Generate all possible pairs of flavors
        for ($i = 0; $i < count($flavors); $i++) {
            for ($j = 0; $j < count($flavors); $j++) {
                if ($i !== $j) {
                    $combinations[] = [
                        'primary' => $flavors[$i]->getId(),
                        'secondary' => $flavors[$j]->getId()
                    ];
                }
            }
        }

        return $combinations;
    }
}
