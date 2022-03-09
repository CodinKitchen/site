<?php

namespace App\Controller;

use App\Entity\Enum\MeetingStatus;
use App\Entity\Meeting;
use App\Form\MeetingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/book', name: 'book')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request, NotifierInterface $notifier): Response
    {
        $meeting = new Meeting();
        $meeting->setStatus(MeetingStatus::DRAFT);
        $meeting->setAttendee($this->getUser());

        $form = $this->createForm(MeetingType::class, $meeting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification = (new Notification('Votre demande de réservation', ['email']))
            ->content('J\'ai bien reçu votre demande de coaching pour le ' . $meeting->getTimeSlot()->format('d/m/Y H:i') . ' pour ' . $meeting->getDuration() . 'h. Je vous répond aussi vite que possible ! À bientôt :)');

            // The receiver of the Notification
            $recipient = new Recipient(
                $this->getUser()->getUserIdentifier(),
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
