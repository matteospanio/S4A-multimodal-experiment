<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\Attribute\AsMenuBuilder;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final readonly class MenuBuilder
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    #[AsMenuBuilder(name: 'main')]
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mb-4');

        $menu->addChild('Home', ['route' => 'app_home'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
        ;
        $menu->addChild('Admin', ['route' => 'admin'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
        ;

        return $menu;
    }
}
