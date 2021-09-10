<?php

namespace App\Tests;

use Iterator;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class BookControllerTest
 *
 * @package App\Tests
 */
class BookControllerTest extends WebTestCase
{

    /**
     * @return Iterator
     */
    public function titleProvider(): Iterator
    {
        $rand = date('Y-m').'-'.gethostname();
        for ($i = 1; $i <= 10; $i++) {
            $authors = [];
            for ($ii = 1; $ii <= rand(1, 10); $ii++) {
                $authors[] = sprintf("Author %s t#%s.%s book", $rand, $i, $ii);
            }
            yield [
              sprintf("English title %s t#%s book", $rand, $i),
              sprintf("Русское название %s t#%s книги", $rand, $i),
              $authors,
            ];
        }
    }


    /**
     * @dataProvider titleProvider
     *
     * @param string $nameEn
     * @param string $nameRu
     *
     * @return int
     */
    public function testCreateBook(string $nameEn, string $nameRu, array $authorNames): void
    {
        $client = static::createClient();
        $content = new stdClass();
        $content->name = "$nameEn|$nameRu";
        $content->author = $authorNames;
        $client->request('POST', '/book/create', [], [], [], json_encode($content));
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('id', $response);
    }

    /**
     * @dataProvider  titleProvider
     * @depends       testCreateBook
     */
    public function testSearchBookMultilingualRU(string $nameEn, string $nameRu): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/ru/book/search', [], [], [], $nameRu);
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsArray($response);
        $this->assertNotCount(0, $response);
    }

    /**
     * @dataProvider  titleProvider
     * @depends       testCreateBook
     */
    public function testSearchBookMultilingualEN(string $nameEn, string $nameRu): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/en/book/search', [], [], [], $nameRu);
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    /**
     * @dataProvider  titleProvider
     * @depends       testCreateBook
     */
    public function testShowBookMultilingualRU(string $nameEn, string $nameRu): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/ru/book/search', [], [], [], $nameRu);
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
        $this->assertStringContainsString($nameRu, $book->Name);
    }

    /**
     * @dataProvider  titleProvider
     * @depends       testCreateBook
     */
    public function testShowBookMultilingualEN(string $nameEn, string $nameRu): void
    {
        $client = static::createClient();
        $client->request('SEARCH', '/en/book/search', [], [], [], $nameEn);
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
        $this->assertStringContainsString($nameEn, $book->Name);
    }

}
