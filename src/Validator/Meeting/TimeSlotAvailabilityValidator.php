<?php

namespace App\Validator\Meeting;

use App\Service\Schedule\ScheduleService;
use DateTimeImmutable;
use Recurr\Exception\InvalidWeekday;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class TimeSlotAvailabilityValidator extends ConstraintValidator
{
    public function __construct(private ScheduleService $scheduleService)
    {
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof TimeSlotAvailability) {
            throw new UnexpectedTypeException($constraint, TimeSlotAvailability::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof DateTimeImmutable) {
            throw new UnexpectedValueException($value, 'DateTimeImmutable');
        }

        if (!in_array($value, $this->scheduleService->getNextBookingSlots())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
