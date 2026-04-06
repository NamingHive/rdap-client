<?php declare(strict_types=1);

namespace NamingHive\RDAP\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NamingHive\RDAP\Http\HttpClient;
use NamingHive\RDAP\RdapException;
use PHPUnit\Framework\TestCase;

final class HttpClientTest extends TestCase
{
    public function testGetReturnsBody(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"test": "data"}'),
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $http = new HttpClient(client: $guzzle);
        $result = $http->get('https://example.com');

        $this->assertSame('{"test": "data"}', $result);
    }

    public function testGetJsonReturnsDecodedArray(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"key": "value", "number": 42}'),
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $http = new HttpClient(client: $guzzle);
        $result = $http->getJson('https://example.com');

        $this->assertSame(['key' => 'value', 'number' => 42], $result);
    }

    public function testGetJsonThrowsOnInvalidJson(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'not valid json'),
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $http = new HttpClient(client: $guzzle);

        $this->expectException(RdapException::class);
        $this->expectExceptionMessage('Invalid JSON');
        $http->getJson('https://example.com');
    }

    public function testGetThrowsRdapExceptionOnHttpError(): void
    {
        $mock = new MockHandler([
            new Response(500, [], 'Server Error'),
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler, 'http_errors' => true]);

        $http = new HttpClient(client: $guzzle);

        $this->expectException(RdapException::class);
        $this->expectExceptionMessage('HTTP request failed');
        $http->get('https://example.com');
    }
}
