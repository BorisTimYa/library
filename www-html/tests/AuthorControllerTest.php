<?php

namespace App\Tests;

use Iterator;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AuthorControllerTest
 *
 * @package App\Tests
 */
class AuthorControllerTest extends WebTestCase
{

    /**
     * @return \Iterator
     */
    public function authorProvider(): Iterator
    {
        for ($i = 1; $i <= 5; $i++) {
            yield ["Test Author #$i"];
        }
    }

    /**
     * @dataProvider authorProvider
     */
    public function testCreateAuthor(string $name): void
    {
        $client = static::createClient();
        $content = new stdClass();
        $content->name = $name;
        $client->request('POST', '/author/create', [], [], [], json_encode($content));
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent());
        $this->assertIsObject($response);
        $this->assertObjectHasAttribute('id', $response);
    }


}
