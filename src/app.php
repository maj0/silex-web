<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

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
		}
		return $app;
	} // end instance
} // end Silex_Application

$app = Silex_Application::instance();

$app->register(new ServiceControllerServiceProvider());

$app->register(new AssetServiceProvider());

$app->register(new TwigServiceProvider());

$app->register(new HttpFragmentServiceProvider());

$app['twig'] = $app->extend('twig', function ($twig, $app) {

    // add custom globals, filters, tags, ...



    return $twig;

});



return $app;