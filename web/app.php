<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Default to a non-debugging prod-environment. 
$app_env = isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : 'prod';
$app_debug = isset($_ENV['APP_DEBUG']) ? $_ENV['APP_DEBUG'] : '0';

// Get the symfony autoloader in place.
$loader = require __DIR__.'/../app/autoload.php';

// Enable debugging if explicitly asked to do so.
if ($app_debug) {
  Debug::enable();
}

// Then get the kernel ready, parse the request and handle it.
require_once __DIR__.'/../app/AppKernel.php';
$kernel = new AppKernel($app_env, $app_debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

