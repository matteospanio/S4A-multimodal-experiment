<?php

declare(strict_types=1);

namespace App\Entity\Trial;

use App\Entity\Task;
use App\Repository\TrialRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TrialRepository::class)]
#[ORM\Table(name: 'trial')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'flavor2music' => FlavorToMusicTrial::class,
    'music2flavor' => MusicToFlavorTrial::class,
])]
class Trial implements \Stringable
{
    use TimestampableEntity;

    const string SMELLS2MUSIC = 'smells2music';

    const string MUSICS2SMELL = 'musics2smell';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'trials')]
    private ?Task $task = null;

    public function __toString(): string
    {
        return 'Trial #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getTimeInterval(): ?string
    {
        if ($this->getCreatedAt() && $this->getUpdatedAt()) {
            return $this->getUpdatedAt()->diff($this->getCreatedAt())->format('%H:%I:%S');
        }

        return null;
    }
}
