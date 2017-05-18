<?php

use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class Silex_Application extends Silex\Application
{
	/*public function abort($statusCode, $message = '', array $headers = array())
	{
		die($message);
	}*/
	public static function instance()
	{
		static $app;
		if(empty($app))
		{
			$app = new static();
			$app['debug'] = true;

			// register monolog service provider // logger
			$app->register(new Silex\Provider\MonologServiceProvider(), array(
				'monolog.logfile' => __DIR__.'/../logs/app.log',
				'monolog.class_path' => __DIR__.'/../vendor/monolog/src',
			));
			
			// register value validator service
			$app->register(new Silex\Provider\ValidatorServiceProvider());

			// register session provider //
			$app->register(new Silex\Provider\SessionServiceProvider());

			// register twig template path  // composer require twig/twig
			$app->register(new Silex\Provider\TwigServiceProvider(), array(
				'twig.path' => __DIR__.'/../views',
			));
			//$app->register(new UrlGeneratorServiceProvider());

			// connect to database // composer require doctrine/dbal
			$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
				'db.options' => array(
					'dbname' => 'pes',      // DATABASE to use
					'user' => 'homestead',  // DATABASE user
					'password' => 'secret', // DATABASE password
					'host' => '127.0.0.1',
					'driver' => 'pdo_mysql'
				),
			));



			// register & enable security
			$app->register(new Silex\Provider\SecurityServiceProvider(), $app['security.firewalls'] = array(
				'public' => array(
					'pattern' => '^(/login|/css/.*|/js/.*)$',
					'anonymous' => true,
				),
				'secured' => array(
					'pattern' => '^.*$',
					'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
					'logout' => array('logout_path' => '/admin/logout', 'invalidate_session' => true),
					//'users' => array(
					//    'admin' => array('ROLE_ADMIN', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
					//),
					'users' => function () use ($app) {
						return new UserProvider($app['db']);
					},
				),
			));
			
			// register password encoder plaintext
			$app['security.default_encoder'] = function ($app) {
				//error_log("use plain text encoder!");
				// Plain text (e.g. for debugging)
				return new PlaintextPasswordEncoder();
			};
		} // end-if
		return $app;
	} // end-function
	
	public static function login(Silex_Application $app) /*use ($app)*/ {
		$request = Request::createFromGlobals();
		$last_username = $app['session']->get('_security.last_username');
		//error_log("last_username is $last_username");
		return $app['twig']->render('login.html', array(
			'error'         => $app['security.last_error']($request),
			'last_username' => $app['session']->get('_security.last_username'),
		));
	}
	
	public static function home(Silex_Application $app) {
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$db_user = User::getUser($username);
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$access = array(
			'last_username' => $username,
			'header_content' => __DIR__.'/../views/header_content.html',
			'footer_content' => __DIR__.'/../views/footer_content.html',
		);

		return $app['twig']->render('home.html', $access);
	} // end-home function
	
	public static function about(Silex_Application $app) {
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$db_user = User::getUser($username);
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$access = array(
			'last_username' => $username,
			'header_content' => __DIR__.'/../views/header_content.html',
			'footer_content' => __DIR__.'/../views/footer_content.html',
		);

		return $app['twig']->render('about.html', $access);
	} // end-about function
	
}  // end-application
