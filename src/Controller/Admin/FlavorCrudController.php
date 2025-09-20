<?php

namespace App\Controller\Admin;

use App\Entity\Stimulus\Flavor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FlavorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flavor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('icon'),
            TextEditorField::new('description'),
        ];
    }
}
