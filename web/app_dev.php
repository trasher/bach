<?php
/**
 * Application debug entry point
 *
 * PHP version 5
 *
 * @category Public
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

use Symfony\Component\HttpFoundation\Request;

/*
 * This check prevents access to debug front controllers that are deployed
 * by accident to production servers.
 * Feel free to remove this, extend it, or make something more sophisticated.
 */
$authorized = array(
    '127.0.0.1',
    '::1'
);

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], $authorized)
) {
    header('HTTP/1.0 403 Forbidden');
    exit(
        'You are not allowed to access this file. Check ' .
        basename(__FILE__) . ' for more information.'
    );
}

require_once __DIR__.'/../app/autoload.php';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
