<?php

namespace App\Controller\Admin;

use App\Entity\ScheduleRule;
use App\Form\RruleType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ScheduleRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ScheduleRule::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('rule')
                ->setFormType(RruleType::class),
        ];
    }
}
