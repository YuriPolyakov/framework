<?php

use App\Http\Action\CabinetAction;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\CredentialsMiddleware;
use App\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\NotFoundHadler;
use App\Http\Middleware\ProfilerMiddleware;
use Framework\Http\Application;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Pipeline\Pipeline;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;
use Framework\Http\Router\AuraRouterAdapter;
use \Framework\Http\Router\Exception\RequestNotMatchedException;
use Zend\Diactoros\Response\HtmlResponse;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

// Initialization
$params = [
    'debug' => true,
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
$routes->get('cabinet', '/cabinet', [
    ProfilerMiddleware::class,
    new BasicAuthMiddleware($params['users']),
    CabinetAction::class
]);

$router   = new AuraRouterAdapter($aura);
$resolver = new MiddlewareResolver();
$app      = new Application($resolver, new NotFoundHadler());

$app->pipe(new ErrorHandlerMiddleware($params['debug']));
$app->pipe(CredentialsMiddleware::class);
$app->pipe(ProfilerMiddleware::class);

// Running
$request = ServerRequestFactory::fromGlobals();
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $app->pipe($result->getHandler());
} catch (RequestNotMatchedException $e) {
}

$response = $app->run($request);

// Sending
$emiter = new SapiEmitter();
$emiter->emit($response);