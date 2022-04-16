<?php

namespace App\DataFixtures;

use App\Entity\ScheduleRule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Recurr\Rule;

class ScheduleRuleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $scheduleRule = new ScheduleRule();

        $rule = new Rule('FREQ=WEEKLY;INTERVAL=1;BYSECOND=0;BYMINUTE=0;BYHOUR=9,10,11,12,14,15,16,17,18;BYDAY=TU,TH;WKST=MO');
        $scheduleRule->setRule($rule);
        $manager->persist($scheduleRule);

        $manager->flush();
    }
}
