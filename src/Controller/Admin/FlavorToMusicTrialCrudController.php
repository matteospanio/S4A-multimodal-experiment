<?php

namespace App\Controller\Admin;

use App\Entity\Trial\FlavorToMusicTrial;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FlavorToMusicTrialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FlavorToMusicTrial::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $common = [
            TextField::new('timeInterval')
        ];

        if ($pageName === Crud::PAGE_INDEX) {
            return array_merge([
                IdField::new('id'),
                TextField::new('intendedFlavor'),
                BooleanField::new('doesMatch')->renderAsSwitch(false),
            ], $common);
        }

        return array_merge([
            CollectionField::new('flavors'),
            AssociationField::new('song'),
            AssociationField::new('choice'),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
            ], $common
        );
    }
}
