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

$routes->get('/login', 'Login::login');
$routes->post('/do_auth', 'Login::do_auth');
$routes->get('/do_unauth', 'Login::do_unauth');
// $routes->get('/', 'Rekon::data_rekon_master');
$routes->get('/', 'homeController::index');
$routes->get('rekon', 'Rekon::data_rekon_master');
$routes->get('rekon_sch', 'RekonSch::data_rekon_master');
$routes->get('data_transaksi', 'DataTransaksi::data_master');

$routes->group('rekon', function ($routes) {
    $routes->get('rekon_sch', 'Rekon::add_rekon_sch');
    $routes->get('add', 'Rekon::add_rekon_master');
    $routes->post('upload', 'Rekon::upload_data_rekon');
    $routes->post('save_delimiter', 'Rekon::save_delimiter');
    $routes->get('cleansing_data/(:any)', 'Rekon::cleansing_data');
    $routes->get('cleansing_data', 'Rekon::cleansing_data');
    $routes->post('save_cleansing', 'Rekon::save_cleansing');
    $routes->get('add_rekon_next', 'Rekon::add_rekon_next');
    $routes->get('add_compare', 'Rekon::add_rekon_data_to_compare');
    $routes->post('add_kolom_compare', 'Rekon::add_kolom_compare');
    $routes->post('rm_kolom_compare', 'Rekon::rm_kolom_compare');
    $routes->post('add_kolom_sum', 'Rekon::add_kolom_sum');
    $routes->post('rm_kolom_sum', 'Rekon::rm_kolom_sum');
    $routes->get('rekon_preview', 'Rekon::add_rekon_preview');
    $routes->get('add_rekon_finish', 'Rekon::add_rekon_finish');
    $routes->post('save_compare', 'Rekon::save_compare');
    $routes->get('rekon_preview_sum', 'Rekon::add_rekon_preview_sum');
    $routes->post('save_compare_sum', 'Rekon::save_compare_sum');
    $routes->get('rekon_result', 'Rekon::rekon_result');
    $routes->get('rekon_result_amount', 'Rekon::rekon_result_amount');
    $routes->post('rekon_result_post', 'Rekon::rekon_result_post');
    $routes->get('delimiter', 'Rekon::add_rekon_delimiter');
    $routes->post('upload_with_setting', 'Rekon::upload_with_setting');

    $routes->get('generate_pdf', 'Rekon::generate_pdf');
    $routes->get('generate_pdf2', 'Rekon::generate_pdf2');
    $routes->get('hehepdf', 'Rekon::hehepdf');
    
    $routes->get('export_unmatch/(:any)/(:any)', 'Rekon::export_unmatch');
    $routes->get('export_match/(:any)/(:any)', 'Rekon::export_match');
    $routes->get('export_all/(:any)', 'Rekon::export_all');


    $routes->get('retry_process/(:any)', 'Rekon::retry_process');
});

$routes->group('rekon_sch', function ($routes) {
    $routes->get('add', 'RekonSch::add_rekon');
    $routes->post('submit', 'RekonSch::save_data_sch');
    $routes->get('data_rekon_sch', 'RekonSch::data_rekon_sch');
    $routes->post('data_rekon_sch_temp', 'RekonSch::data_rekon_sch_temp');
    $routes->post('process_data_sch', 'RekonSch::process_data_sch');
    $routes->post('update_rekon', 'RekonSch::update_rekon');
    $routes->get('process_data_sch_cek', 'RekonSch::process_data_sch_cek');    
    $routes->get('rekon_preview', 'RekonSch::add_rekon_preview');
    $routes->post('save_compare', 'RekonSch::save_compare');
    $routes->get('add_rekon_finish', 'RekonSch::add_rekon_finish');
});

$routes->group('rekon_unmatch_bulanan', function ($routes) {
    $routes->get('/', 'RekonUnmatchBulanan::data_rekon');
    $routes->get('add', 'RekonUnmatchBulanan::add_rekon');
    $routes->post('proses', 'RekonUnmatchBulanan::proses_rekon'); 
    $routes->get('cleansing_data/(:any)', 'RekonUnmatchBulanan::cleansing_data');
    $routes->get('cleansing_data', 'RekonUnmatchBulanan::cleansing_data');
    $routes->get('cleansing_data_dua/(:any)', 'RekonUnmatchBulanan::cleansing_data_dua');
    $routes->get('cleansing_data_dua', 'RekonUnmatchBulanan::cleansing_data_dua');
    $routes->post('save_cleansing', 'RekonUnmatchBulanan::save_cleansing');
    $routes->get('add_compare', 'RekonUnmatchBulanan::add_rekon_data_to_compare');
    $routes->post('add_kolom_compare', 'RekonUnmatchBulanan::add_kolom_compare');
    $routes->post('rm_kolom_compare', 'RekonUnmatchBulanan::rm_kolom_compare');
    $routes->post('add_kolom_sum', 'RekonUnmatchBulanan::add_kolom_sum');
    $routes->post('rm_kolom_sum', 'RekonUnmatchBulanan::rm_kolom_sum');
    $routes->get('rekon_preview', 'RekonUnmatchBulanan::add_rekon_preview');
    $routes->get('add_rekon_finish', 'RekonUnmatchBulanan::add_rekon_finish');
    $routes->post('save_compare', 'RekonUnmatchBulanan::save_compare');

});

