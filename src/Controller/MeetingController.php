<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Service\Meeting\MeetingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class MeetingController extends AbstractController
{
    #[Route('/meeting/{id}/join', name: 'meeting_join')]
    public function join(Meeting $meeting, MeetingService $meetingService): Response
    {
        return $this->redirect($meetingService->join($meeting));
    }
}
