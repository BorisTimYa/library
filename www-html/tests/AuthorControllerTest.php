<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://192.168.8.100:8888/ru/book/1');

        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('small', 'Welcome to');
    }
}
