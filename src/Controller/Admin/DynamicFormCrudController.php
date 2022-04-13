<?php

namespace App\Controller\Admin;

use App\Entity\DynamicForm;
use App\Form\Admin\DynamicFormInputType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DynamicFormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DynamicForm::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            CollectionField::new('inputs')
                ->setEntryType(DynamicFormInputType::class)
                ->allowAdd()
                ->allowDelete()
                ->setCustomOption('by_reference', false),
        ];
    }
}
