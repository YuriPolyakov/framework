<?php

/**
 * Присваивание массива параметров приложения в контейнер с ключом config
 */

use Framework\Container\Container;

$container = new Container();

$container->set('config', require __DIR__ . '/parameters.php');

require __DIR__ . '/dependencies.php';

return $container;