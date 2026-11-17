<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- Autentikasi ---
$routes->get('/', 'Auth::check');
$routes->get('login', 'Auth::showLogin');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::showRegister');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

// --- Dashboard ---
$routes->get('dashboard', 'Dashboard::index');

// --- Menu / lainnya ---
$routes->get('menu', 'Menu::index');

// --- Manajemen kendaraan ---
$routes->get('kendaraan', 'Kendaraan::index');
$routes->get('kendaraan/create', 'Kendaraan::create');
$routes->get('kendaraan/edit/(:num)', 'Kendaraan::edit/$1');
$routes->post('kendaraan/save', 'Kendaraan::save');
$routes->post('kendaraan/delete/(:num)', 'Kendaraan::delete/$1');

// --- Manajemen spare part ---
$routes->get('sparepart', 'Sparepart::index');
$routes->get('sparepart/create', 'Sparepart::create');
$routes->get('sparepart/edit/(:num)', 'Sparepart::edit/$1');
$routes->post('sparepart/save', 'Sparepart::save');
$routes->post('sparepart/delete/(:num)', 'Sparepart::delete/$1');

// --- Manajemen bengkel ---
$routes->get('bengkel', 'Bengkel::index');
$routes->get('bengkel/create', 'Bengkel::create');
$routes->get('bengkel/edit/(:num)', 'Bengkel::edit/$1');
$routes->post('bengkel/save', 'Bengkel::save');
$routes->post('bengkel/delete/(:num)', 'Bengkel::delete/$1');

// --- Input service + monitoring ---
$routes->get('service', 'Service::index');              // monitoring list
$routes->get('service/create', 'Service::create');      // form input service
$routes->post('service/save', 'Service::save');
$routes->get('service/detail/(:num)', 'Service::detail/$1');
$routes->post('service/delete/(:num)', 'Service::delete/$1');
$routes->post('service/status/(:num)', 'Service::status/$1');
$routes->get('service/api/kendaraan/(:num)', 'Service::apiKendaraan/$1'); // data kendaraan (json)

// --- Setting notifikasi service berikutnya ---
$routes->get('notifikasi', 'Notifikasi::index');
$routes->post('notifikasi/save', 'Notifikasi::save');
$routes->post('notifikasi/toggle', 'Notifikasi::toggle');
