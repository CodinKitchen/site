<?php

namespace App\Controller\Admin;

use App\Entity\DynamicForm;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DynamicFormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DynamicForm::class;
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
