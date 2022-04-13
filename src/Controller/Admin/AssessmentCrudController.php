<?php

namespace App\Controller\Admin;

use App\Entity\Assessment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AssessmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Assessment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code'),
            TextField::new('name'),
            ImageField::new('image')
                ->setBasePath('uploads/images/')
                ->setUploadDir('public/uploads/images'),
            AssociationField::new('form'),
        ];
    }
}
