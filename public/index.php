<?php

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

// inittialization
$request = ServerRequestFactory::fromGlobals();

// action
$name     = $request->getQueryParams()['name'] ?? 'Guest';
$response = (new HtmlResponse('Hello ' . $name))
    ->withHeader('X-Developer', 'Yuri');

// sending
$emiter = new SapiEmitter();
$emiter->emit($response);