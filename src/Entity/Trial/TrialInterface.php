<?php

namespace App\Entity\Trial;

use App\Entity\Stimulus\StimulusInterface;

/**
 * @template S of StimulusInterface
 */
interface TrialInterface
{
    public function getId(): ?int;

    /**
     * @return S|null
     */
    public function getChoice(): ?StimulusInterface;

    /**
     * @param S|null $choice
     */
    public function setChoice(?StimulusInterface $choice): static;

    public function doesMatch(): ?bool;
}
