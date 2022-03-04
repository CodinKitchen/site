<?php

namespace App\Entity;

use App\DBAL\Type\RruleType;
use App\Repository\ScheduleRuleRepository;
use Doctrine\ORM\Mapping as ORM;
use Recurr\Rule;

#[ORM\Entity(repositoryClass: ScheduleRuleRepository::class)]
class ScheduleRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: RruleType::NAME)]
    private ?Rule $rule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setRule(Rule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }
}
