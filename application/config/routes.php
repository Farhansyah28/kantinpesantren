<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

// Default routes
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Root URL redirect to welcome
$route[''] = 'welcome';

// Auth routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['register'] = 'auth/register';
$route['change-password'] = 'auth/change_password';
$route['auth'] = 'auth/login'; // Redirect /auth ke login

// Dashboard routes
$route['dashboard'] = 'dashboard/index';
$route['profile'] = 'dashboard/profile';
$route['dashboard/filter_database'] = 'dashboard/filter_database';

// Santri routes
$route['santri'] = 'santri/index';
$route['santri/create'] = 'santri/create';
$route['santri/edit/(:num)'] = 'santri/edit/$1';
$route['santri/view/(:num)'] = 'santri/view/$1';
$route['santri/delete/(:num)'] = 'santri/delete/$1';
$route['santri/update/(:num)'] = 'santri/update/$1';
$route['santri/store'] = 'santri/store';

// Tabungan routes
$route['tabungan'] = 'tabungan/index';
$route['tabungan/setoran'] = 'tabungan/setoran';
$route['tabungan/penarikan'] = 'tabungan/penarikan';
$route['tabungan/transfer_kategori'] = 'tabungan/transfer_kategori';
$route['tabungan/transfer_antar_santri'] = 'tabungan/transfer_antar_santri';
$route['tabungan/riwayat'] = 'tabungan/riwayat';
$route['tabungan/riwayat/(:num)'] = 'tabungan/riwayat/$1';
$route['tabungan/get_saldo/(:num)'] = 'tabungan/get_saldo/$1';
$route['tabungan/search_santri'] = 'tabungan/search_santri';
$route['tabungan/get_santri_info'] = 'tabungan/get_santri_info';

// Kantin routes (existing)
$route['menu'] = 'menu/index';
$route['menu/create'] = 'menu/create';
$route['menu/edit/(:num)'] = 'menu/edit/$1';
$route['menu/delete/(:num)'] = 'menu/delete/$1';
$route['menu/update/(:num)'] = 'menu/update/$1';
$route['menu/store'] = 'menu/store';
$route['menu/stok_management'] = 'menu/stok_management';
$route['menu/riwayat_stok'] = 'menu/riwayat_stok';
$route['menu/tambah_stok_form/(:num)'] = 'menu/tambah_stok_form/$1';
$route['menu/tambah_stok'] = 'menu/tambah_stok';
$route['menu/kurangi_stok/(:num)'] = 'menu/kurangi_stok/$1';

$route['transaksi'] = 'transaksi/index';
$route['transaksi/create'] = 'transaksi/create';
$route['transaksi/edit/(:num)'] = 'transaksi/edit/$1';
$route['transaksi/delete/(:num)'] = 'transaksi/delete/$1';
$route['transaksi/update/(:num)'] = 'transaksi/update/$1';
$route['transaksi/store'] = 'transaksi/store';
$route['transaksi/beli'] = 'transaksi/beli';
$route['transaksi/riwayat_santri'] = 'transaksi/riwayat_santri';

// Transaksi Kantin routes
$route['transaksi/search_santri'] = 'transaksi/search_santri';
$route['transaksi/search_menu'] = 'transaksi/search_menu';
$route['transaksi/get_santri_info'] = 'transaksi/get_santri_info';
$route['transaksi/process_transaction'] = 'transaksi/process_transaction';
$route['transaksi/debug'] = 'transaksi/debug';

// POS routes
$route['pos'] = 'pos/index';
$route['pos/riwayat'] = 'pos/riwayat';
$route['pos/riwayat_hari_ini'] = 'pos/riwayat_hari_ini';
$route['pos/process_transaction'] = 'pos/process_transaction';
$route['pos/get_santri_info'] = 'pos/get_santri_info';
$route['pos/get_menu_info'] = 'pos/get_menu_info';
$route['pos/search_santri'] = 'pos/search_santri';
$route['pos/search_menu'] = 'pos/search_menu';

// Activity Logs routes
$route['activity-logs'] = 'activity_logs/index';
$route['activity-logs/view/(:num)'] = 'activity_logs/view/$1';
$route['activity-logs/export'] = 'activity_logs/export';
$route['activity-logs/dashboard'] = 'activity_logs/dashboard';
$route['activity-logs/clean'] = 'activity_logs/clean_old_logs';
$route['activity-logs/test_log'] = 'activity_logs/test_log';
