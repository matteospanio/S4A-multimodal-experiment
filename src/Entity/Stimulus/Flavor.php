<?php

namespace App\Entity\Stimulus;

use App\Entity\Trial\MusicToFlavorTrial;
use App\Repository\FlavorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlavorRepository::class)]
class Flavor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Song>
     */
    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'flavor')]
    private Collection $songs;

    /**
     * @var Collection<int, MusicToFlavorTrial>
     */
    #[ORM\OneToMany(targetEntity: MusicToFlavorTrial::class, mappedBy: 'flavor')]
    private Collection $musicToFlavorTrials;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->musicToFlavorTrials = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
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
            $song->setFlavor($this);
        }

        return $this;
    }

    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getFlavor() === $this) {
                $song->setFlavor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MusicToFlavorTrial>
     */
    public function getMusicToFlavorTrials(): Collection
    {
        return $this->musicToFlavorTrials;
    }

    public function addMusicToFlavorTrial(MusicToFlavorTrial $musicToFlavorTrial): static
    {
        if (!$this->musicToFlavorTrials->contains($musicToFlavorTrial)) {
            $this->musicToFlavorTrials->add($musicToFlavorTrial);
            $musicToFlavorTrial->setFlavor($this);
        }

        return $this;
    }

    public function removeMusicToFlavorTrial(MusicToFlavorTrial $musicToFlavorTrial): static
    {
        if ($this->musicToFlavorTrials->removeElement($musicToFlavorTrial)) {
            // set the owning side to null (unless already changed)
            if ($musicToFlavorTrial->getFlavor() === $this) {
                $musicToFlavorTrial->setFlavor(null);
            }
        }

        return $this;
    }
}
