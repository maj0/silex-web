<?php

namespace MyApp;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AboutController implements ControllerProviderInterface {

  public function connect(Application $app) {
    $factory = $app['controllers_factory'];
    $factory->get('/','MyApp\AboutController::home');
    $factory->get('foo','MyApp\AboutController::doFoo');
    return $factory;
  }
 
  public function home() {
    return 'Welcome to my About page!';
  }
 
  public function doFoo() {
    return 'Bar';
  }
}
