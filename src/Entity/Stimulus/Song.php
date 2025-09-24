<?php

namespace App\Entity\Stimulus;

use App\Entity\Trial\MusicToFlavorTrial;
use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song implements StimulusInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $prompt = null;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flavor $flavor = null;

    /**
     * @var Collection<int, MusicToFlavorTrial>
     */
    #[ORM\ManyToMany(targetEntity: MusicToFlavorTrial::class, mappedBy: 'songs')]
    private Collection $musicToFlavorTrials;

    public function __construct()
    {
        $this->musicToFlavorTrials = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s #%d', $this->flavor, $this->id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): static
    {
        $this->prompt = $prompt;

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
            $musicToFlavorTrial->addSong($this);
        }

        return $this;
    }

    public function removeMusicToFlavorTrial(MusicToFlavorTrial $musicToFlavorTrial): static
    {
        if ($this->musicToFlavorTrials->removeElement($musicToFlavorTrial)) {
            $musicToFlavorTrial->removeSong($this);
        }

        return $this;
    }

    public function getUrlPath(): ?string
    {
        return '/uploads/' . basename((string) $this->url);
    }
}
