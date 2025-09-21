<?php

namespace App\Service;

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
}