$routes->group('data_transaksi', function ($routes) {
    $routes->get('add', 'DataTransaksi::add_transaksi');
    $routes->post('upload', 'DataTransaksi::upload_data_transaksi');
    $routes->post('get_collection_view', 'DataTransaksi::get_collection_view');
    $routes->post('save_collection_view', 'DataTransaksi::save_collection_view');
    $routes->get('delimiter', 'DataTransaksi::add_transaksi_delimiter');
    $routes->post('save_delimiter', 'DataTransaksi::save_delimiter');
    $routes->get('cleansing_data/(:any)', 'DataTransaksi::cleansing_data');
    $routes->get('cleansing_data', 'DataTransaksi::cleansing_data');
    $routes->post('save_cleansing', 'DataTransaksi::save_cleansing');
    $routes->get('add_compare', 'DataTransaksi::add_transaksi_data_to_compare');
    $routes->post('add_kolom_compare', 'DataTransaksi::add_kolom_compare');
    $routes->post('rm_kolom_compare', 'DataTransaksi::rm_kolom_compare');
    $routes->post('add_kolom_sum', 'DataTransaksi::add_kolom_sum');
    $routes->post('rm_kolom_sum', 'DataTransaksi::rm_kolom_sum');
});

$routes->group('rekon_transaksi', function ($routes) {
    $routes->get('add', 'RekonTransaksi::add_rekon_master');
    $routes->post('upload', 'RekonTransaksi::upload_data_rekon');
    $routes->post('save_delimiter', 'RekonTransaksi::save_delimiter');
    $routes->get('cleansing_data/(:any)', 'RekonTransaksi::cleansing_data');
    $routes->get('cleansing_data', 'RekonTransaksi::cleansing_data');
    $routes->post('save_cleansing', 'RekonTransaksi::save_cleansing');
    $routes->get('add_rekon_next', 'RekonTransaksi::add_rekon_next');
    $routes->get('add_compare', 'RekonTransaksi::add_rekon_data_to_compare');
    $routes->post('add_kolom_compare', 'RekonTransaksi::add_kolom_compare');
    $routes->post('rm_kolom_compare', 'RekonTransaksi::rm_kolom_compare');
    $routes->post('add_kolom_sum', 'RekonTransaksi::add_kolom_sum');
    $routes->post('rm_kolom_sum', 'RekonTransaksi::rm_kolom_sum');
    $routes->get('rekon_preview', 'RekonTransaksi::add_rekon_preview');
    $routes->get('add_rekon_finish', 'RekonTransaksi::add_rekon_finish');
    $routes->post('save_compare', 'RekonTransaksi::save_compare');
    $routes->get('rekon_preview_sum', 'RekonTransaksi::add_rekon_preview_sum');
    $routes->post('save_compare_sum', 'RekonTransaksi::save_compare_sum');
    $routes->get('rekon_result', 'RekonTransaksi::rekon_result');
    $routes->get('rekon_result_amount', 'RekonTransaksi::rekon_result_amount');
    $routes->post('rekon_result_post', 'RekonTransaksi::rekon_result_post');
    $routes->get('delimiter', 'RekonTransaksi::add_rekon_delimiter');
    $routes->post('upload_with_setting', 'RekonTransaksi::upload_with_setting');

    $routes->get('generate_pdf', 'RekonTransaksi::generate_pdf');
    $routes->get('generate_pdf2', 'RekonTransaksi::generate_pdf2');
    $routes->get('hehepdf', 'RekonTransaksi::hehepdf');
    
    $routes->get('export_unmatch/(:any)/(:any)', 'RekonTransaksi::export_unmatch');
    $routes->get('export_match/(:any)/(:any)', 'RekonTransaksi::export_match');
    $routes->get('export_all/(:any)', 'RekonTransaksi::export_all');

    $routes->get('retry_process/(:any)', 'RekonTransaksi::retry_process');

    $routes->post('get_collection_view', 'RekonTransaksi::get_collection_view');
});

