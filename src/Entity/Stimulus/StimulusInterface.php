<?php

declare(strict_types=1);

namespace App\Entity\Stimulus;

interface StimulusInterface
{
    public function getId(): ?int;

    public function __toString(): string;
}
