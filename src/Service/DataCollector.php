<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Stimulus\StimulusInterface;
use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\TrialInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This service is responsible to collect and store data about the experiment.
 * Its main purpose is to record the votes of participants for each trial.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
readonly class DataCollector
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param StimulusInterface $choice
     * @param TrialInterface $trial
     * @return void
     */
    public function recordVote(StimulusInterface $choice, TrialInterface $trial): void
    {
        $trial->setChoice($choice);
        $this->entityManager->flush();
    }

    /**
     * Deletes all trials that have no recorded choice.
     *
     * @return void
     */
    public function deleteEmptyTrials(): void
    {
        $f2m = $this->entityManager->getRepository(FlavorToMusicTrial::class)->findBy(['choice' => null]);
        $m2f = $this->entityManager->getRepository(MusicToFlavorTrial::class)->findBy(['choice' => null]);

        foreach (array_merge($f2m, $m2f) as $trial) {
            $this->entityManager->remove($trial);
        }

        $this->entityManager->flush();
    }
}
