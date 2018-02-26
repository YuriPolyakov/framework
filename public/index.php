<?php

use Framework\Container\Container;
use Framework\Http\Application;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * @var Container $container
 * @var Application $app
 */

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$container = require 'config/container.php';
$app = $container->get(Application::class);

require 'config/pipeline.php';
require 'config/routes.php';

$request  = ServerRequestFactory::fromGlobals();
$response = $app->run($request, new Response());

$emiter = new SapiEmitter();
$emiter->emit($response);