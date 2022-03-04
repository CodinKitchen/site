<?php

namespace App\Admin\FieldConfigurator;

use App\Form\RruleType;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Recurr\Rule;

class RruleFieldConfigurator implements FieldConfiguratorInterface
{

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return $field->getFormType() === RruleType::class && $field->getValue() instanceof Rule;
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $field->setValue($field->getValue()->getString());
    }
}
