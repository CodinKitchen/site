<?php

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private const USERS = [
        [
            'email' => 'admin@codin.kitchen',
            'firstname' => 'Gauthier',
            'role' => UserRole::ROLE_ADMIN,
            'ref' => 'admin',
        ],
        [
            'email' => 'attendee@test.com',
            'firstname' => 'John',
            'role' => UserRole::ROLE_ATTENDEE,
            'ref' => 'attendee',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $user) {
            $userEntity = new User();
            $userEntity->setEmail($user['email']);
            $userEntity->setFirstname($user['firstname']);
            $userEntity->addRole($user['role']);
            $this->setReference($user['ref'], $userEntity);
            $manager->persist($userEntity);
        }

        $manager->flush();
    }
}
