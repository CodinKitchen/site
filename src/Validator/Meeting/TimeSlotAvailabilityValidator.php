<?php

namespace App\Validator\Meeting;

use App\Dto\MeetingRequestDto;
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

        if (!$value instanceof MeetingRequestDto) {
            throw new UnexpectedValueException($value, 'MeetingRequestDto');
        }

        if ($value->getDate() === null || $value->getTime() === null) {
            $this->context->buildViolation($constraint->slotNotNull)
                ->addViolation();
                return;
        }

        $timeSlot = $value->getDate();
        $timeSlot = $timeSlot->setTime((int) $value->getTime()->format('H'), (int) $value->getTime()->format('i'));

        if (!in_array($timeSlot, $this->scheduleService->getNextBookingSlots())) {
            $this->context->buildViolation($constraint->slotNotAvailable)
                ->addViolation();
        }
    }
}
