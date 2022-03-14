<?php

namespace App\Controller\Admin;

use App\Entity\Enum\MeetingStatus;
use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class MeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $choiceField = ChoiceField::new('status')
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', MeetingStatus::class)
            ->setChoices(MeetingStatus::cases());

        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            $choiceField->setChoices(array_reduce(
                MeetingStatus::cases(),
                static fn (array $choices, MeetingStatus $status) => $choices + [$status->name => $status->value],
                [],
            ));
        }
        return [
            $choiceField,
            DateField::new('timeSlot'),
            IntegerField::new('duration'),
        ];
    }
}
