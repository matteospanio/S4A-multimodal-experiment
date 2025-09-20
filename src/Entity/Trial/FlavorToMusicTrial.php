<?php

namespace App\Entity\Trial;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Stimulus\StimulusInterface;
use App\Repository\FlavorToMusicTrialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @implements TrialInterface<Flavor>
 */
#[ORM\Entity(repositoryClass: FlavorToMusicTrialRepository::class)]
class FlavorToMusicTrial extends Trial implements TrialInterface
{
    /**
     * @var Collection<int, Flavor>
     */
    #[ORM\ManyToMany(targetEntity: Flavor::class)]
    private Collection $flavors;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Song $song = null;

    #[ORM\ManyToOne]
    private ?Flavor $choice = null;

    public function __construct()
    {
        parent::__construct();
        $this->flavors = new ArrayCollection();
    }

    /**
     * @return Collection<int, Flavor>
     */
    public function getFlavors(): Collection
    {
        return $this->flavors;
    }

    public function addFlavor(Flavor $flavor): static
    {
        if (!$this->flavors->contains($flavor)) {
            $this->flavors->add($flavor);
        }

        return $this;
    }

    public function removeFlavor(Flavor $flavor): static
    {
        $this->flavors->removeElement($flavor);

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): static
    {
        $this->song = $song;

        return $this;
    }

    public function getChoice(): ?Flavor
    {
        return $this->choice;
    }

    /**
     * @param Flavor|null $choice
     */
    public function setChoice(?StimulusInterface $choice): static
    {
        $this->choice = $choice;
        return $this;
    }
}
