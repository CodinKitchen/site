<?php

namespace App\DataFixtures;

use App\Entity\Meeting;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MeetingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $meeting = new Meeting();
        $meeting->setDuration(1);
        $meeting->setNote('Test metting');
        $meeting->setPrice(4000);
        $meeting->setStatus(Meeting::STATUS_CONFIRMED);
        $meeting->setTimeSlot(new DateTimeImmutable());
        /** @var User $attendee */
        $attendee = $this->getReference('attendee');
        $meeting->setAttendee($attendee);
        $manager->persist($meeting);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
