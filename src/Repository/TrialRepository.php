<?php

namespace App\Repository;

use App\Entity\Trial\Trial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trial>
 */
class TrialRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly FlavorToMusicTrialRepository $flavorToMusicTrialRepository,
        private readonly MusicToFlavorTrialRepository $musicToFlavorTrialRepository,
    )
    {
        parent::__construct($registry, Trial::class);
    }

    public function countAllIncompleteTrials(): int
    {
        return $this->flavorToMusicTrialRepository->countIncompleteTrials() +
               $this->musicToFlavorTrialRepository->countIncompleteTrials();
    }
}
