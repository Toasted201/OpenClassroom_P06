<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testShowRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());      
        $this->assertGreaterThan(0, $crawler->filter('button.btn-success')->count(),'Il y a au moins un bouton');
        $this->assertSelectorTextContains('html h1', 'Inscrivez-vous');
    }

    public function testTitleRegister()
    {
        $client = static::createClient();       
        $crawler = $client->request('GET', '/register');
        $this->assertSelectorTextContains('html h1', 'Inscrivez-vous');
    }

    public function testLoginLinkRegister()
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->clickLink('Déjà Inscrit? Connectez-vous');
        $this->assertPageTitleContains('Connexion');
    }
}    