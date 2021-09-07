<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{

    public function testCreateAuthor(): void
    {
        $client = static::createClient();
        $client->request('PUT', 'http://192.168.8.100:8888/author/create', [], [], [], '{"name":"Test Author"}');
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('id', $response);
    }

}
