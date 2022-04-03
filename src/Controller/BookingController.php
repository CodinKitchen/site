<?php

namespace App\Controller;

use App\Dto\MeetingRequestDto;
use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingRequestType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class BookingController extends AbstractController
{
    private const CONFIRM_REDIRECT_TIMING = 10;

    #[Route('/book', name: 'book')]
    public function index(
        Request $request,
        WorkflowInterface $meetingStateMachine,
    ): Response {
        $meetingRequest = new MeetingRequestDto();
        $form = $this->createForm(MeetingRequestType::class, $meetingRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $meeting = $meetingRequest->toMeeting();
            $meeting->setAttendee($user);
            /** @var int $price */
            $price = $this->getParameter('meeting.price');
            $meeting->setPrice($price);
            $meetingStateMachine->apply($meeting, 'request', ['paymentMethod' => $meetingRequest->getPaymentMethod()]);

            return $this->redirectToRoute('confirm', ['paymentReference' => $meeting->getPaymentReference()]);
        }

        return $this->renderForm(
            '/booking/book.html.twig',
            [
                'meetingForm' => $form,
            ]
        );
    }

    #[Route('/confirm/{paymentReference}', name: 'confirm')]
    public function confirm(
        Meeting $meeting,
        StripeClient $stripeClient,
        WorkflowInterface $meetingStateMachine,
    ): Response {
        if ($meeting->getStatus() !== Meeting::STATUS_UNPAYED) {
            return $this->redirectToRoute('home');
        }

        try {
            /** @var string $paymentReference */
            $paymentReference = $meeting->getPaymentReference();
            $paymentIntent = $stripeClient->paymentIntents->retrieve($paymentReference);
        } catch (Exception $e) {
            $paymentIntent = null;
        }

        if ($paymentIntent === null || $paymentIntent->status !== PaymentIntent::STATUS_REQUIRES_CAPTURE) {
            return $this->redirectToRoute('home');
        }

        $meetingStateMachine->apply($meeting, 'prepay');

        $this->addFlash('success', [
            'message' => 'flash.meeting.request',
            'params' => [
                'date' => $meeting->getTimeSlot()?->format('d/m/Y'),
                'time' => $meeting->getTimeSlot()?->format('H:i')
            ],
        ]);

        $response = $this->render('booking/confirm.html.twig');
        $response->headers->set('Refresh', sprintf('%d; url=%s', self::CONFIRM_REDIRECT_TIMING, $this->generateUrl('home')));

        return $response;
    }
}
