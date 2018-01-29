<?php

namespace Tests\Framework\Http;

use Framework\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testEmpty(): void
    {
        $response = new Response($body = 'Body');

        self::assertEquals($body, $response->getBody());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhrase());
    }

    public function test404(): void
    {
        $response = new Response($body = 'Empty', $status = 404);

        self::assertEquals($body, $response->getBody());
        self::assertEquals($status, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhrase());
    }

    public function testHeaders(): void
    {
        $response = (new Response(''))
            ->withHeader($header1 = 'X-Header-1', $value1 = 'value1')
            ->withHeader($header2 = 'X-Header-2', $value2 = 'value2');

        self::assertEquals([$header1 => $value1, $header2 => $value2], $response->getHeaders());
    }
}