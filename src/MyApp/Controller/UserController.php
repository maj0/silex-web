<?php

namespace MyApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use MyApp\Entity\User;
use MyApp\Form\Type\UserType;

define('ROLE_IS_ADMIN', 1);
define('ROLE_IS_EMPLOYER', 2);
define('ROLE_IS_EMPLOYEE', 3);

class UserController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get('/', 'MyApp\Controller\UserController::home')->bind('user_home');
        $factory->match('add', 'MyApp\Controller\UserController::doAdd');
        $factory->match('edit/{id}', 'MyApp\Controller\UserController::doEdit');
        $factory->match('delete/{id}', 'MyApp\Controller\UserController::doDelete');
        $factory->match('show/{id}', 'MyApp\Controller\UserController::doShow');
        return $factory;
    }

    public static function getUser($id, $app)
    {
        if (preg_match('#^\d+$#', $id)) {
            $sql = 'select * from user WHERE id = ?;';
            //error_log("sql=$sql");
            $user = $app['db']->fetchAssoc($sql, array((int)$id));
        } else {
            $sql = 'select * from user WHERE '. (strpos($id, '@')? 'email' : 'name') . ' = ?';
            //error_log("sql=$sql");
            $user = $app['db']->fetchAssoc($sql, array($id));
        }
        return $user;
    }

    /*
    * This function expose variables to framework
    * @param $req Request 
    * @param $app Application
    * @param $id  User id
    * @param $oid Organisation id
    * @return array Variables
    */
    protected static function access($req, $app, $id = 0, $oid = 0)
    {
        error_log("id=$id, oid=$oid");
        $username = $app['security.token_storage']->getToken()->getUser()->getUsername();
        $db_user = self::getUser($username, $app);
        //echo '<pre>'.print_r($db_user,1).'</pre>';
        if (empty($db_user)) {
            $app->abort(404, "User $id does not exist.");
        }

        $access = array(
            'error'         => $app['security.last_error']($req),
            'last_username' => $app['session']->get('_security.last_username'),
            'user_type' => 'User','user_add' => '/user/add','hide_add_user' => '',
            'organisationID_readonly' => '',
            'last_organisationID' => '',
            'active' => 'user',
        );
        
        $roles = array(
        1 => array('name' => 'admin', 'value' => 1, 'selected' => ''),
             array('name' => 'employer', 'value' => 2, 'selected' => ''),
             array('name' => 'employee', 'value' => 3, 'selected' => ''),
        );
        $offset = 0;
        $limit = 20;

        $access['logged'] = $db_user;
        $access['uribase'] = "/user";
        $access['type_user'] = 'User Details';

        $where = array('is_deleted != 1');
        if (!empty($db_user['organisationID'])) {
            $uoid = $db_user['organisationID'];
            $where[] = "organisationID=$uoid";
            $access['hide_add_user'] = $db_user['role'] == 3 ? 'hidden' : '';
            $access['last_organisationID'] = $db_user['organisationID'];
            $access['organisationID_readonly'] = 'readonly';
            $access['uribase'] = "/organisation/$uoid/employee";
            $access['user_type'] = 'Employee';
            unset($roles[1]);
        } elseif ($oid) {
            $where[] = "organisationID=$oid";
            $access['uribase'] = "/organisation/$oid/employee";
            $access['user_type'] = 'Employee';
            $access['organisationID_readonly'] = 'readonly';
            unset($roles[1]);
        }

        $users = $app['repository.user']->findAll($limit, $offset, $where);
        //echo "<pre>", print_r($users,1), "</pre><br\>\n";
        $access['users'] = $users;
        if (isset($users[$id])) {
            $user = $access['users'][$id];
            $access['user'] = $user;

            foreach ($user as $k => $v) {
                $access['user']["{$k}_disabled"] = '';
            }
            foreach ($roles as &$role) {
                $rv = $user->getRole();
                if ($role['value'] == $rv) {
                    $role['selected'] = 'selected';
                }
            }
        } elseif ($id) {
            $app->abort(404, "User $id does not exist.");
        }
        if (!empty($db_user['organisationID'])) {
            foreach ($users as $user) {
                $uid = $user->getId();
                $access['users'][$uid]['employeeID_readonly'] = 'readonly';
                $access['users'][$uid]['organisationID_readonly'] = 'readonly';
                if ($db_user['role'] == 3) {
                    unset($roles[2]);
                    $access['users'][$uid]['hide_edit_user'] = ($db_user['id'] === $uid) ? '' : 'hidden';
                    $access['users'][$uid]['hide_delete_user'] = 'hidden';
                    $access['users'][$uid]['role_readonly'] = 'readonly';
                }
            }
            $access['user_type'] = 'Employee';
            unset($roles[1]);
        }
        $access['roles'] = $roles;
        if (empty($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = '/user';
        }
        $access['_SERVER'] = $_SERVER;
        return $access;
    }

    /*
    * render home page of organisation
    * @param $req Request 
    * @param $app Application
    * @return file contents
    */
    public function home(Request $req, Application $app, $id = 0, $oid = 0)
    {
        $access = self::access($req, $app, $id, $oid);
        return $app['twig']->render('user.home.html.twig', $app['app.access'] = $access);
    }

    /*
    * render add page of user
    * @param $req Request 
    * @param $app Application
    * @return file contents
    */
    public function doAdd(Request $req, Application $app, $id = 0, $oid = 0)
    {
        if (empty($oid)) {
            $oid = $req->attributes->get('oid');
        }
        $user = new User();
        $form = $app['form.factory']->create(UserType::class, $user);
        if ($req->isMethod('POST')) {
            $form->bind($req);
            if ($form->isValid()) {
                $app['repository.user']->save($user);
                $message = 'The user ' . $user->getName() . ' has been saved.';
                $app['session']->getFlashBag()->add('success', $message);
                //return "Updated data successfully!";
                $access = self::access($req, $app, $user->getId(), $oid);
                $access['type_oper'] = 'Add';
                $access['form'] = $form->createView();
                return $app['twig']->render('edit_user.html.twig', $app['app.access'] = $access);
            } else {
                $message = 'The user ' . $user->getName() . ' has not been saved.';
                $app['session']->getFlashBag()->add('warning', $message);
            }
        }
        $access = self::access($req, $app, $id, $oid);
        if ($access['logged']['role'] == ROLE_IS_EMPLOYEE) {
            $app->abort(404, "User not allowed.");
        }
        $access['type_oper'] = 'Add';
        $user = array('probation_checked' => '');
        $text = 'name email address password role employeeID organisationID birthdate probation telephone';
        foreach (explode(' ', $text) as $key) {
            $user[$key] = '';
        }
        $access['user'] = $user;
        foreach ($user as $k => $v) {
            $access['user']["{$k}_disabled"] = '';
        }
        if ($oid) {
            $access['user']['organisationID'] = $oid;
            $access['user']['organisationID_disabled'] = 'disabled';
        } elseif ($access['logged']['role'] == ROLE_IS_EMPLOYER) {
            $access['user']['organisationID'] = $access['logged']['organisationID'];
        }
        return $app['twig']->render('add_user.html.twig', $app['app.access'] = $access);
        return __FUNCTION__ . ' new user';
    }

    /*
    * render edit page of user
    * @param $req Request 
    * @param $app Application
    * @param $id user
    * @param $oid organisation id of user
    * @return file contents
    */
    public function doEdit(Request $req, Application $app, $id, $oid = 0)
    {
        $user = $app['repository.user']->find($id);
        $form = $app['form.factory']->create(UserType::class, $user);
        if ($req->isMethod('POST')) {
        //echo "request:<pre>",print_r($_REQUEST,1),"</pre><br/>\n";
            $form->bind($req);
            if ($form->isValid()) {
                $app['repository.user']->save($user);
                $message = 'The user ' . $user->getName() . ' has been saved.';
                $app['session']->getFlashBag()->add('success', $message);
            } else {
                $message = 'The user ' . $user->getName() . ' has not been saved.';
                $app['session']->getFlashBag()->add('warning', $message);
            }
        }
        $access = self::access($req, $app, $id, $oid);
        $access['type_oper'] = 'Edit';
        if ($access['logged']['organisationID']) {
            if ($access['logged']['role'] == ROLE_IS_EMPLOYEE && $access['logged']['id'] != $id) {
                $app->abort(404, "User not allowed");
            }
        }
        $access['form'] = $form->createView();
        return $app['twig']->render('edit_user.html.twig', $app['app.access'] = $access);
        return __FUNCTION__ . " user $id";
    }

    /*
    * render delete page of user
    * @param $req Request 
    * @param $app Application
    * @param $id user
    * @param $oid organisation id of user
    * @return file contents
    */
    public function doDelete(Request $req, Application $app, $id, $oid = 0)
    {
        if ($req->isMethod('GET')) {
            $access = self::access($req, $app, $id, $oid);
            if ($access['logged']['role'] == ROLE_IS_EMPLOYEE) {
                $app->abort(404, "User not allowed");
            }
            $access['type_oper'] = 'Delete';
            $access['type_user'] = 'User Details';
            if ($oid) {
                $access['user']['organisationID'] = $oid;
            }
            $access['user_delete'] = "/user/delete/$id";
            return $app['twig']->render('delete_user.html.twig', $app['app.access'] = $access);
            return __FUNCTION__ . " user $id";
        } else {
            $user = new User();
            $user->setId($id);
            $app['repository.user']->delete($user);
            return "Deleted data successfully!";
        }
    }

    /*
    * render show page of user
    * @param $req Request 
    * @param $app Application
    * @param $id user
    * @param $oid organisation id of user
    * @return file contents
    */
    public function doShow(Request $req, Application $app, $id, $oid = 0)
    {
        if ($req->isMethod('GET')) {
            //echo "<pre>",print_r($req,1),"</pre>";
            $access = self::access($req, $app, $id, $oid);
            $access['type_oper'] = 'Show';
            $access['type_user'] = 'User Details';
            if ($oid) {
                $access['user']['organisationID'] = $oid;
            }
            return $app['twig']->render('show_user.html.twig', $app['app.access'] = $access);
        }
    }
}
