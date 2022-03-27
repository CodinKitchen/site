<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class MeetingCrudController extends AbstractCrudController
{
    public function __construct(private WorkflowInterface $meetingStateMachine)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('status')->setDisabled(),
            DateTimeField::new('timeSlot')->setDisabled(),
            IntegerField::new('duration'),
            TextareaField::new('note')->onlyOnForms(),
            MoneyField::new('price')
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->setDisabled(),
            TextField::new('paymentReference')
                ->setDisabled()
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $transitions = $this->meetingStateMachine->getDefinition()->getTransitions();
        /** @var string[] $transitions */
        $transitions = array_map(function (Transition $transition) {
            return $transition->getName();
        }, $transitions);
        $transitions = array_unique($transitions);

        foreach ($transitions as $transition) {
            $workflowAction = Action::new($transition)
                ->linkToCrudAction($transition)
                ->displayIf(fn (Meeting $meeting) => $this->meetingStateMachine->can($meeting, $transition));
            $actions->add(Crud::PAGE_INDEX, $workflowAction);
            $actions->add(Crud::PAGE_EDIT, $workflowAction);
        }

        return $actions;
    }

    public function request(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->applyWorkflow($context, $adminUrlGenerator, 'request');
    }

    public function pay(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->applyWorkflow($context, $adminUrlGenerator, 'prepay');
    }

    public function confirm(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->applyWorkflow($context, $adminUrlGenerator, 'confirm');
    }

    public function cancel(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->applyWorkflow($context, $adminUrlGenerator, 'cancel');
    }

    private function applyWorkflow(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        string $transition
    ): Response {
        /** @var Meeting $meeting */
        $meeting = $context->getEntity()->getInstance();
        if ($this->meetingStateMachine->can($meeting, $transition)) {
            $this->meetingStateMachine->apply($meeting, $transition);
        }

        $adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->removeReferrer();
        return $this->redirect($adminUrlGenerator);
    }
}
