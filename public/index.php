<?php

/*
 * Author: SM <sm@mifon.tk>
 * Changes:
 * 001 SM 10-05-2017 Initial creation
 */

//define('MYAPP_PUBLIC_ROOT', __DIR__);

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/MyApp/userprovider.php';
require_once __DIR__.'/../src/MyApp/application.php';


$app = MyApp\SilexApplication::instance();

require_once __DIR__.'/../src/routes.php';

try {
    $app->run();
} catch (Exception $e) {
    echo "Errror: $e<br/>\n";
}
