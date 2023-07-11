<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testItDisplaysMovies(): void
    {
        $client = static::createClient();
        $client->request('GET', 'movies/list');

        $this->assertSelectorCount(3, 'ul li');
    }
}
