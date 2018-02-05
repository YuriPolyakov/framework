<?php

namespace Framework\Http\Middleware;

use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\Result;
use Framework\Http\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class RouteMiddleware
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Определение маршрута с заполнением реквеста
     *
     * @param ServerRequestInterface $request
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            $result = $this->router->match($request);
            foreach ($result->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }

            return $next($request->withAttribute(Result::class, $result));
        } catch (RequestNotMatchedException $e) {
            return $next($request);
        }
    }
}