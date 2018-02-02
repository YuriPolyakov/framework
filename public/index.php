<?php

use Framework\Http\ActionResolver;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;
use Framework\Http\Router\AuraRouterAdapter;
use \Framework\Http\Router\Exception\RequestNotMatchedException;
use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

// Initialization

$params = [
    'users' => ['admin' => 'password']
];

$aura   = new Aura\Router\RouterContainer;
$routes = $aura->getMap();

$routes->get('home', '/', App\Http\Action\HelloAction::class);
$routes->get('about', '/about', function () {
    return new HtmlResponse('about', 200);
});
$routes->get('blog', '/blog', \App\Http\Action\Blog\IndexAction::class);
$routes->get('blog_show', '/blog/{id}', \App\Http\Action\Blog\ShowAction::class)->tokens(['id' => '\d+']);

$routes->get('cabinet', '/cabinet', function (ServerRequestInterface $request) use ($params) {
    $profiler = new \App\Http\Middleware\ProfilerMiddleware();
    $auth     = new \App\Http\Middleware\BasicAuthMiddleware($params['users']);
    $cabinet  = new \App\Http\Action\CabinetAction();

    return $profiler($request, function (ServerRequestInterface $request) use ($auth, $cabinet) {
        return $auth($request, function (ServerRequestInterface $request) use ($cabinet) {
            return $cabinet($request);
        });
    });
});

$router   = new AuraRouterAdapter($aura);
$resolver = new ActionResolver();

// Running
$request = ServerRequestFactory::fromGlobals();
try {
    $result = $router->match($request);

    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }

    $action   = $resolver->resolve($result->getHandler());
    $response = $action($request);
} catch (RequestNotMatchedException $e) {
    $response = new JsonResponse(['error' => 'undefined page'], 404);
}

// Postprocessing
$response = $response->withHeader('X-Developer', 'Yuri');

// Sending
$emiter = new SapiEmitter();
$emiter->emit($response);