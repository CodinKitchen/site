<?php

namespace App\Validator\Meeting;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TimeSlotAvailability extends Constraint
{
    public $message = 'validator.slot.not.available';
}
