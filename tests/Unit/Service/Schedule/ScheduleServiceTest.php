<?php

namespace App\Tests\Unit\Service\Schedule;

use App\Entity\ScheduleRule;
use App\Repository\ScheduleRuleRepository;
use App\Service\Schedule\ScheduleService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Recurr\Rule;

class ScheduleServiceTest extends TestCase
{
    public function testgetNextBookingSlots(): void
    {
        $scheduleRule = new ScheduleRule();
        $scheduleRule->setRule(new Rule('FREQ=WEEKLY;INTERVAL=1;WKST=MO;BYDAY=MO;BYHOUR=0;BYMINUTE=0;BYSECOND=0'));

        $scheduleRuleRepository = $this->createMock(ScheduleRuleRepository::class);
        $scheduleRuleRepository
            ->expects($this->any())
            ->method('findAll')
            ->willReturn([$scheduleRule]);

        $scheduleService = new ScheduleService($scheduleRuleRepository);
        $dates = $scheduleService->getNextBookingSlots();

        $this->assertContainsOnlyInstancesOf(DateTimeImmutable::class, $dates);
        $this->assertCount(2, $dates);
        $this->assertEquals([
            new DateTimeImmutable('next monday 00:00:00'),
            new DateTimeImmutable('second monday 00:00:00'),
        ], $dates);
    }
}
