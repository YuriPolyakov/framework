<?php

use App\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\NotFoundHadler;
use Aura\Router\RouterContainer;
use Framework\Http\Application;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Router;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response;

return [
    'dependencies' => [
        'abstract_factories' => [
            \Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory::class
        ],
        'factories' => [
            Application::class => function (ContainerInterface $container) {
                return new Application(
                    $container->get(MiddlewareResolver::class),
                    $container->get(Router::class),
                    new NotFoundHadler(),
                    new Response()
                );
            },
            Router::class => function () {
                return new AuraRouterAdapter(new RouterContainer());
            },
            MiddlewareResolver::class => function (ContainerInterface $container) {
                return new MiddlewareResolver($container);
            },

            ErrorHandlerMiddleware::class => function (ContainerInterface $container) {
                return new ErrorHandlerMiddleware($container->get('config')['debug']);
            }
        ]
    ],

    'debug' => false
];