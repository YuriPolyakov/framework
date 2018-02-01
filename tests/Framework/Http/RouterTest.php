<?php

namespace Tests\Framework\Http;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Framework\Http\Router\RouterCollection;
use Framework\Http\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testCorrectMethod()
    {
        $routes = new RouterCollection();

        $routes->get($nameGet = 'blog', '/blog', $hadlerGet = 'handler_get');
        $routes->post($namePost = 'blog_edit', '/blog', $handlerPost = 'handlerPost');

        $router = new Router($routes);

        $result = $router->match($this->buildRequest('GET', '/blog'));
        self::assertEquals($nameGet, $result->getName());
        self::assertEquals($hadlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog'));
        self::assertEquals($namePost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

/*    public function testGenerateMissingAttributes()
    {
        $routes = new RouterCollection();

        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        $this->expectException(\InvalidArgumentException::class);
        $router->generate('blog_show', ['slug' => 'post']);
    }*/

    private function buildRequest($method, $url): ServerRequest
    {
        return (new ServerRequest())
            ->withMethod($method)
            ->withUri(new Uri($url));
    }
}