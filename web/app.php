<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
  include_once __DIR__.'/../var/bootstrap.php.cache';
}

// Default to a non-debugging prod-environment.
$symfony_env = getenv('SYMFONY_ENV') ? getenv('SYMFONY_ENV') : 'prod';
$symfony_debug = getenv('SYMFONY_DEBUG') ? getenv('SYMFONY_DEBUG') : '0';
// Enable debugging if explicitly asked to do so.
if ($symfony_debug) {
  Debug::enable();
}

$kernel = new AppKernel($symfony_env, $symfony_debug);
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
