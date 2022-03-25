<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('status')->setDisabled(),
            ChoiceField::new('action')
                ->setChoices(['draft'])
                ->onlyWhenUpdating()
                ->setFormTypeOption('mapped', false)
                ->setCustomOption('is_workflow', true),
            DateTimeField::new('timeSlot')->setDisabled(),
            IntegerField::new('duration'),
            TextareaField::new('note'),
            MoneyField::new('price')
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->setDisabled(),
            TextField::new('paymentReference')->setDisabled(),
        ];
    }
}
