<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://localhost:8888/ru/book/1');

        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('small', 'Welcome to');
    }
}
