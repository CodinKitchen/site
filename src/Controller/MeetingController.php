<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Service\Meeting\MeetingService;
use App\Service\Security\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use UnexpectedValueException;

class MeetingController extends AbstractController
{
    #[Route('/meeting/{id}/join', name: 'meeting_join')]
    #[IsGranted('ROLE_USER')]
    public function join(Meeting $meeting, MeetingService $meetingService): Response
    {
        return $this->redirect($meetingService->join($meeting));
    }

    #[Route('/meeting/{id}/replay', name: 'meeting_replay')]
    #[IsGranted('ROLE_USER')]
    public function replay(Meeting $meeting, MeetingService $meetingService): Response
    {
        return $this->redirect($meetingService->getPlaybackUrl($meeting));
    }

    #[Route('/meeting/{id}/webhook/ended', name: 'meeting_ended', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function webhookEnded(
        Meeting $meeting,
        Request $request,
        WorkflowInterface $meetingStateMachine,
        EntityManagerInterface $entityManager
    ): Response {
        $nextStep = $request->query->get('recordingmarks') ? 'wait_for_recording' : 'ended';
        $meetingStateMachine->apply($meeting, $nextStep);
        $entityManager->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/meeting/{id}/webhook/replay-ready', name: 'meeting_replay_ready', methods: ['POST'])]
    public function webhookReplayReady(
        Meeting $meeting,
        Request $request,
        WorkflowInterface $meetingStateMachine,
        EntityManagerInterface $entityManager,
        JWTService $JWTService
    ): Response {
        // try {
            $data = $JWTService->decode($request->request->get('signed_parameters'), base64_encode('kotmlILhg7HeiqwdvrZ7U3dyOBxqszJeF7CUbhjQ'), 'HS256');
            dump($data);
        // } catch (UnexpectedValueException $exception) {
        //     return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
        // }
        // $meeting->setBbbRecordingId($request->request->get('recordId'));
        // $meetingStateMachine->apply($meeting, 'to_playable');
        // $entityManager->flush();
        return $this->json(['success' => true]);
    }
}
