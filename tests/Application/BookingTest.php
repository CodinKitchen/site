<?php

namespace App\Tests\Application;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingTest extends WebTestCase
{
    public function testBookingLoggedOut(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book');

        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testBookingLoggedIn(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'attendee@test.com']);

        $client->loginUser($user);

        $client->request('GET', '/book');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Réservez votre session coaching !');
    }
}
