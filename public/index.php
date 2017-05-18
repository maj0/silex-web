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

$app->get('/organisations', 'Organisation::olist');
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

$app->get('/users', 'User::ulist');
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


$app->error(function (\Exception $e, Request $request, $code) use ($app) {

    if ($app['debug']) {

        return;

    }



    // 404.html, or 40x.html, or 4xx.html, or error.html

    $templates = array(

        'errors/'.$code.'.html.twig',

        'errors/'.substr($code, 0, 2).'x.html.twig',

        'errors/'.substr($code, 0, 1).'xx.html.twig',

        'errors/default.html.twig',

    );



    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);

});

$app->after(function (Request $request, Response $response) {
    //echo "<p>AFTER1</p>".$response->getContent()."<p>AFTER2</p>";
	//var_dump($response);
	$app = Silex_Application::instance();
	$token = $app['security.token_storage']->getToken();
	$user = empty($token) ? '' : $token->getUser();
	$username = is_object($user) ? $user->getUsername() : $user;			
	$access = array(
		'last_username' => $username,
	);
	$content = $response->getContent();
	$header = $app['twig']->render('page_start.html', $access);
	$footer = $app['twig']->render('page_end.html', $access);
	//echo "<pre>new_content=".$new_conent."</pre><br/>\n";
	$response->setContent($header.$content.$footer);
});
$app->run();
