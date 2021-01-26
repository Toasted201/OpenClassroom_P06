<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //dd($client->getResponse()->getContent());

        $crawler = $client->request('GET', '/register');
        $this->assertGreaterThan(0, $crawler->filter('button.btn-success')->count(),'Il y a au moins un bouton');
        $this->assertSelectorTextContains('html h1', 'Inscrivez-vous');

    }
}