<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class BookingController extends AbstractController
{
    #[Route('/book', name: 'book')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        WorkflowInterface $meetingStateMachine,
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

            return $this->redirectToRoute('payment', ['id' => $meeting->getId()]);
        }

        return $this->renderForm(
            '/booking/book.html.twig',
            [
                'meetingForm' => $form,
            ]
        );
    }

    #[Route('/payment/{id}', name: 'payment')]
    public function payment(
        Meeting $meeting,
        StripeClient $stripeClient,
        EntityManagerInterface $entityManager
    ): Response {
        if ($meeting->getStatus() !== Meeting::STATUS_UNPAYED) {
            return $this->redirect('home');
        }

        $paymentIntent = $stripeClient->paymentIntents->create([
            'amount' => 100000,
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        $meeting->setPaymentReference($paymentIntent->id);
        $entityManager->flush();

        return $this->render('booking/payment.html.twig', [
            'paymentClientSecret' => $paymentIntent->client_secret
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

        return $this->render('booking/confirm.html.twig');
    }
}
