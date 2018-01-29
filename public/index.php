<?php

use Framework\Http\Request;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$request = new Request();

$name = $request->getQueryParrams()['name'] ?? 'Guest';
header('X-Developer: ElisDN');
echo 'Hello ' . $name;