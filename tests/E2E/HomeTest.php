<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class HomeTest extends PantherTestCase
{
    public function testBookingLoggedIn(): void
    {
        $client = self::createPantherClient();

        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Welcome to my Kitchen !');
        $client->takeScreenshot('screen.png');
    }
}
