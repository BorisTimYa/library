<?php

namespace App\Tests;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{

    public function testCreateBook(): void
    {
        $client = static::createClient();
        $content = new stdClass();
        $content->name = "English book title|Русское название книги";
        $content->author = ["First Test Author", "Second Test Author"];

        $client->request('POST', '/book/create', [], [], [], json_encode($content));
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('id', $response);
    }

    public function testSearchBookMultilingualRU(): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/ru/book/search', [], [], [], 'название книги');
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsArray($response);
        $this->assertNotCount(0, $response);
    }

    public function testSearchBookMultilingualEN(): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/en/book/search', [], [], [], 'название книги');
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    public function testShowBookMultilingualRU(): void
    {
        $titlePart = 'название книги';
        $client = static::createClient();
        $client->request('SEARCH', '/ru/book/search', [], [], [], $titlePart);
        $response = json_decode($client->getResponse()->getContent());
        $book = reset($response);
        $this->assertIsObject($book);
        $this->assertObjectHasAttribute('id', $book);
        $client->request('GET', '/ru/book/'.$book->id);
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('Name', $book);
        $this->assertObjectHasAttribute('Author', $book);
        $this->assertIsArray($book->Author);
        $this->assertStringContainsString($titlePart, $book->Name);
    }

    public function testShowBookMultilingualEN(): void
    {
        $titlePart = 'book title';
        $client = static::createClient();
        $client->request('SEARCH', '/en/book/search', [], [], [], $titlePart);
        $response = json_decode($client->getResponse()->getContent());
        $book = reset($response);
        $this->assertIsObject($book);
        $this->assertObjectHasAttribute('id', $book);
        $client->request('GET', '/en/book/'.$book->id);
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('Name', $book);
        $this->assertObjectHasAttribute('Author', $book);
        $this->assertIsArray($book->Author);
        $this->assertStringContainsString($titlePart, $book->Name);
    }

}
