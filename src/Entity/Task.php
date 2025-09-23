<?php

namespace App\Entity;

use App\Entity\Trial\Trial;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Experiment $experiment = null;

    /**
     * @var Collection<int, Trial>
     */
    #[ORM\OneToMany(targetEntity: Trial::class, mappedBy: 'task', cascade: ['persist'])]
    private Collection $trials;

    public function __construct()
    {
        $this->trials = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getExperiment(): ?Experiment
    {
        return $this->experiment;
    }

    public function setExperiment(?Experiment $experiment): static
    {
        $this->experiment = $experiment;

        return $this;
    }

    /**
     * @return Collection<int, Trial>
     */
    public function getTrials(): Collection
    {
        return $this->trials;
    }

    public function addTrial(Trial $trial): static
    {
        if (!$this->trials->contains($trial)) {
            $this->trials->add($trial);
            $trial->setTask($this);
        }

        return $this;
    }

    public function removeTrial(Trial $trial): static
    {
        if ($this->trials->removeElement($trial)) {
            // set the owning side to null (unless already changed)
            if ($trial->getTask() === $this) {
                $trial->setTask(null);
            }
        }

        return $this;
    }
}
