<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $meetings = $user->getMeetings()->filter(fn(Meeting $meeting) => $meeting->getStatus() !== Meeting::STATUS_DRAFT);

        return $this->render('profile/index.html.twig', compact('meetings'));
    }
}
