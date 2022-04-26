<?php

namespace App\Controller;

use App\Service\Meeting\MeetingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', options: ['sitemap' => true])]
    public function index(MeetingService $meetingService): Response
    {
        $meetingService->getRecordings();
        return $this->render('home.html.twig');
    }
}
