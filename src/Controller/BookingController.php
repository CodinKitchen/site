<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class BookingController extends AbstractController
{
    // #[Route('/book', name: 'book')]
    // #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        WorkflowInterface $meetingStateMachine
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $meeting = new Meeting();
        $meeting->setStatus(Meeting::STATUS_DRAFT);
        $meeting->setAttendee($user);

        $form = $this->createForm(MeetingType::class, $meeting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meetingStateMachine->apply($meeting, 'request');

            $this->addFlash('success', [
                    'message' => 'flash.meeting.request',
                    'params' => [
                        'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                        'time' => $meeting->getTimeSlot()?->format('H:i')
                    ],
            ]);

            return $this->render('booking/confirm.html.twig');
        }

        return $this->renderForm(
            '/booking/book.html.twig',
            [
                'meetingForm' => $form,
            ]
        );
    }
}
