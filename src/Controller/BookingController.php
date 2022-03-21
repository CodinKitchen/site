<?php

namespace App\Controller;

use App\Dto\MeetingRequestDto;
use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingRequestType;
use App\Repository\MeetingRepository;
use Exception;
use LogicException;
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
            $meeting->setStatus(Meeting::STATUS_DRAFT);
            $meeting->setAttendee($user);
            $meetingStateMachine->apply($meeting, 'request', ['paymentMethod' => $meetingRequest->getPaymentMethod()]);

            return $this->redirectToRoute('payment', ['paymentReference' => $meeting->getPaymentReference()]);
        }

        return $this->renderForm(
            '/booking/book.html.twig',
            [
                'meetingForm' => $form,
            ]
        );
    }

    #[Route('/payment/{paymentReference}', name: 'payment')]
    public function payment(
        Meeting $meeting,
        StripeClient $stripeClient,
    ): Response {
        if ($meeting->getStatus() !== Meeting::STATUS_UNPAYED || $meeting->getAttendee() != $this->getUser()) {
            return $this->redirect('home');
        }

        try {
            if ($meeting->getPaymentReference() === null) {
                throw new LogicException('PaymentReference should not be null');
            }

            $paymentIntent = $stripeClient->paymentIntents->retrieve($meeting->getPaymentReference());
        } catch (Exception $e) {
            return $this->redirect('home');
        }

        return $this->render('booking/payment.html.twig', [
            'meeting' => $meeting,
            'paymentIntent' => $paymentIntent
        ]);
    }

    #[Route('/confirm', name: 'confirm')]
    public function confirm(
        Request $request,
        StripeClient $stripeClient,
        MeetingRepository $meetingRepository,
        WorkflowInterface $meetingStateMachine,
    ): Response {
        /** @var string $paymentIntent */
        $paymentIntent = $request->query->get('payment_intent');
        try {
            $paymentIntent = $stripeClient->paymentIntents->retrieve($paymentIntent);
        } catch (Exception $e) {
            $paymentIntent = null;
        }

        if ($paymentIntent === null || $paymentIntent->status !== PaymentIntent::STATUS_SUCCEEDED) {
            return $this->redirectToRoute('home');
        }

        $meeting = $meetingRepository->findOneBy(['paymentReference' => $paymentIntent->id]);
        if ($meeting === null || $meeting->getStatus() !== Meeting::STATUS_UNPAYED) {
            return $this->redirectToRoute('home');
        }

        $meetingStateMachine->apply($meeting, 'pay');

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
