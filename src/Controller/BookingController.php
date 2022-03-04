<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Form\MeetingFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/book', name: 'book')]
    public function index(Request $request): Response
    {
        $meeting = new Meeting();
        $meeting->setStatus(Meeting::STATUS_DRAFT);
        $meeting->setAttendee($this->getUser());

        $form = $this->createForm(MeetingFormType::class, $meeting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('meeting form valid');
        }

        return $this->render(
            'book.html.twig',
            [
                'meetingForm' => $form->createView(),
            ]
        );
    }
}
