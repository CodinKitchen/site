<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialController extends AbstractController
{
    #[Route('/social', name: 'app_social')]
    public function index(): Response
    {
        return $this->render('social/index.html.twig', [
            'controller_name' => 'SocialController',
        ]);
    }
}
