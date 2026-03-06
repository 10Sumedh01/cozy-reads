<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ── Auth routes FIRST (before Shield) ──────────────────
$routes->get ('register', 'Auth\RegisterController::registerView');
$routes->post('register', 'Auth\RegisterController::registerAction');

$routes->get ('login',    'Auth\LoginController::loginView');
$routes->post('login',    'Auth\LoginController::loginAction');

$routes->get ('logout',   'Auth\LoginController::logoutAction');

// ── Protected routes (requires login) ──────────────────
$routes->group('', ['filter' => 'session'], function($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Books
    $routes->get ('books/add',              'BooksController::addView');       // show add book form
    $routes->post('books/add',              'BooksController::addAction');     // handle form submit
    $routes->get ('books/search',           'BooksController::search');        // Google Books API search (AJAX)
    $routes->get ('books/(:num)',           'BooksController::show/$1');       // single book detail
    $routes->post('books/progress/(:num)',  'BooksController::updateProgress/$1'); // update current page
    $routes->post('books/status/(:num)',    'BooksController::updateStatus/$1');   // change shelf status
    $routes->post('books/delete/(:num)',    'BooksController::delete/$1');     // remove from shelf

    // EPUB Reader
    $routes->get('reader/(:num)',           'ReaderController::index/$1');     // open epub reader
    $routes->post('reader/position/(:num)', 'ReaderController::savePosition/$1'); // save CFI position (AJAX)

    // Shelves
    $routes->get('shelves', 'ShelvesController::index');

    // Stats
    $routes->get('stats', 'StatsController::index');

    // Goals
    $routes->get ('goals',        'GoalsController::index');
    $routes->post('goals/save',   'GoalsController::save');

    // Profile
    $routes->get ('profile',        'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');

});

// ── Shield handles magic-link, 2FA etc ─────────────────
service('auth')->routes($routes);