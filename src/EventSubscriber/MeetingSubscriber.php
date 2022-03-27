<?php

namespace App\EventSubscriber;

use App\Controller\Admin\MeetingCrudController;
use App\Entity\Meeting;
use App\Notification\AdminNotification;
use App\Notification\AttendeeNotification;
use App\Service\Notification\NotificationFactory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
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
        private AdminUrlGenerator $adminUrlGenerator,
        private StripeClient $stripeClient,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.meeting.completed.request' => [
                ['createPaymentIntent', 20],
                ['persistMeeting', 10],
            ],
            'workflow.meeting.completed.prepay' => [
                ['persistMeeting', 20],
                ['onMeetingRequestAttendeeEmail', 10],
                ['onMeetingRequestAdminEmail', 0],
            ],
            'workflow.meeting.completed.confirm' => [
                ['onMeetingConfirmedCapture', 30],
                ['persistMeeting', 20],
                ['onMeetingConfirmedAttendeeEmail', 10],
            ],
            'workflow.meeting.completed.cancel' => [
                ['onMeetingCanceledRefund', 30],
                ['persistMeeting', 20],
                ['onMeetingCanceledAttendeeEmail', 10],
            ],
        ];
    }

    public function createPaymentIntent(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();

        $paymentIntent = $this->stripeClient->paymentIntents->create([
            'amount' => $meeting->getPrice(),
            'currency' => 'eur',
            'confirmation_method' => 'manual',
            'payment_method' => $event->getContext()['paymentMethod'] ?? null,
            'confirm' => true,
        ]);

        $meeting->setPaymentReference($paymentIntent->id);
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
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        if (($attendee = $meeting->getAttendee()) === null) {
            return;
        }

        $notification = $this->notificationFactory->createNotification(
            AttendeeNotification::class,
            'notification.booking.request.attendee.subject',
            'notification.booking.request.attendee.content',
            ['email'],
            [
                'firstname' => $attendee->getFirstname()
            ],
            [
                'firstname' => $attendee->getFirstname(),
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i'),
                'duration' => $meeting->getDuration(),
            ],
        );

        // The receiver of the Notification
        $recipient = new Recipient($attendee->getUserIdentifier());

        // Send the notification to the recipient
        $this->notifier->send($notification, $recipient);
    }

    public function onMeetingRequestAdminEmail(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        if (($attendee = $meeting->getAttendee()) === null) {
            return;
        }

        /** @var AdminNotification $notification */
        $notification = $this->notificationFactory->createNotification(
            AdminNotification::class,
            'notification.booking.request.admin.subject',
            'notification.booking.request.admin.content',
            ['email'],
            [
                'firstname' => $attendee->getFirstname()
            ],
            [
                'firstname' => $attendee->getFirstname(),
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

    public function onMeetingConfirmedCapture(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        if ($meeting->getPaymentReference() === null) {
            $event->stopPropagation();
            return;
        }

        $paymentIntent = $this->stripeClient->paymentIntents->retrieve($meeting->getPaymentReference());
        $paymentIntent->capture();
    }

    public function onMeetingConfirmedAttendeeEmail(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        if (($attendee = $meeting->getAttendee()) === null) {
            return;
        }

        $notification = $this->notificationFactory->createNotification(
            AttendeeNotification::class,
            'notification.booking.confirmed.attendee.subject',
            'notification.booking.confirmed.attendee.content',
            ['email'],
            [
                'firstname' => $attendee->getFirstname()
            ],
            [
                'firstname' => $attendee->getFirstname(),
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i'),
                'duration' => $meeting->getDuration(),
            ],
        );

        // The receiver of the Notification
        $recipient = new Recipient($attendee->getUserIdentifier());

        // Send the notification to the recipient
        $this->notifier->send($notification, $recipient);
    }

    public function onMeetingCanceledRefund(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        try {
            if ($meeting->getPaymentReference() === null) {
                $event->stopPropagation();
                return;
            }
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($meeting->getPaymentReference());
            if ($paymentIntent->status === PaymentIntent::STATUS_REQUIRES_CAPTURE) {
                $paymentIntent->cancel();
                return;
            }
            $this->stripeClient->refunds->create(['payment_intent' => $meeting->getPaymentReference()]);
        } catch (Exception $exception) {
            $event->stopPropagation();
        }
    }

    public function onMeetingCanceledAttendeeEmail(Event $event): void
    {
        /** @var Meeting $meeting */
        $meeting = $event->getSubject();
        if (($attendee = $meeting->getAttendee()) === null) {
            return;
        }

        $notification = $this->notificationFactory->createNotification(
            AttendeeNotification::class,
            'notification.booking.canceled.attendee.subject',
            'notification.booking.request.attendee.content',
            ['email'],
            [
                'firstname' => $attendee->getFirstname()
            ],
            [
                'firstname' => $attendee->getFirstname(),
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i'),
                'duration' => $meeting->getDuration(),
            ],
        );

        // The receiver of the Notification
        $recipient = new Recipient($attendee->getUserIdentifier());

        // Send the notification to the recipient
        $this->notifier->send($notification, $recipient);
    }
}
