<?php

namespace App\Entity;

use App\Entity\Enum\MeetingStatus;
use App\Repository\MeetingRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 10, enumType: MeetingStatus::class)]
    private ?MeetingStatus $status;

    #[ORM\Column(type: 'integer')]
    private ?int $duration;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $attendee;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $timeSlot;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?MeetingStatus
    {
        return $this->status;
    }

    public function setStatus(MeetingStatus $status): self
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
