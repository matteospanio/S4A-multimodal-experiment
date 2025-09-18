<?php

namespace App\Controller\Admin;

use App\Entity\Stimulus\Flavor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FlavorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flavor::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
