<?php

namespace MyApp;

use Silex\Provider as Provider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;

class SilexApplication extends \Silex\Application
{
    public static function instance()
    {
        static $app;
        if (empty($app)) {
            $app = new static();
            $app['debug'] = true;
            $config = require_once(__DIR__ . '/../../config/config.php');
            $new_config = function ($section, $options) use ($app, $config) {
                $app[$section] = isset($app[$section]) ? array_merge($app[$section], $options) : $options;
                if (isset($config[$section])) {
                    return array_merge($app[$section], $config[$section]);
                }
                return $app[$section];
            };

            // register monolog service provider // logger
            $app->register(new Provider\MonologServiceProvider(), $new_config('monolog.options',array(
                'monolog.logfile' => __DIR__.'/../../logs/app.log',
                'monolog.class_path' => __DIR__.'/../../vendor/monolog/src',
            )));
            
            // register form provider service
            $app->register(new Provider\FormServiceProvider());
            //$app->register(new Provider\TranslationServiceProvider());
            $app->register(new Provider\TranslationServiceProvider(), array(
                'translator.messages' => array(),
                'locale'            => 'en',
                'locale_fallbacks'  => array('en')
            ));
            // register mailer service composer require swiftmailer/swiftmailer
            $app->register(new Provider\SwiftmailerServiceProvider(), $new_config('swiftmailer.options', array(
                'host' => 'smtphost',
                'port' => 25,
                'username' => 'username',
                'password' => 'password',
                'encryption' => null,
                'auth_mode' => null,
            )));
            // register value validator service
            $app->register(new Provider\ValidatorServiceProvider());

            // register session provider //
            $app->register(new Provider\SessionServiceProvider());

            // register twig template path  // composer require twig/twig
            $app->register(new Provider\TwigServiceProvider(), $new_config('twig.options', array(
                'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
                'twig.path' => __DIR__.'/../../views',
            )));

            //$app->register(new UrlGeneratorServiceProvider());

            // connect to database // composer require doctrine/dbal
            $app->register(new Provider\DoctrineServiceProvider(), array(
                'db.options' => $new_config('db.options', array(
                    'dbname' => 'datebase',      // DATABASE to use
                    'user' => 'dbuser',         // DATABASE user
                    'password' => 'dbpassword', // DATABASE password
                    'host' => '127.0.0.1',
                    'driver' => 'pdo_mysql'
                )),
            ));



            // register & enable security
            $app->register(new Provider\SecurityServiceProvider(), $app['security.firewalls'] = array(
                'public' => array(
                    'pattern' => '^(/login|/about|/contact[/]?|/css/.*|/js/.*)$',
                    'anonymous' => true,
                ),
                'secured' => array(
                    'pattern' => '^.*$',
                    'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                    'logout' => array('logout_path' => '/admin/logout', 'invalidate_session' => true),
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
            
            // Register repositories.
            $app['repository.user'] = function ($app) {
                return new Repository\UserRepository($app['db']);
            };
            //error_log("app['repository.user']=".$app['repository.user']);
            $app['repository.organisation'] = function ($app) {
                return new Repository\OrganisationRepository($app['db']);
            };
            $app['repository.search'] = function ($app) {
                return new Repository\SearchRepository($app['db']);
            };
            $app['app.access'] = array('active' => 'homepage');
            
            // mailgun options
            $app['mailgun.options'] = $new_config('mailgun.options', array(
                'APIKEY' => 'PUTYOUR-KEY-IN-CONFIG-FILE',
            ));
        } // end-if empty $app
        return $app;
    } // end-function
    
    public static function login(Request $req, SilexApplication $app) /*use ($app)*/
    {
        $request = Request::createFromGlobals();
        $last_username = $app['session']->get('_security.last_username');
        //error_log("last_username is $last_username");
        return $app['twig']->render('login.html.twig', $app['app.access'] = array_merge($app['app.access'], array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        )));
    }
    
    public static function home(SilexApplication $app)
    {
        $username = "Unknown";
        if (empty($app['session.test'])) {
            $username = $app['security.token_storage']->getToken()->getUser()->getUsername();
            $db_user = Controller\UserController::getUser($username, $app);
            //echo '<pre>'.print_r($db_user,1).'</pre>';
            if (empty($db_user)) {
                $app->abort(404, "User $id does not exist.");
            }
        }
        $access = array(
            'last_username' => $username,
            'active' => 'homepage',
        );

        return $app['twig']->render('home.html.twig', $app['app.access'] = array_merge($app['app.access'], $access));
    } // end-home function
    
    public static function about(SilexApplication $app)
    {
        $token = $app['security.token_storage']->getToken();
        $user = $token->getUser();
        $username = is_object($user) ? $user->getUsername() : $user;
        $access = array(
            'last_username' => $username,
            'header_content' => __DIR__.'/../../views/header_content.html',
            'footer_content' => __DIR__.'/../../views/footer_content.html',
            'active' => 'about',
        );

        return $app['twig']->render('about.html.twig', $app['app.access'] = array_merge($app['app.access'], $access));
    } // end-about function
}  // end-application
