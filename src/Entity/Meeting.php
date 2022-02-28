<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $status;

    #[ORM\Column(type: 'integer')]
    private $duration;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private $attendee;

    #[ORM\Column(type: 'datetime_immutable')]
    private $timeSlot;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAttendee(): ?User
    {
        return $this->attendee;
    }

    public function setAttendee(?User $attendee): self
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function getTimeSlot(): ?\DateTimeImmutable
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(\DateTimeImmutable $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }
}
