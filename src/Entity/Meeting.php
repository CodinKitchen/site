<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_STARTED = 'started';
    public const STATUS_ENDED = 'ended';
    public const STATUS_WAITING_RECORDING = 'wait_for_recording';
    public const STATUS_PLAYABLE = 'playable';
    public const STATUS_CANCELED = 'canceled';

    public const ALLOWED_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_STARTED,
        self::STATUS_ENDED,
        self::STATUS_WAITING_RECORDING,
        self::STATUS_PLAYABLE,
        self::STATUS_CANCELED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 25)]
    #[Assert\NotBlank]
    private ?string $status;

    #[ORM\Column(type: 'integer')]
    #[Assert\Choice([1,2])]
    #[Assert\NotNull()]
    private ?int $duration;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?User $attendee;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull(message:'error.meeting.timeSlot')]
    private ?DateTimeImmutable $timeSlot;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bbbRecordingId;

    private ?string $meetingUrl;

    public function __construct()
    {
        $this->status = self::STATUS_DRAFT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param array<mixed, mixed> $context
     */
    public function setStatus(string $status, array $context = []): self
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getBbbRecordingId(): ?string
    {
        return $this->bbbRecordingId;
    }

    public function setBbbRecordingId(?string $bbbRecordingId): self
    {
        $this->bbbRecordingId = $bbbRecordingId;

        return $this;
    }

    public function getMeetingUrl(): ?string
    {
        return $this->meetingUrl;
    }

    public function setMeetingUrl(string $meetingUrl): self
    {
        $this->meetingUrl = $meetingUrl;

        return $this;
    }

    public function isJoinable(): bool
    {
        return $this->status === self::STATUS_CONFIRMED && $this->timeSlot < new DateTimeImmutable();
    }
}
