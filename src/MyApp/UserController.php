<?php

namespace MyApp;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class UserController implements ControllerProviderInterface {

  public function connect(Application $app) {
    $factory = $app['controllers_factory'];
    $factory->get('/','MyApp\UserController::home');
    $factory->match('add','MyApp\UserController::doAdd');
    $factory->match('edit/{id}','MyApp\UserController::doEdit');
    $factory->match('delete/{id}','MyApp\UserController::doDelete');
    $factory->match('show/{id}','MyApp\UserController::doShow');
    return $factory;
  }
 
  public function home(Request $req, Application $app) {
	try {
	return $app['twig']->render('user.home.html.twig', array());
	} catch (Exception $e) {
		$err = "Error:" . $e->getMessage();
	}
    return __CLASS__ . " home err=$err";// . __DIR__.'/../../templates/user.home.html.twig';
  }
 
  public function doAdd() {
    return __FUNCTION__ . ' new user';
  }
  
  public function doEdit(Request $req, $id) {
    return __FUNCTION__ . " user $id";
  }
  
  public function doDelete(Request $req, $id) {
    return __FUNCTION__ . " user $id";
  }
  
  public function doShow(Request $req, $id) {
    return __FUNCTION__ . " user $id";
  }
}
