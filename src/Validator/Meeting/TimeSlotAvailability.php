<?php

namespace App\Validator\Meeting;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TimeSlotAvailability extends Constraint
{
    public string $slotNotNull = 'error.meeting.timeSlot';
    public string $slotNotAvailable = 'error.slot.not.available';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
