<?php

namespace App\Twig\Component;

use App\Service\Schedule\ScheduleService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('meeting_slot_selection')]
class MeetingSlotSelectionComponent
{
    use DefaultActionTrait;

    /**
     * @var array<string, string[]> $dates
     */
    public array $slotsByDate;

    #[LiveProp(writable: true)]
    public string $date = "";

    public function __construct(private ScheduleService $scheduleService)
    {
        $this->slotsByDate = $this->arrangeSlotsByDate();
    }

    /**
     * @return string[]
     */
    public function getAvailableSlots(): array
    {
        return $this->slotsByDate[$this->date] ?? [];
    }

    /**
     * @return array<string, string[]>
     */
    private function arrangeSlotsByDate(): array
    {
        $slotsByDate = [];

        foreach ($this->scheduleService->getNextBookingSlots() as $bookingSlot) {
            $timeSlots = $slotsByDate[$bookingSlot->format('d/m/Y')] ?? [];
            $timeSlots[] = $bookingSlot->format('H:i');
            $slotsByDate[$bookingSlot->format('d/m/Y')] = $timeSlots;
        }

        return $slotsByDate;
    }
}
