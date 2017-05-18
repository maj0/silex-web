<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



//Request::setTrustedProxies(array('127.0.0.1'));



$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array());
})

->bind('homepage')

;
// About Controller
$app->mount('/about', new MyApp\AboutController());
// User Controller
$app->mount('/user', new MyApp\UserController());
// Organisation Controller
$app->mount('/organisation', new MyApp\OrganisationController());
// Employee Controller
$app->mount('/organisation/{oid}/employee', new MyApp\UserController());



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
