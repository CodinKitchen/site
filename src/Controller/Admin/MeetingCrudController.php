<?php

namespace App\Controller\Admin;

use App\Entity\Enum\MeetingStatus;
use App\Entity\Meeting;
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
        return [
            ChoiceField::new('status')->setFormType(EnumType::class)->setFormTypeOption('class', MeetingStatus::class),
            DateField::new('timeSlot'),
            IntegerField::new('duration'),
        ];
    }
}
