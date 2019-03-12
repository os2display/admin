<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Default to a non-debugging prod-environment.
$app_env = getenv('APP_ENV') ? getenv('APP_ENV') : 'prod';
$app_debug = getenv('APP_DEBUG') ? getenv('APP_DEBUG') : '0';
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