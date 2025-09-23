<?php

namespace App\Entity\Trial;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Stimulus\StimulusInterface;
use App\Repository\MusicToFlavorTrialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @implements TrialInterface<Song>
 */
#[ORM\Entity(repositoryClass: MusicToFlavorTrialRepository::class)]
class MusicToFlavorTrial extends Trial implements TrialInterface
{
    /**
     * @var Collection<int, Song>
     */
    #[ORM\ManyToMany(targetEntity: Song::class, inversedBy: 'musicToFlavorTrials')]
    private Collection $songs;

    #[ORM\ManyToOne(inversedBy: 'musicToFlavorTrials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flavor $flavor = null;

    #[ORM\ManyToOne]
    private ?Song $choice = null;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
        }

        return $this;
    }

    public function removeSong(Song $song): static
    {
        $this->songs->removeElement($song);

        return $this;
    }

    public function getFlavor(): ?Flavor
    {
        return $this->flavor;
    }

    public function setFlavor(?Flavor $flavor): static
    {
        $this->flavor = $flavor;

        return $this;
    }

    public function getChoice(): ?Song
    {
        return $this->choice;
    }

    /**
     * @param Song|null $choice
     */
    public function setChoice(?StimulusInterface $choice): static
    {
        $this->choice = $choice;

        return $this;
    }

    public function doesMatch(): ?bool
    {
        if ($this->choice === null || $this->flavor === null) {
            return null;
        }

        return $this->choice->getFlavor() === $this->flavor;
    }
}
