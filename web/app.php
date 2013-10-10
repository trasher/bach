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

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict
// with another application
/*
 * $loader = new ApcClassLoader('sf2', $loader); $loader->register(true);
 */

/** FIXME: name *must* be unique if more than one app in sinstalled on the server! */
$loader = new ApcClassLoader('bach', $loader);
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
