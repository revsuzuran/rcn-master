<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/login', 'Login::login');
$routes->post('/do_auth', 'Login::do_auth');
$routes->get('/do_unauth', 'Login::do_unauth');
// $routes->post('mantab', 'Home::do_upload_csv');

$routes->get('/', 'Rekon::data_rekon_master');
$routes->get('rekon', 'Rekon::data_rekon_master');
$routes->get('rekon/add', 'Rekon::add_rekon_master');
$routes->post('rekon/upload', 'Rekon::upload_data_rekon');
$routes->post('rekon/save_delimiter', 'Rekon::save_delimiter');
$routes->get('rekon/cleansing_data/(:any)', 'Rekon::cleansing_data');
$routes->get('rekon/cleansing_data', 'Rekon::cleansing_data');
$routes->post('rekon/save_cleansing', 'Rekon::save_cleansing');
$routes->get('rekon/add_rekon_next', 'Rekon::add_rekon_next');
$routes->get('rekon/add_compare', 'Rekon::add_rekon_data_to_compare');
$routes->post('rekon/add_kolom_compare', 'Rekon::add_kolom_compare');
$routes->post('rekon/rm_kolom_compare', 'Rekon::rm_kolom_compare');
$routes->post('rekon/add_kolom_sum', 'Rekon::add_kolom_sum');
$routes->post('rekon/rm_kolom_sum', 'Rekon::rm_kolom_sum');
$routes->get('rekon/rekon_preview', 'Rekon::add_rekon_preview');
$routes->get('rekon/add_rekon_finish', 'Rekon::add_rekon_finish');
$routes->post('rekon/save_compare', 'Rekon::save_compare');
$routes->get('rekon/rekon_preview_sum', 'Rekon::add_rekon_preview_sum');
$routes->post('rekon/save_compare_sum', 'Rekon::save_compare_sum');
$routes->get('rekon/rekon_result', 'Rekon::rekon_result');
$routes->post('rekon/rekon_result_post', 'Rekon::rekon_result_post');
$routes->get('rekon/delimiter', 'Rekon::add_rekon_delimiter');
$routes->get('profil', 'Setting::profil');
$routes->post('update_user', 'Setting::update_user');


$routes->get('ftp', 'Setting::ftp');
$routes->get('add_ftp', 'Setting::add_ftp');
$routes->post('save_ftp', 'Setting::save_ftp');
$routes->get('edit_ftp/(:any)', 'Setting::edit_ftp');
$routes->post('update_ftp', 'Setting::update_ftp');
$routes->post('rm_ftp', 'Setting::rm_ftp');


$routes->get('database', 'Setting::database');
$routes->get('add_database', 'Setting::add_database');
$routes->post('save_database', 'Setting::save_database');
$routes->get('edit_database/(:any)', 'Setting::edit_database');
$routes->post('update_database', 'Setting::update_database');
$routes->post('rm_database', 'Setting::rm_database');


$routes->get('rekon/generate_pdf', 'Rekon::generate_pdf');
$routes->get('rekon/hehepdf', 'Rekon::hehepdf');




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
