<?php

namespace App\Service;

use App\Entity\Stimulus\StimulusInterface;
use App\Entity\Trial\TrialInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This service is responsible to collect and store data about the experiment.
 * Its main purpose is to record the votes of participants for each trial.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
class DataCollector
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
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
}
