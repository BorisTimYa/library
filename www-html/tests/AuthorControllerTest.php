<?php

namespace App\Tests;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    public function testCreateAuthor(): void
    {
        $client = static::createClient();
        $content = new stdClass();
        $content->name = "First Test Author";
        $client->request('POST', '/author/create', [], [], [], json_encode($content));
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('id', $response);
    }
}
