<?php

namespace App\Entity;

use App\Repository\AssessmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssessmentRepository::class)]
class Assessment extends Product
{
    #[ORM\ManyToOne(targetEntity: DynamicForm::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $form;

    public function getForm(): ?DynamicForm
    {
        return $this->form;
    }

    public function setForm(?DynamicForm $form): self
    {
        $this->form = $form;

        return $this;
    }
}
