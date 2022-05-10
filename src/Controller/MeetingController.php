<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Service\Meeting\MeetingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[IsGranted('ROLE_USER')]
class MeetingController extends AbstractController
{
    #[Route('/meeting/{id}/join', name: 'meeting_join')]
    public function join(Meeting $meeting, MeetingService $meetingService): Response
    {
        return $this->redirect($meetingService->join($meeting));
    }

    #[Route('/meeting/{id}/replay', name: 'meeting_replay')]
    public function replay(Meeting $meeting, MeetingService $meetingService): Response
    {
        return $this->redirect($meetingService->getPlaybackUrl($meeting));
    }

    #[Route('/meeting/{id}/replay/ready', name: 'meeting_replay_ready')]
    public function replayReady(Meeting $meeting, Request $request, WorkflowInterface $meetingStateMachine): Response
    {
        $meeting->setBbbRecordingId($request->request->get('recordId'));
        $meetingStateMachine->apply($meeting, 'to_playable');
        return $this->redirect($meetingService->getPlaybackUrl($meeting));
    }
}
