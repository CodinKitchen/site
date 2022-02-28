<?php

namespace App\Entity;

use App\Repository\ScheduleRuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRuleRepository::class)]
class ScheduleRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $rule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(string $rule): self
    {
        $this->rule = $rule;

        return $this;
    }
}
