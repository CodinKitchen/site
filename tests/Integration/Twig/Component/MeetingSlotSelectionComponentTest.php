<?php

namespace App\Tests\Integration\Twig\Component;

use App\Twig\Component\MeetingSlotSelectionComponent;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MeetingSlotSelectionComponentTest extends KernelTestCase
{
    public function testgetAvailableSlots(): void
    {
        $kernel = self::bootKernel();

        $meetingSlotComponent = static::getContainer()->get(MeetingSlotSelectionComponent::class);

        $meetingSlotComponent->date = (new DateTime('next tuesday'))->format('d/m/Y');
        $slots = $meetingSlotComponent->getAvailableSlots();
        $this->assertCount(9, $slots);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $slots[0]);

        $meetingSlotComponent->date = (new DateTime('next monday'))->format('d/m/Y');
        $this->assertCount(0, $meetingSlotComponent->getAvailableSlots());
    }
}
