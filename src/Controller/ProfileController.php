<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Meeting\MeetingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(MeetingService $meetingService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        dump($meetingService->getRecordings($user));
        return $this->render('profile/index.html.twig');
    }
}
