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
class StimuliManager
{
    private FlavorRepository $flavorRepository;
    private SongRepository $songRepository;
    
    public function __construct(
        FlavorRepository $flavorRepository,
        SongRepository $songRepository
    ) {
        $this->flavorRepository = $flavorRepository;
        $this->songRepository = $songRepository;
    }

    /**
     * Get the next trial for a participant based on balanced combination logic.
     * 
     * @param Participant $participant
     * @param string $taskType Either Trial::SMELLS2MUSIC or Trial::MUSICS2SMELL
     * @return array Trial data with stimuli information
     */
    public function getNextTrial(Participant $participant, string $taskType): array
    {
        $flavors = $this->flavorRepository->findAll();
        
        if (count($flavors) < 2) {
            throw new \RuntimeException('At least 2 flavors are required for trial generation.');
        }

        switch ($taskType) {
            case Trial::SMELLS2MUSIC:
                return $this->generateSmells2MusicTrial($flavors);
            case Trial::MUSICS2SMELL:
                return $this->generateMusics2SmellTrial($flavors);
            default:
                throw new \InvalidArgumentException('Invalid task type: ' . $taskType);
        }
    }

    /**
     * Generate a trial for the Smells to Music task.
     * Pick a random song from flavor A, then pick a random second flavor B.
     * 
     * @param Flavor[] $flavors
     * @return array
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
     * Generate a trial for the Musics to Smell task.
     * Pick two different songs from two different flavors.
     * 
     * @param Flavor[] $flavors
     * @return array
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
     * @return array
     */
    public function getAllPossibleCombinations(): array
    {
        $flavors = $this->flavorRepository->findAll();
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
