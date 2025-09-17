<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Trial;

/**
 * This service is responsible to collect and store data about the experiment.
 * Its main purpose is to record the votes of participants for each trial.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
class DataCollector
{
    public function recordVote(Participant $participant, Trial $trial): void
    {
        // Logic to record the vote of the participant for the given trial
    }
}
