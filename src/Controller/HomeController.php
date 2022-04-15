<?php

namespace App\Controller;

use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/bbb', name: 'bbb')]
    public function bbb(BigBlueButton $bigBlueButton): Response
    {
        $createMeetingParams = new CreateMeetingParameters('yrzfiarw8hcpwqdcs914x9ram750cg3lzvvfxo2f', 'Test meeting');
        $createMeetingParams->setAllowModsToEjectCameras(true);
        $createMeetingParams->setBreakoutRoomsEnabled(false);
        $createMeetingParams->setBreakoutRoomsRecord(false);
        $createMeetingParams->setBreakoutRoomsPrivateChatEnabled(false);
        $createMeetingParams->setMeetingLayout('abc');
        dump($bigBlueButton->createMeeting($createMeetingParams));
        dump($bigBlueButton->getJoinMeetingURL(new JoinMeetingParameters('yrzfiarw8hcpwqdcs914x9ram750cg3lzvvfxo2f', 'Toto', '')));

        return $this->json(['status' => 'success']);
    }
}
