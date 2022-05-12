<?php

namespace App\EventSubscriber\Admin;

use App\Entity\Meeting;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class MeetingSubscriber implements EventSubscriberInterface
{
    public function __construct(private WorkflowInterface $meetingStateMachine)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['autoValidateMeeting'],
        ];
    }

    public function autoValidateMeeting(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Meeting)) {
            return;
        }

        $this->meetingStateMachine->apply($entity, 'auto_validate');
    }
}
