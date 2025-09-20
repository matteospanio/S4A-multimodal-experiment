<?php

namespace App\Controller\Admin;

use App\Entity\Stimulus\Song;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class SongCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Song::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextEditorField::new('prompt'),
            AssociationField::new('flavor'),
            UrlField::new('url'),
        ];
    }
}
