<?php

/*
  Author: SM <sm@mifon.tk>
  Pupose: define application routes in this page
  Changes:
  001 SM 01-05-2017 Initial creation
*/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type as Type;

$app->get('/css/{styleName}', function ($styleName) {
    $file = __DIR__.'/../views/css/'.$styleName;
    header("Content-type: text/css", true);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}); // define style action
$app->get('/js/{scriptName}', function ($scriptName) {
    $file = __DIR__.'/../views/js/'.$scriptName;
    header("Content-type: text/javascript", true);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}); // define script action

$app->get('/login', 'MyApp\SilexApplication::login')->bind('login');
$app->get('/logout', 'MyApp\SilexApplication::logout')->bind('logout');
$app->mount('/organisation', new MyApp\Controller\OrganisationController());
$app->mount('/organisation/{oid}/employee', new MyApp\Controller\UserController());
$app->mount('/user', new MyApp\Controller\UserController());
$app->mount('/search', new MyApp\Controller\SearchController());
$app->mount('/contact', new MyApp\Controller\ContactController());

$app->get('/about', 'MyApp\SilexApplication::about')->bind('about'); // define about action
$app->get('/', 'MyApp\SilexApplication::home')->bind('homepage'); // define default action
    

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
	$access = array(
		//'last_username' => $username,
		'code' => $code,
		'error' => $e->getMessage(),
	);
	
	$access = array_merge($app['app.access'], $access);
	$app['request'] = $request;
	$access['app'] = $app;

    if ($app['debug']) {
		return;      
    }
	//return new Response($app['twig']->render('Exception/error404.html.twig', $access), $code);
	//return new Response($app['twig']->render('errors/404.html.twig', $access), $code);

    // 404.html, or 40x.html, or 4xx.html, or error.html

    $templates = array(

        'errors/'.$code.'.html.twig',

        'errors/'.substr($code, 0, 2).'x.html.twig',

        'errors/'.substr($code, 0, 1).'xx.html.twig',

        'errors/default.html.twig',

    );



    return new Response($app['twig']->resolveTemplate($templates)->render($access), $code);

});

$app->after(function (Request $request, Response $response) {
    //echo "<p>AFTER1</p>".$response->getContent()."<p>AFTER2</p>";
    //var_dump($response);
    $app = MyApp\SilexApplication::instance();
    $token = $app['security.token_storage']->getToken();
    $user = empty($token) ? '' : $token->getUser();
    $username = is_object($user) ? $user->getUsername() : $user;
    $access = array(
        'last_username' => $username,
    );
    
    $access = array_merge($app['app.access'], $access);
    $content = $response->getContent();
    $header = $app['twig']->render('page_start.html.twig', $access);
    $footer = $app['twig']->render('page_end.html.twig', $access);
    //echo "<pre>new_content=".print_r($access,1)."</pre><br/>\n";
    $temp = "<div id='temp'>" .
    //"<pre>".print_r($app['db.options'], 1)."</pre>".
    "</div>";
    $response->setContent($header.$content.$temp.$footer);
});
