<?php

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    private const USERS = [
        [
            'email' => 'admin@codin.kitchen',
            'firstname' => 'Gauthier',
            'role' => UserRole::ROLE_ADMIN,
            'ref' => 'admin',
            'bbbMeetingId' => null,
        ],
        [
            'email' => 'attendee@test.com',
            'firstname' => 'John',
            'role' => UserRole::ROLE_ATTENDEE,
            'ref' => 'attendee',
            'bbbMeetingId' => '3afa35fb-d21a-4260-b22e-852d3be3c35a',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $user) {
            $userEntity = new User();
            $userEntity->setEmail($user['email']);
            $userEntity->setFirstname($user['firstname']);
            $userEntity->addRole($user['role']);
            $userEntity->setBbbMeetingId($user['bbbMeetingId'] !== null ? Uuid::fromString($user['bbbMeetingId']) : null);
            $this->setReference($user['ref'], $userEntity);
            $manager->persist($userEntity);
        }

        $manager->flush();
    }
}
