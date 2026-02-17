<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('install', static function (RouteCollection $routes): void {
    $routes->get('/', 'Installer::index');
    $routes->match(['get', 'post'], 'precheck', 'Installer::precheck');
    $routes->match(['get', 'post'], 'database', 'Installer::database');
    $routes->match(['get', 'post'], 'app', 'Installer::app');
    $routes->match(['get', 'post'], 'migrate', 'Installer::migrate');
    $routes->match(['get', 'post'], 'cleanup', 'Installer::cleanup');
    $routes->get('complete', 'Installer::complete');
});
