<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\Trial;
use App\Repository\FlavorRepository;
use App\Repository\SongRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

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
        private SongRepository   $songRepository,
        private EntityManagerInterface $entityManager,
        private TaskRepository $taskRepository,
    ) {
    }

    /**
     * Get the next trial for a participant based on balanced combination logic.
     *
     * @template T of Trial
     * @param string $taskType Either Trial::SMELLS2MUSIC or Trial::MUSICS2SMELL
     * @param array $usedCombinations
     * @return T Trial data with stimuli information
     */
    public function getNextTrial(string $taskType, array $usedCombinations = []): Trial
    {
        $flavors = $this->flavorRepository->findAll();
        $songs = $this->songRepository->findAll();

        if (count($flavors) < 2 || count($songs) < 2) {
            throw new \RuntimeException('Servono almeno 2 profumi e 2 canzoni.');
        }

        return match ($taskType) {
            Trial::SMELLS2MUSIC => $this->persistTrial($this->generateFlavorToMusicTrial($flavors, $songs, $usedCombinations)),
            Trial::MUSICS2SMELL => $this->persistTrial($this->generateMusicToFlavorTrial($flavors, $songs, $usedCombinations)),
            default => throw new \InvalidArgumentException('Tipo task non valido: ' . $taskType),
        };
    }

    /**
     * Genera un trial Flavor→Music (2 profumi, 1 canzone).
     */
    private function generateFlavorToMusicTrial(array $flavors, array $songs, array $usedCombinations): FlavorToMusicTrial
    {
        $task = $this->taskRepository->findOneBy(['type' => Trial::SMELLS2MUSIC]);

        $allCombos = $this->getAllFlavorToMusicCombinations($flavors, $songs);
        $available = $this->filterUnusedCombinations($allCombos, $usedCombinations);

        if ($available === []) {
            // Tutte le combinazioni già usate: si ricomincia
            $available = $allCombos;
        }

        $combo = $available[array_rand($available)];

        // Randomizza ordine dei profumi
        $flavorObjs = [$combo['flavor1'], $combo['flavor2']];
        shuffle($flavorObjs);

        $trial = new FlavorToMusicTrial();
        $trial->addFlavor($flavorObjs[0]);
        $trial->addFlavor($flavorObjs[1]);
        $trial->setSong($combo['song']);
        $trial->setTask($task);

        return $trial;
    }

    /**
     * Genera un trial Music→Flavor (2 canzoni, 1 profumo).
     * @param Flavor[] $flavors
     * @param Song[] $songs
     */
    private function generateMusicToFlavorTrial(array $flavors, array $songs, array $usedCombinations): MusicToFlavorTrial
    {
        $task = $this->taskRepository->findOneBy(['type' => Trial::MUSICS2SMELL]);

        $allCombos = $this->getAllMusicToFlavorCombinations($flavors, $songs);
        $available = $this->filterUnusedCombinations($allCombos, $usedCombinations);

        if ($available === []) {
            $available = $allCombos;
        }

        $combo = $available[array_rand($available)];

        // Randomizza ordine delle canzoni
        $songObjs = [$combo['song1'], $combo['song2']];
        shuffle($songObjs);

        $trial = new MusicToFlavorTrial();
        $trial->addSong($songObjs[0]);
        $trial->addSong($songObjs[1]);
        $trial->setFlavor($combo['flavor']);
        $trial->setTask($task);

        return $trial;
    }

    /**
     * Restituisce tutte le combinazioni possibili per Flavor→Music.
     * Ogni combinazione: 2 profumi distinti, 1 canzone il cui intendedFlavor è uno dei due.
     * @param Flavor[] $flavors
     * @param Song[] $songs
     * @return array [['flavor1'=>Flavor, 'flavor2'=>Flavor, 'song'=>Song, 'comboKey'=>string], ...]
     */
    public function getAllFlavorToMusicCombinations(array $flavors, array $songs): array
    {
        $combos = [];
        foreach ($flavors as $f1) {
            foreach ($flavors as $f2) {
                if ($f1 === $f2) {
                    continue;
                }

                foreach ($songs as $song) {
                    $intended = $song->getFlavor();
                    if ($intended && ($intended === $f1 || $intended === $f2)) {
                        // Chiave unica per la combinazione (id profumi ordinati + id canzone)
                        $ids = [$f1->getId(), $f2->getId()];
                        sort($ids);
                        $comboKey = implode('-', $ids) . '-' . $song->getId();
                        $combos[] = [
                            'flavor1' => $f1,
                            'flavor2' => $f2,
                            'song' => $song,
                            'comboKey' => $comboKey
                        ];
                    }
                }
            }
        }

        return $combos;
    }

    /**
     * Restituisce tutte le combinazioni possibili per Music→Flavor.
     * Ogni combinazione: 2 canzoni con intendedFlavor diversi, 1 profumo che corrisponde a uno dei due.
     * @param Flavor[] $flavors
     * @param Song[] $songs
     * @return array [['song1'=>Song, 'song2'=>Song, 'flavor'=>Flavor, 'comboKey'=>string], ...]
     */
    public function getAllMusicToFlavorCombinations(array $flavors, array $songs): array
    {
        $combos = [];
        foreach ($songs as $s1) {
            foreach ($songs as $s2) {
                if ($s1 === $s2) {
                    continue;
                }

                $f1 = $s1->getFlavor();
                $f2 = $s2->getFlavor();
                if (!$f1) {
                    continue;
                }

                if (!$f2) {
                    continue;
                }

                if ($f1 === $f2) {
                    continue;
                }

                foreach ([$f1, $f2] as $flavor) {
                    // Chiave unica per la combinazione (id canzoni ordinati + id profumo)
                    $ids = [$s1->getId(), $s2->getId()];
                    sort($ids);
                    $comboKey = implode('-', $ids) . '-' . $flavor->getId();
                    $combos[] = [
                        'song1' => $s1,
                        'song2' => $s2,
                        'flavor' => $flavor,
                        'comboKey' => $comboKey
                    ];
                }
            }
        }

        return $combos;
    }

    /**
     * Filtra le combinazioni già usate.
     * @param array $allCombos
     * @param array $usedCombinations array di comboKey già usate
     * @return array
     */
    private function filterUnusedCombinations(array $allCombos, array $usedCombinations): array
    {
        return array_values(array_filter($allCombos, fn(array $c): bool => !in_array($c['comboKey'], $usedCombinations, true)));
    }

    /**
     * Helper: restituisce tutte le chiavi delle combinazioni possibili per il task richiesto.
     * @param string $taskType
     * @return string[]
     */
    public function getAllCombinationKeys(string $taskType): array
    {
        $flavors = $this->flavorRepository->findAll();
        $songs = $this->songRepository->findAll();
        return match ($taskType) {
            Trial::SMELLS2MUSIC => array_column($this->getAllFlavorToMusicCombinations($flavors, $songs), 'comboKey'),
            Trial::MUSICS2SMELL => array_column($this->getAllMusicToFlavorCombinations($flavors, $songs), 'comboKey'),
            default => [],
        };
    }

    /**
     * Persists a trial to the database and returns it.
     */
    private function persistTrial(Trial $trial): Trial
    {
        $this->entityManager->persist($trial);
        $this->entityManager->flush();
        return $trial;
    }
}
