<?php

namespace App\Dto;

use App\Entity\Meeting;
use DateTimeImmutable;

class MeetingRequestDto
{
    private DateTimeImmutable $date;

    private DateTimeImmutable $time;

    private int $duration;

    private ?string $note = null;

    private string $paymentMethod;

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(DateTimeImmutable $time): self
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

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function toMeeting(): Meeting
    {
        $meeting = new Meeting();
        $meeting->setDuration($this->duration);
        $meeting->setNote($this->note);
        $timeSlot = $this->date;
        $timeSlot = $timeSlot->setTime((int) $this->time->format('H'), (int) $this->time->format('i'));
        $meeting->setTimeSlot($timeSlot);

        return $meeting;
    }
}
