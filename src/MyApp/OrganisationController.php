<?php

namespace MyApp;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class OrganisationController implements ControllerProviderInterface {

  public function connect(Application $app) {
    $factory = $app['controllers_factory'];
    $factory->get('/','MyApp\OrganisationController::home');
    $factory->match('add','MyApp\OrganisationController::doAdd');
    $factory->match('edit/{id}','MyApp\OrganisationController::doEdit');
    $factory->match('delete/{id}','MyApp\OrganisationController::doDelete');
    $factory->match('show/{id}','MyApp\OrganisationController::doShow');
    return $factory;
  }
 
  public function home() {
    return __CLASS__ . ' home';
  }
 
  public function doAdd() {
    return __FUNCTION__ . ' new organisation';
  }
  
  public function doEdit(Request $req, $id) {
    return __FUNCTION__ . " organisation $id";
  }
  
  public function doDelete(Request $req, $id) {
    return __FUNCTION__ . " organisation $id";
  }
  
  public function doShow(Request $req, $id) {
    return __FUNCTION__ . " organisation $id";
  }
}
