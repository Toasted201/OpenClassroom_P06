<?php

namespace Tests\Controller;

use App\Repository\UserRepository;
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

    public function testAddComment()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'bob@doe.com']);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/trick/mute');

        $form = $crawler->selectButton('Valider')->form();
        $form['comment_form[content]'] = 'Test Comment';
        $crawler = $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorTextContains('.text-info', 'Test Comment');
    }
}    