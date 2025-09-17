<?php

namespace App\Service;

use App\Entity\Participant;

/**
 * This service is responsible to manage the stimuli presentation logic.
 * Its main purpose is to decide which trial should be presented to the participant.
 *
 * @author Matteo Spanio <spanio@dei.unipd.it>
 */
class StimuliManager
{
    public function getNextTrial(Participant $participant)
    {
        // Logic to determine the next trial for the participant
    }
}
