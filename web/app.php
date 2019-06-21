<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
  include_once __DIR__.'/../var/bootstrap.php.cache';
}

// Default to a non-debugging prod-environment.
$app_env = getenv('APP_ENV') ? getenv('APP_ENV') : 'prod';
$app_debug = getenv('APP_DEBUG') ? getenv('APP_DEBUG') : '0';
// Enable debugging if explicitly asked to do so.
if ($app_debug) {
  Debug::enable();
}

$kernel = new AppKernel($app_env, $app_debug);
if (PHP_VERSION_ID < 70000) {
  $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
