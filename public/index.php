<?php


require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/utils.php';
require_once __DIR__.'/../src/organisation.php';
require_once __DIR__.'/../src/user.php';
require_once __DIR__.'/../src/userprovider.php';
require_once __DIR__.'/../src/application.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


if(Utility\check('debug', $_REQUEST)) error_log("request:".json_encode($_REQUEST));
$app = Silex_Application::instance();

$app->get('/login', 'Silex_Application::login');
//$app->post("/admin/login_check", "admin\controllers\AdminController::loginCheck");
/*$app->post("/admin/login_check", function(Request $request) use ($app) {
	return "Hello login_check";
});*/

$app->get('/organisations', 'Organisation::list');
$app->match('/organisation/add', 'Organisation::add');
$app->get('/organisation/show/{id}', 'Organisation::show');
$app->match("/organisation/edit/{id}", 'Organisation::edit');
//$app->post("/organisation/update", 'Organisation::update');
$app->get('/organisation/employer/add/{id}', 'Organisation::add_employee');
$app->match('/organisation/employee/add/{id}', 'Organisation::add_employee');
//$app->post("/organisation/employee/add", 'Organisation::do_add_employee');
$app->match('/organisation/employer/edit/{id}', /*function(Request $req, $id) use($app) {return */'Organisation::edit_employee'/*($req, $app, $id);}*/);
//$app->get('/organisation/employee/edit/{id}', function(Request $req, $id) use($app) {return Organisation::edit_employee($req, $app, $id);});
$app->match('/organisation/employee/edit/{id}', 'Organisation::edit_employee'/*($req, $app, $id);}*/);
$app->get("/organisation/delete/{id}", 'Organisation::delete');

$app->get('/users', 'User::list');
$app->get('/user/show/{id}', 'User::show');
$app->match('/user/add', 'User::add_user');
$app->match("/user/edit/{id}", 'Organisation::edit_employee');
$app->get("/user/delete/{id}", 'User::delete');

$app->get('/', 'Silex_Application::home'); // define default action
$app->get('/about', 'Silex_Application::about'); // define about action
$app->get('/css/{styleName}', function($styleName) {
	return file_get_contents(__DIR__.'/../views/css/'.$styleName);
}); // define style action
$app->get('/js/{scriptName}', function($scriptName) {
	return file_get_contents(__DIR__.'/../views/js/'.$scriptName);
}); // define script action

$app->run();
