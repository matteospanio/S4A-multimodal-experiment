<?php

namespace App\Entity;

use App\Repository\TrialRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrialRepository::class)]
class Trial
{
    const SMELLS2MUSIC = 'smells2music';
    const MUSICS2SMELL = 'musics2smell';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $taskType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskType(): ?string
    {
        return $this->taskType;
    }

    public function setTaskType(string $taskType): static
    {
        $this->taskType = $taskType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
