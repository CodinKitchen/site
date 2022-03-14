<?php

namespace App\Validator\Meeting;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TimeSlotAvailability extends Constraint
{
    public string $message = 'validator.slot.not.available';
}
