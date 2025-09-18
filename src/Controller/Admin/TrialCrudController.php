<?php

namespace App\Controller\Admin;

use App\Entity\Trial\Trial;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TrialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trial::class;
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
