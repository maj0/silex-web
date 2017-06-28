<?php

namespace MyApp;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AuthController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get('/login', 'MyApp\AuthController::home');
        //$factory->get('/logout','MyApp\AuthController::logout');
        return $factory;
    }

    public function home(Request $req, Application $app)
    {
        $user = array(
            'name' => 'admin',
            'email' => 'admin@mifon.tk',
            'role' => 1,
        );
        $access = array(
            'error'         => $app['security.last_error']($req),
            'last_username' => $app['session']->get('_security.last_username'),
        );
        $access['logged'] = $user;
        return $app['twig']->render('login.html.twig', $access);
        return 'Welcome to my About page!';
    }
}
