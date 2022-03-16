<?php

namespace App\EventSubscriber;

use App\Controller\Admin\MeetingCrudController;
use App\Entity\Meeting;
use App\Entity\User;
use App\Notification\AdminNotification;
use App\Notification\AttendeeNotification;
use App\Service\Notification\NotificationFactory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\Event;

class MeetingSubscriber implements EventSubscriberInterface
{
    /**
     * @param Notifier $notifier
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotifierInterface $notifier,
        private NotificationFactory $notificationFactory,
        private Security $security,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.meeting.completed.request' => [
                ['persistMeeting', 20],
                ['onMeetingRequestAttendeeEmail', 10],
                ['onMeetingRequestAdminEmail', 0],
            ]
        ];
    }

    public function persistMeeting(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        $this->entityManager->persist($meeting);
        $this->entityManager->flush();
    }

    public function onMeetingRequestAttendeeEmail(Event $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user === null) {
            return;
        }

        /** @var Meeting $meeting */
        $meeting = $event->getSubject();

        $notification = $this->notificationFactory->createNotification(
            AttendeeNotification::class,
            'notification.booking.request.attendee.subject',
            'notification.booking.request.attendee.content',
            ['email'],
            [
                'firstname' => $user->getFirstname()
            ],
            [
                'firstname' => $user->getFirstname(),
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i'),
                'duration' => $meeting->getDuration(),
            ],
        );

        // The receiver of the Notification
        $recipient = new Recipient($user->getUserIdentifier());

        // Send the notification to the recipient
        $this->notifier->send($notification, $recipient);
    }

    public function onMeetingRequestAdminEmail(Event $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user === null) {
            return;
        }

        /** @var Meeting $meeting */
        $meeting = $event->getSubject();

        /** @var AdminNotification $notification */
        $notification = $this->notificationFactory->createNotification(
            AdminNotification::class,
            'notification.booking.request.admin.subject',
            'notification.booking.request.admin.content',
            ['email'],
            [
                'firstname' => $user->getFirstname()
            ],
            [
                'firstname' => $user->getFirstname(),
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i'),
                'duration' => $meeting->getDuration(),
            ],
        );

        $adminEditUrl = $this->adminUrlGenerator
            ->setController(MeetingCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($meeting->getId())
            ->generateUrl();

        $notification->setAction('notification.booking.request.admin.action', $adminEditUrl);
        $notification->importance(Notification::IMPORTANCE_HIGH);

        // Send the notification to the recipient
        $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
    }
}
