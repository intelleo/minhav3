<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// routes home
$routes->get('/', 'Home::index');
$routes->get('/login', 'AuthController::login', ['filter' => 'guest']);
$routes->get('/register', 'AuthController::register');
$routes->post('/login', 'AuthController::doLogin', ['filter' => 'guest']);
$routes->post('/doRegister', 'AuthController::doRegister', ['filter' => 'guest']);

// Admin auth routes (terpisah dari user)
$routes->get('/admin/login', 'AdminAuthController::login', ['filter' => 'adminguest']);
$routes->post('/admin/login', 'AdminAuthController::doLogin', ['filter' => 'adminguest']);
$routes->get('/admin/logout', 'AdminAuthController::logout');

// Admin dashboard & pages (SPA shell) - protected
$routes->group('Admin', ['filter' => 'adminauth'], function ($routes) {
  $routes->get('Dashboard', 'Admin\Dashboard::index');
  // $routes->get('', 'Admin\Dashboard::index');

  // Master Data routes
  $routes->get('MasterData', 'Admin\MasterData::index');
  $routes->get('MasterData/users', 'Admin\MasterData::users');
  $routes->get('MasterData/chatbot', 'Admin\MasterData::chatbot');
  $routes->get('MasterData/mading', 'Admin\MasterData::mading');

  // Master Data AJAX routes
  $routes->post('MasterData/updateUserStatus', 'Admin\MasterData::updateUserStatus');
  $routes->post('MasterData/deleteUser', 'Admin\MasterData::deleteUser');
  $routes->post('MasterData/updateUser', 'Admin\MasterData::updateUser');
  $routes->post('MasterData/addUser', 'Admin\MasterData::addUser');
  $routes->post('MasterData/addLayanan', 'Admin\MasterData::addLayanan');
  $routes->post('MasterData/updateLayanan', 'Admin\MasterData::updateLayanan');
  $routes->post('MasterData/deleteLayanan', 'Admin\MasterData::deleteLayanan');
  $routes->get('MasterData/getLayanan', 'Admin\MasterData::getLayanan');
  // Mading management AJAX
  $routes->post('MasterData/addMading', 'Admin\MasterData::addMading');
  $routes->post('MasterData/updateMading', 'Admin\MasterData::updateMading');
  $routes->post('MasterData/updateMadingStatus', 'Admin\MasterData::updateMadingStatus');
  $routes->post('MasterData/deleteMading', 'Admin\MasterData::deleteMading');
  $routes->get('MasterData/getMading', 'Admin\MasterData::getMading');

  // Test route without CSRF filter
  $routes->post('MasterData/addUserTest', 'Admin\MasterData::addUserTest', ['filter' => 'adminauth']);

  // E-mading routes
  $routes->get('Mading', 'Admin\Mading::index');
  $routes->get('Mading/list-html', 'Admin\Mading::listHtml');
  $routes->get('Mading/detail/(:num)', 'Admin\Mading::detail/$1');
  $routes->post('Mading/create', 'Admin\Mading::create');
  $routes->post('Mading/komentar', 'Admin\Mading::addComment');
  $routes->post('Mading/like', 'Admin\Mading::toggleLike');
  $routes->get('Mading/comments/(:num)', 'Admin\Mading::loadComments/$1');

  // Reports routes
  $routes->get('Reports', 'Admin\Reports::index');
  $routes->post('Reports/replyComment', 'Admin\Reports::replyComment');

  // Activity Logs routes (replace Settings)
  $routes->get('Settings', 'Admin\ActivityLogs::index');
  $routes->get('ActivityLogs', 'Admin\ActivityLogs::index');
});


// grouping user after login
$routes->group('', ['filter' => 'auth'], function ($routes) {
  // home
  $routes->get('/Dashboard', 'UserController\UCDashboard::indexHome');
  $routes->get('/Mading', 'UserController\UCMading::indexMading');
  $routes->get('/Mading/list-html', 'UserController\UCMading::listMadingHtml');
  $routes->get('/Mading/detail/(:num)', 'UserController\UCMading::detail/$1');
  $routes->post('/Mading/komentar', 'UserController\UCMading::addComment');
  $routes->post('/Mading/like', 'UserController\UCMading::toggleLike');
  $routes->post('/Mading/context-menu-action', 'UserController\UCMading::contextMenuAction');
  $routes->post('/Mading/update-comment', 'UserController\UCMading::updateComment');
  // Ajax: komentar pagination
  $routes->get('/Mading/comments/(:num)', 'UserController\UCMading::loadComments/$1');
  $routes->get('/Chatbot', 'UserController\UCChatbot::indexChatbot');
  $routes->get('/Notifications', 'UserController\UCNotifications::index');
  $routes->get('/Notifications/count', 'UserController\UCNotifications::count');
  $routes->post('/Notifications/seen/(:num)', 'UserController\UCNotifications::seen/$1');
  $routes->post('/Notifications/dismiss/(:num)', 'UserController\UCNotifications::dismiss/$1');
  $routes->get('/Likes', 'UserController\UCLikes::index');

  $routes->get('/Profile', 'UserController\UCProfile::index');
  // Profile updates
  $routes->post('/Profile/update-photo', 'UserController\UCProfile::updatePhoto');
  $routes->post('/Profile/delete-photo', 'UserController\UCProfile::deletePhoto');
  $routes->post('/Profile/update-bio', 'UserController\UCProfile::updateBio');
  $routes->post('/Profile/update-password', 'UserController\UCProfile::updatePassword');
  $routes->get('/logout', 'AuthController::logout');
});





// Test route outside Admin group to bypass CSRF filter
$routes->post('Admin/MasterData/addLayananTest', 'Admin\MasterData::addLayananTest', ['filter' => 'adminauth']);
$routes->post('Admin/MasterData/updateLayananTest', 'Admin\MasterData::updateLayananTest', ['filter' => 'adminauth']);
$routes->post('Admin/MasterData/deleteLayananTest', 'Admin\MasterData::deleteLayananTest', ['filter' => 'adminauth']);

// api database informasi layanan informasi untuk flowise ai
$routes->group('api', function ($routes) {
  $routes->get('layanan', 'Api\Layanan::index');
  $routes->get('layanan/(:segment)', 'Api\Layanan::index/$1'); // filter kategori

});
