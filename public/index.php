<?php

use Framework\Http\RequestFactory;
use Framework\Http\Response;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

// inittialization
$request = RequestFactory::fromGlobals();

// action
$name     = $request->getQueryParrams()['name'] ?? 'Guest';
$response = (new Response('Hello ' . $name))
    ->withHeader('X-Developer', 'Yuri');

// sending
header('HTTP/1.0 ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase());
foreach ($response->getHeaders() as $name => $value) {
    header($name . ':' . $value);
}

echo $response->getBody();