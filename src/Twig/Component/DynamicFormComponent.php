<?php

namespace App\Twig\Component;

use App\Form\DynamicFormType;
use App\Repository\DynamicFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('dynamic_form')]
class DynamicFormComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(private DynamicFormRepository $dynamicFormRepository)
    {
    }

    /** @var null|mixed[] */
    #[LiveProp(fieldName: 'data')]
    public ?array $data = null;

    protected function instantiateForm(): FormInterface
    {
        $dynamicForm = $this->dynamicFormRepository->find(2);
        return $this->createForm(DynamicFormType::class, $this->formValues, ['dynamic_form' => $dynamicForm]);
    }
}
