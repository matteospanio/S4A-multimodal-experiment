<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $prompt = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $expectedFlavor = [];

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flavor $flavor = null;

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

    public function getExpectedFlavor(): array
    {
        return $this->expectedFlavor;
    }

    public function setExpectedFlavor(array $expectedFlavor): static
    {
        $this->expectedFlavor = $expectedFlavor;

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
}
