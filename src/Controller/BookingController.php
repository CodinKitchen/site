<?php

namespace App\Controller;

use App\Entity\Enum\MeetingStatus;
use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingType;
use App\Notification\AttendeeNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\Notification\NotificationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/book', name: 'book')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        NotifierInterface $notifier,
        NotificationFactory $notificationFactory
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $meeting = new Meeting();
        $meeting->setStatus(MeetingStatus::DRAFT);
        $meeting->setAttendee($user);

        $form = $this->createForm(MeetingType::class, $meeting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification = $notificationFactory->createNotification(
                AttendeeNotification::class,
                'notification.booking.request.subject',
                'notification.booking.request.content',
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
            $recipient = new Recipient(
                $user->getUserIdentifier(),
            );

            // Send the notification to the recipient
            $notifier->send($notification, $recipient);
        }

        return $this->renderForm(
            'book.html.twig',
            [
                'meetingForm' => $form,
            ]
        );
    }
}
