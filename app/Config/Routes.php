<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// Dashboard
$routes->get('dashboard', 'Dashboard::index');

// Products CRUD
$routes->get('products', 'Products::index');
$routes->get('products/create', 'Products::create');
$routes->post('products', 'Products::store');
$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update/(:num)', 'Products::update/$1');
$routes->get('products/delete/(:num)', 'Products::delete/$1');

// Inventory & Reports
$routes->get('inventory', 'Inventory::index');
$routes->get('reports', 'Reports::index');
