<?php

namespace App\Service\Schedule;

use App\Repository\ScheduleRuleRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Recurr\Recurrence;
use Recurr\Transformer\ArrayTransformer;

class ScheduleService
{
    private const MAX_BOOKING_DELAY = 14;

    public function __construct(private ScheduleRuleRepository $scheduleRuleRepository)
    {
    }

    /**
     * @return DateTimeInterface[]
     */
    public function getNextBookingSlots(): array
    {
        $scheduleRules = $this->scheduleRuleRepository->findAll();

        $dates = [];
        foreach ($scheduleRules as $scheduleRule) {
            $rule = $scheduleRule->getRule();
            if ($rule !== null) {
                $rule->setUntil(new DateTime(sprintf('tomorrow + %d days', self::MAX_BOOKING_DELAY)));
                $rule->setStartDate((new DateTime())->setTime(0, 0));
                $transformer = new ArrayTransformer();
                /** @var Recurrence[] $recurrences */
                $recurrences = $transformer->transform($rule)->toArray();
                $dates = [...$dates, ...$recurrences];
            }
        }

        $dates = array_map(function (Recurrence $recurrence): DateTimeInterface {
            /** @var DateTime $dateStart */
            $dateStart = $recurrence->getStart();
            return DateTimeImmutable::createFromMutable($dateStart);
        }, $dates);

        sort($dates);

        return $dates;
    }
}
