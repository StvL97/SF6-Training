<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageTest extends WebTestCase
{
    public static function provideHelloSayingPages(): array
    {
        return [
            'Homepage' => ['/', 'World'],
            'Hello page' => ['/hello/steffen', 'steffen']
        ];
    }

    /**
     * @dataProvider provideHelloSayingPages
     */
    public function testHomepageSaysHelloWorld(string $uri, string $expectedName): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello ' . $expectedName);
    }

    public function testHelloPageIsNotFoundWhenNameIsNotGiven(): void
    {
        $client = static::createClient();
        $client->request('GET', '/hello/');

        $this->assertResponseStatusCodeSame(404, 'expected 404');
    }
}
