<?php



// configure your app for the production environment



$app['twig.path'] = array(__DIR__.'/../views');

//$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app['swiftmailer.options'] = array(
    'host' => 'host',
    'port' => '25',
    'username' => 'username',
    'password' => 'password',
    'encryption' => null,
    'auth_mode' => null
);