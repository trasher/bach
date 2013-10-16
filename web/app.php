<?php
/**
 * Application entry point
 *
 * PHP version 5
 *
 * @category Public
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = include_once __DIR__ . '/../app/bootstrap.php.cache';

// Use APC(u) for autoloading, improve perfs
// Key *must* be unique if multiple applications
// are running on the same server.
$loader = new ApcClassLoader(
    __DIR__ . '_bach_app',
    $loader
);
$loader->register(true);

require_once __DIR__ . '/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
