<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);
// Override Shield's default auth routes
$routes->get('register',  'Auth\RegisterController::registerView');
$routes->post('register', 'Auth\RegisterController::registerAction');

$routes->get('login',     'Auth\LoginController::loginView');
$routes->post('login',    'Auth\LoginController::loginAction');

$routes->get('logout',    'Auth\LoginController::logoutAction');

// Protected routes
$routes->group('', ['filter' => 'session'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});