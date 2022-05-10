<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use App\Service\Meeting\MeetingService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\WorkflowInterface;

class MeetingCrudController extends AbstractCrudController
{
    public function __construct(
        private MeetingService $meetingService,
        private WorkflowInterface $meetingStateMachine
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $joinMeeting = Action::new('joinMeeting', 'Join')
            ->linkToCrudAction('joinMeeting');
        $actions->add(Crud::PAGE_INDEX, $joinMeeting);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('status')->setDisabled();
        yield DateTimeField::new('timeSlot');
        yield IntegerField::new('duration');
        yield TextareaField::new('note');
        yield AssociationField::new('attendee');
    }

    public function joinMeeting(AdminContext $context): Response
    {
        $meeting = $context->getEntity()->getInstance();

        return $this->redirect($this->meetingService->join($meeting, true));
    }
}
