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

// Registration
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerPost');
// Verification for registration
$routes->get('register/verify', 'Auth::verifyRegister');
$routes->post('register/verify', 'Auth::verifyRegisterPost');

// Password reset
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPasswordPost');
$routes->get('reset-password/(:any)', 'Auth::validateResetToken/$1');
$routes->get('reset-password', 'Auth::resetPassword');
$routes->post('reset-password', 'Auth::resetPasswordPost');

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

// Chat (AJAX)
$routes->get('chat/fetch', 'Chat::fetch');
$routes->post('chat/send', 'Chat::send');

// SMS Gateway Routes
$routes->group('sms', function($routes) {
    $routes->post('send', 'SmsController::send');
    $routes->post('bulk', 'SmsController::sendBulk');
    $routes->get('logs', 'SmsController::getLogs');
    $routes->get('config', 'SmsController::getConfig');
    $routes->get('test', 'SmsController::test');
    $routes->get('status', 'SmsController::status');
});

// SMS Verification & Testing Routes
$routes->group('sms-verify', function($routes) {
    $routes->get('/', 'SmsVerificationController::index');
    $routes->get('check', 'SmsVerificationController::fullCheck');
    $routes->get('dashboard', 'SmsVerificationController::dashboard');
    $routes->post('test-send', 'SmsVerificationController::testSend');
    $routes->get('logs', 'SmsVerificationController::getLogs');
    $routes->get('report', 'SmsVerificationController::generateReport');
});

// Debug
$routes->get('debug/test-env', 'Debug::testEnv');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->get('barcode/generate/(:any)', 'BarcodeController::generate/$1');
    $routes->get('barcode/find/(:any)', 'BarcodeController::find/$1');
    $routes->post('barcode/create', 'BarcodeController::create');
});

// Sales pages
$routes->get('sales', 'Sales::index');
$routes->post('sales/checkout', 'Sales::checkout');

// Settings
$routes->get('settings', 'Settings::index');
$routes->post('settings/profile', 'Settings::updateProfile');
$routes->post('settings/theme', 'Settings::updateTheme');

// Users management (admin only)
$routes->get('users', 'Users::index');
$routes->get('users/create', 'Users::create');
$routes->post('users/store', 'Users::store');
$routes->get('users/edit/(:num)', 'Users::edit/$1');
$routes->post('users/update/(:num)', 'Users::update/$1');
$routes->post('users/delete/(:num)', 'Users::delete/$1');
