<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testShowTrick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/trick/mute');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html h5', 'Description');
    }
}    