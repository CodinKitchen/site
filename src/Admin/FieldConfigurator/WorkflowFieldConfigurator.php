<?php

namespace App\Admin\FieldConfigurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use InvalidArgumentException;
use Recurr\Rule;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Workflow\Registry;

class WorkflowFieldConfigurator implements FieldConfiguratorInterface
{
    public function __construct(private Registry $workflowRegistry)
    {
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return $field->getFieldFqcn() === ChoiceField::class && $field->getCustomOption('is_workflow');
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $subject = $entityDto->getInstance();
        if (!$this->workflowRegistry->has($subject)) {
            throw new InvalidArgumentException('No workflow for this type of object');
        }

        if ($field->getFieldFqcn() !== ChoiceField::class) {
            throw new InvalidArgumentException('Input must be a ChoiceField');
        }

        $workflow = $this->workflowRegistry->get($subject);
        $choices = [];

        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            $choices[$transition->getName()] = $transition->getName();
        }

        $field->setCustomOption('choices', $choices);
    }
}
