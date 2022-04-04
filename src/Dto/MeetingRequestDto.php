<?php

namespace App\Dto;

use App\Entity\Meeting;
use App\Validator\Meeting\TimeSlotAvailability;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

#[TimeSlotAvailability()]
class MeetingRequestDto
{
    #[Assert\NotNull()]
    private ?DateTimeImmutable $date;

    #[Assert\NotNull()]
    private ?DateTimeImmutable $time;

    #[Assert\Choice([1,2])]
    #[Assert\NotNull()]
    private int $duration;

    private ?string $note = null;

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(?DateTimeImmutable $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function toMeeting(): Meeting
    {
        $meeting = new Meeting();
        $meeting->setDuration($this->duration);
        $meeting->setNote($this->note);
        if ($this->date !== null && $this->time !== null) {
            $timeSlot = $this->date;
            $timeSlot = $timeSlot->setTime((int) $this->time->format('H'), (int) $this->time->format('i'));
            $meeting->setTimeSlot($timeSlot);
        }

        return $meeting;
    }
}
