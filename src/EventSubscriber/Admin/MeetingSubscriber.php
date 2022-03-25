<?php

namespace App\EventSubscriber\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class MeetingSubscriber implements EventSubscriberInterface
{
    public function __construct(private WorkflowInterface $meetingStateMachine)
    {
    }

    public function applyWorkflow(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Meeting)) {
            return;
        }

        dd($event);
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => 'applyWorkflow',
        ];
    }
}
