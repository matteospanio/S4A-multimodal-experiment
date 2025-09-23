<?php

namespace App\Controller\Admin;

use App\Entity\Trial\MusicToFlavorTrial;
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

class MusicToFlavorTrialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MusicToFlavorTrial::class;
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
                TextField::new('intendedSong'),
                BooleanField::new('doesMatch')->renderAsSwitch(false),
            ], $common);
        }

        return array_merge([
            CollectionField::new('songs'),
            AssociationField::new('flavor'),
            AssociationField::new('choice'),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ], $common
        );
    }
}
