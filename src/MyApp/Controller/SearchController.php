<?php

namespace MyApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use MyApp\Entity\Search;
use MyApp\Form\Type\SearchType;
use Symfony\Component\Form\FormError;
use MyApp\Repository\SearchRepository;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as Type;

class SearchController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->match('/', 'MyApp\Controller\SearchController::home');
        return $factory;
    }

    protected static function access($req, $app, $id = 0, $oid = 0)
    {
        $username = $app['security.token_storage']->getToken()->getUser();


        $access = array(
            'error'         => $app['security.last_error']($req),
            'last_username' => $app['session']->get('_security.last_username'),
            'user_type' => 'User', 'user_add' => '/user/add', 'hide_add_user' => '',
            'organisationID_readonly' => '',
            'last_organisationID' => '',
            'active' => 'search',
        );
        
        if (empty($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = '/user';
        }
        $access['_SERVER'] = $_SERVER;
        return $access;
    }

    public function home(Request $req, Application $app)
    {
        $username = $app['security.token_storage']->getToken()->getUser()->getUsername();
        $db_user = UserController::getUser($username, $app);
        //echo '<pre>'.print_r($_REQUEST,1).'</pre>';
        if (empty($db_user)) {
            $app->abort(404, "User $id does not exist.");
        }
        if ($db_user['role'] != ROLE_IS_ADMIN) {
            $app->abort(404, "User not allowed!");
        }
        $search = new Search();
        //$form = $app['form.factory']->create(SearchType::class, $search);
        $form = $app['form.factory']->createBuilder(Type\FormType::class, $search)
               ->add('name', Type\TextType::class)
               ->getForm() ;
        $access = self::access($req, $app);
        $access['search'] = $search;
        if ($req->isMethod('POST')) {
            $form->bind($req);
            if ($form->isValid()) {
                $myform = $req->get('search');
                $name = $_REQUEST['search']['name'];
                //$where = array("name like '%" . $search->getName() . "%'");
                $where = array("name like '%" . $name . "%'");
                $results  = $app['repository.search']->findAll(10, 0, $where);
                $access['results'] = $results;
                $message = "The search {$myform['name']} has found " . count($results);
                $app['session']->getFlashBag()->add('success', $message);
                return $app['twig']->render('results.html.twig', $app['app.access'] = $access);
            } else {
                $message = 'The search ' . $search->getName() . ' is not valid.';
                $app['session']->getFlashBag()->add('error', $message);            //return "Saved data successfully!";
            }
        }
        $access['form'] = $form->createView();
        return $app['twig']->render('search.html.twig', $app['app.access'] = $access);
        return 'Welcome to my search page!';
    }
}