$routes->group('rekon_transaksi_sch', function ($routes) {
    $routes->get('add', 'RekonTransaksiSch::add_rekon');
    $routes->post('submit', 'RekonTransaksiSch::save_data_sch');
    $routes->get('data_rekon_sch', 'RekonTransaksiSch::data_rekon_sch');
    $routes->post('data_rekon_sch_temp', 'RekonTransaksiSch::data_rekon_sch_temp');
    $routes->post('process_data_sch', 'RekonTransaksiSch::process_data_sch');
    $routes->post('update_rekon', 'RekonTransaksiSch::update_rekon');
    $routes->get('process_data_sch_cek', 'RekonTransaksiSch::process_data_sch_cek');    
    $routes->get('rekon_preview', 'RekonTransaksiSch::add_rekon_preview');
    $routes->post('save_compare', 'RekonTransaksiSch::save_compare');
    $routes->get('add_rekon_finish', 'RekonTransaksiSch::add_rekon_finish');
});

$routes->group('mitra', function ($routes) {
    $routes->get('/', 'Mitra::index', ['filter' => 'isadmin']);
    $routes->get('profil', 'Setting::profil_mitra');
});

$routes->post('mitra_temp', 'Mitra::mitra_temp');
$routes->get('add', 'Mitra::add', ['filter' => 'isadmin']);
$routes->post('save_mitra', 'Mitra::save_mitra', ['filter' => 'isadmin']);
$routes->post('rm_mitra', 'Mitra::rm_mitra', ['filter' => 'isadmin']);
$routes->get('edit_mitra', 'Mitra::edit_mitra');
$routes->post('update_mitra', 'Mitra::update_mitra');

/* Channel */
$routes->group('channel', function ($routes) {
    $routes->get('/', 'Channel::index');
    $routes->get('add', 'Channel::add');
    $routes->post('save', 'Channel::save_channel');
    $routes->post('temp', 'Channel::channel_temp');
    $routes->get('edit', 'Channel::edit_channel');
    $routes->post('update', 'Channel::update_channel');
    $routes->post('rm', 'Channel::rm_channel');
});

/* Bank */
$routes->group('bank', function ($routes) {
    $routes->get('/', 'Bank::index');
    $routes->get('add', 'Bank::add');
    $routes->post('save', 'Bank::save_bank');
    $routes->post('temp', 'Bank::bank_temp');
    $routes->get('edit', 'Bank::edit_bank');
    $routes->post('update', 'Bank::update_bank');
    $routes->post('rm', 'Bank::rm_bank');
});

/* Lain lain */
$routes->post('update_user', 'Setting::update_user_mitra');
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

/* Administrastion  */
$routes->get('profil', 'Setting::profil', ['filter' => 'isadmin']);
$routes->post('update_user', 'Setting::update_user', ['filter' => 'isadmin']);
$routes->post('get_setting', 'Setting::get_setting');
$routes->post('save_setting', 'Setting::save_setting');
$routes->post('get_setting_transaksi', 'Setting::get_setting_transaksi');
$routes->post('save_setting_transaksi', 'Setting::save_setting_transaksi');

/* disbursement and settlement */
$routes->post('add_disbursement', 'Disbursement::add_disbursement', ['filter' => 'isadmin']);
$routes->get('settlement', 'Settlement::data_settlement');
$routes->group('settlement', function ($routes) {
    $routes->get('monit_disbursment', 'Disbursement::monitoring_disburse');
    $routes->get('proses_settlement', 'Settlement::proses_settlement', ['filter' => 'isadmin']);
    $routes->post('proses_temp', 'Settlement::proses_temp', ['filter' => 'isadmin']);
    $routes->post('proses_inq', 'Settlement::proses_inq', ['filter' => 'isadmin']);
    $routes->post('proses_pay', 'Settlement::proses_pay', ['filter' => 'isadmin']);
    $routes->post('proses_payment', 'Settlement::proses_payment', ['filter' => 'isadmin']);
    $routes->post('manual_action', 'Settlement::manual_action', ['filter' => 'isadmin']);
    $routes->post('callback', 'Settlement::callback');
    $routes->post('proses_settlement_choosen', 'Settlement::proses_settlement_choosen');
    $routes->post('proses_cek_split', 'Settlement::proses_cek_split', ['filter' => 'isadmin']);
    $routes->post('proses_split', 'Settlement::proses_split', ['filter' => 'isadmin']);
    $routes->post('detail_disbursment_temp', 'Settlement::detail_disbursment_temp', ['filter' => 'isadmin']);
    $routes->get('detail_disbursment', 'Settlement::detail_disbursment', ['filter' => 'isadmin']);
    $routes->get('order_disbursment', 'Disbursement::order_disbursment');
    
});


/* Mail */
$routes->get('mail', 'Email::get_email');
$routes->post('update_smtp', 'Email::update_email');
$routes->post('send_email', 'Email::kirim_email');

// HOME
$routes->group('home', function ($routes) {
    $routes->get('/', 'homeController::index', ['filter' => 'isadmin']);
});

//mutasi saldo
$routes->group('mutasiSaldo', function ($routes) {
    $routes->get('/', 'MutasiSaldoController::index', ['filter' => 'isadmin']);
    $routes->post('getData', 'MutasiSaldoController::getDataAPI', ['filter' => 'isadmin']);
});


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
