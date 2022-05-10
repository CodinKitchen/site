<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use Doctrine\ORM\EntityRepository;
use App\Service\Meeting\MeetingService;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Workflow\WorkflowInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Workflow\Transition;

class MeetingCrudController extends AbstractCrudController
{
    public function __construct(
        private MeetingService $meetingService,
        private WorkflowInterface $meetingStateMachine,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['timeSlot' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var Transition $transition */
        foreach ($this->meetingStateMachine->getDefinition()->getTransitions() as $transition) {
            $workflowAction = Action::new($transition->getName(), $transition->getName())
                ->displayIf(fn(Meeting $meeting) => $this->meetingStateMachine->can($meeting, $transition->getName()))
                ->linkToUrl(function (Meeting $meeting) use ($transition) {
                    return $this->adminUrlGenerator
                        ->unsetAll()
                        ->setEntityId($meeting->getId())
                        ->setController(self::class)
                        ->setAction('applyWorkflow')
                        ->set('transition', $transition->getName())
                        ->generateUrl();
                });
            $actions->add(Crud::PAGE_EDIT, $workflowAction);
            $actions->add(Crud::PAGE_INDEX, $workflowAction);
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('status')->setDisabled()->onlyWhenUpdating();
        yield DateTimeField::new('timeSlot');
        yield IntegerField::new('duration');
        yield TextareaField::new('note');
        yield AssociationField::new('attendee')
            ->setFormTypeOption('placeholder', 'Choose attendee')
            ->setFormTypeOption('query_builder', function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->andWhere("JSON_GET_PATH_TEXT(u.roles, '{}') LIKE :role_attendee")
                    ->setParameter('role_attendee', '%ROLE_ATTENDEE%')
                    ->orderBy('u.firstname', 'ASC');
            });
    }

    public function applyWorkflow(AdminContext $context): Response
    {
        /** @var Meeting $meeting */
        $meeting = $context->getEntity()->getInstance();

        $this->meetingStateMachine->apply($meeting, $context->getRequest()->query->get('transition'));

        return $this->redirect($this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }

    public function joinMeeting(AdminContext $context): Response
    {
        /** @var Meeting $meeting */
        $meeting = $context->getEntity()->getInstance();

        return $this->redirect($this->meetingService->join($meeting, true));
    }
}
