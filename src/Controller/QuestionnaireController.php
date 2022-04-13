<?php

namespace App\Controller;

use App\Form\DynamicFormType;
use App\Repository\DynamicFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireController extends AbstractController
{
    #[Route('/questionnaire', name: 'app_questionnaire')]
    public function index(DynamicFormRepository $dynamicFormRepository, Request $request): Response
    {
        $dynamicForm = $dynamicFormRepository->find(2);

        $data = [];
        $form = $this->createForm(DynamicFormType::class, $data, ['dynamic_form' => $dynamicForm]);

        $form->handleRequest($request);

        return $this->renderForm('questionnaire/index.html.twig', [
            'data' => $data,
            'form' => $form,
        ]);
    }
}
