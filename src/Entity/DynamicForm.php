<?php

namespace App\Entity;

use App\Repository\DynamicFormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DynamicFormRepository::class)]
class DynamicForm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: DynamicFormInput::class, orphanRemoval: true)]
    private $inputs;

    public function __construct()
    {
        $this->inputs = new ArrayCollection();
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

    /**
     * @return Collection<int, DynamicFormInput>
     */
    public function getInputs(): Collection
    {
        return $this->inputs;
    }

    public function addInput(DynamicFormInput $input): self
    {
        if (!$this->inputs->contains($input)) {
            $this->inputs[] = $input;
            $input->setForm($this);
        }

        return $this;
    }

    public function removeInput(DynamicFormInput $input): self
    {
        if ($this->inputs->removeElement($input)) {
            // set the owning side to null (unless already changed)
            if ($input->getForm() === $this) {
                $input->setForm(null);
            }
        }

        return $this;
    }
}
