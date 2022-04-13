<?php

namespace App\Entity;

use App\Form\Config\FormTypes;
use App\Repository\DynamicFormInputRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DynamicFormInputRepository::class)]
class DynamicFormInput
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $label;

    #[ORM\Column(type: 'string', length: 255, enumType: FormTypes::class)]
    private ?FormTypes $type;

    /** @var mixed[]|null $options */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = [];

    #[ORM\Column(type: 'integer')]
    private ?int $step;

    #[ORM\ManyToOne(targetEntity: DynamicForm::class, inversedBy: 'inputs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DynamicForm $form;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $sort;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Json()]
    private ?string $displayRule;

    public function __toString(): string
    {
        return sprintf("%s", $this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): ?FormTypes
    {
        return $this->type;
    }

    public function setType(FormTypes $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null|mixed[]
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param null|mixed[] $options
     */
    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getForm(): ?DynamicForm
    {
        return $this->form;
    }

    public function setForm(?DynamicForm $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getDisplayRule(): ?string
    {
        return $this->displayRule;
    }

    public function setDisplayRule(?string $displayRule): self
    {
        $this->displayRule = $displayRule;

        return $this;
    }
}
