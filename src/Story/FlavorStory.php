<?php

namespace App\Story;

use App\Factory\Stimulus\FlavorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use Zenstruck\Foundry\Story;

final class FlavorStory extends Story
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function build(): void
    {
        $repository = $this->em->getRepository(Translation::class);

        FlavorFactory::createSequence([
            ['name' => 'Lemon', 'icon' => '🍋'],
            ['name' => 'Caramel', 'icon' => '🍬'],
            ['name' => 'Coffee', 'icon' => '☕'],
            ['name' => 'Apple', 'icon' => '🍏'],
        ]);

        $repository
            ->translate(FlavorFactory::find(['name' => 'Lemon']), 'name', 'it', 'Limone')
            ->translate(FlavorFactory::find(['name' => 'Caramel']), 'name', 'it', 'Caramello')
            ->translate(FlavorFactory::find(['name' => 'Coffee']), 'name', 'it', 'Caffè')
            ->translate(FlavorFactory::find(['name' => 'Apple']), 'name', 'it', 'Mela')
        ;
        $this->em->flush();
    }
}
