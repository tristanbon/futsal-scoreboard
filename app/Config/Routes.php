<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');


// Futsal Scoreboard
$routes->get('/scoreboard', 'Scoreboard::index');
$routes->get('/scoreboard/create', 'Scoreboard::create');
$routes->post('/scoreboard/store', 'Scoreboard::store');

// list of all matches (live + finished)
$routes->get('/scoreboard/history', 'Scoreboard::history');

//match page
$routes->get('/scoreboard/match/(:num)', 'Scoreboard::match/$1');
// save live changes from the match page
$routes->post('/scoreboard/update/(:num)', 'Scoreboard::update/$1');
