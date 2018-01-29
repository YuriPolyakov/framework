<?php

namespace Tests\Framework\Http;

use Framework\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testEmpty(): void
    {
        $request = new Request();

        self::assertEquals([], $request->getQueryParrams());
        self::assertNull($request->getParsedBody());
    }

    public function testQueryParams(): void
    {
        $request = (new Request())
            ->withQueryParams($data = [
            'name' => 'John',
            'age'  => 30
        ]);

        self::assertEquals($data, $request->getQueryParrams());
        self::assertNull($request->getParsedBody());
    }

    public function testParseBody(): void
    {
        $request = (new Request())->withParsedBody($data = ['title' => 'title']);

        self::assertEquals([], $request->getQueryParrams());
        self::assertEquals($data, $request->getParsedBody());
    }
}