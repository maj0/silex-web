<?php

namespace MyApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use MyApp\Entity\Organisation;
use MyApp\Form\Type\OrganisationType;
use Symfony\Component\Form\FormError;
use MyApp\Repository\OrganisationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrganisationController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get('/', 'MyApp\Controller\OrganisationController::home')->bind('organisation');
        $factory->match('add', 'MyApp\Controller\OrganisationController::doAdd');
        $factory->match('edit/{organisation}', 'MyApp\Controller\OrganisationController::doEdit')
                ->bind('organisation_edit')
                ->convert('organisation', function ($id) use ($app) {
                    //error_log("converter for organisation for id=$id called!");
                    if ($id) {
                        return $app['repository.organisation']->find($id);
                    }
                });
        $factory->match('delete/{id}', 'MyApp\Controller\OrganisationController::doDelete');
        $factory->match('show/{id}', 'MyApp\Controller\OrganisationController::doShow');
        return $factory;
    }

    /*
    * This function expose variables to framework
    * @param $req Request 
    * @param $app Application
    * @param $oid Organisation id
    * @return array Variables
    */
    protected static function access($req, $app, $oid = 0)
    {
        $username = $app['security.token_storage']->getToken()->getUser()->getUsername();
        $db_user = UserController::getUser($username, $app);
        //echo '<pre>'.print_r($db_user,1).'</pre>';
        if (empty($db_user)) {
            $app->abort(404, "User $id does not exist.");
        }

        $access = array(
            'error'         => $app['security.last_error']($req),
            'last_username' => $username,
            'hide_edit_organisation' => '', 'hide_delete_organisation' => '','hide_add_organisation' => '',
            'user_type' => 'User',
            'active' => 'organisation',
        );
        $db_user['hide_add_user'] = '';
        $db_user['add_user'] = '/user/add';
        $access['logged'] = $db_user;
        $offset = 0;
        $limit = 20;

        $access['users'] = array(); // empty users for add organisation
        if ($oid) {
            if (($db_user['role'] != ROLE_IS_ADMIN && $db_user['organisationID'] != $oid)) {
                $app->abort(404, "Organisation $oid not allowed.");
            }
            $organisation = $app['repository.organisation']->find($oid);
            $access['organisation'] = $organisation;
            $where = array("organisationID = $oid");
            $where[] = "is_deleted != 1"; // get all record which are not marked deleted
            $users = $app['repository.user']->findAll($limit, $offset, $where);
            //$users = $organisations;
            $access['users'] = $users;
            foreach ($users as $user) {
                $uid = $user->getId();

                if ($db_user['role'] == ROLE_IS_EMPLOYEE) {
                    $access['users'][$uid]['hide_edit_user'] = ($db_user['id'] === $uid) ? '' : 'hidden';
                    $access['users'][$uid]['hide_delete_user'] = 'hidden';
                }
            }
            $access['uribase'] = "/organisation/$oid/employee";
        } else {
            $where = array('is_deleted != 1');// get all record which are not marked deleted
            if (!empty($db_user['organisationID'])) {
                $where[] = "id={$db_user['organisationID']}";
                $access['hide_add_organisation'] = 'hidden';
                $access['hide_delete_organisation'] = 'hidden';
                $access['user_type'] = 'Empoloyee';
            }
            $organisations = $app['repository.organisation']->findAll($limit, $offset, $where);
            $access['organisations'] = $organisations;
        }
        if ($db_user['role'] == ROLE_IS_EMPLOYEE) {
            $access['hide_add_user'] = $access['logged']['hide_add_user'] = 'hidden';
            $access['hide_edit_organisation'] = $access['hide_delete_organisation'] = 'hidden';
        }
        //echo "<pre>",print_r($access, 1), "</pre><br/>\n";
        //echo "<pre>",print_r($app, 1), "</pre><br/>\n";
        if (empty($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = '/organisation';
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
    public function home(Request $req, Application $app)
    {
        return $app['twig']->render('organisation.home.html.twig', $app['app.access'] = self::access($req, $app));
    }


    /*
    * render add page of organisation
    * @param $req Request 
    * @param $app Application
    * @return file contents
    */
    public function doAdd(Request $req, Application $app)
    {
        $access = self::access($req, $app);
        if ($access['logged']['role'] != ROLE_IS_ADMIN) {
            $app->abort(404, "User not allowed.");
        }
        $organisation = new Organisation();
        $form = $app['form.factory']->create(OrganisationType::class, $organisation);

        if ($req->isMethod('GET')) {
            $access['type_oper'] = 'Add';
            $access['type_organisation'] = 'Organisation Details';
            $organisation = array();
            foreach (explode(' ', 'name address telephone') as $key) {
                $organisation[$key] = '';
            }
            //file_put_contents("myform.txt",$access['form']);
            //echo "<pre>", print_r($form,1),"</pre>\n";
        } else {
            $form->bind($req);
            if ($form->isValid()) {
                $where = "name = '" . $organisation->getName() . "'";
                $results = $app['repository.organisation']->findAll(1, 0, array($where));
                if (empty($results)) {
                   //exit(0);
                    $app['repository.organisation']->save($organisation);
                    $message = 'The organisation ' . $organisation->getName() . ' has been saved.';
                    $app['session']->getFlashBag()->add('success', $message);
                    $id = $organisation->getId();
                    $access = self::access($req, $app, $id);
                    $access['type_oper'] = 'Edit';
                    $access['type_organisation'] = 'Organisation Details';
                    $access['organisation_edit'] = '/organisation/edit/'.$id;
                    $access['title'] = "Edit Organisation";
                    $access['form'] = $form->createView();
                    return $app['twig']->render('edit_organisation.html.twig', $app['app.access'] = $access);
                } else {
                    $message = 'The organisation ' . $organisation->getName() . ' alread exist.';
                    $app['session']->getFlashBag()->add('error', $message);
                }
            } else {
                $message = 'The organisation ' . $organisation->getName() . ' has not been saved.';
                $app['session']->getFlashBag()->add('warrning', $message);
            }
        }
        $access['organisation'] = $organisation;
        $access['organisation_add'] = '/organisation/add';
        $access['form'] = $form->createView();
        return $app['twig']->render('add_organisation.html.twig', $app['app.access'] = $access);
    }

    /*
    * render edit page of organisation
    * @param $req Request 
    * @param $app Application
    * @return file contents
    */
    public function doEdit(Request $req, Application $app)
    {
        //echo "<pre>",print_r($req,1),"</pre><br/>\n";
        $organisation = $req->attributes->get('organisation');
        $form = $app['form.factory']->create(OrganisationType::class, $organisation);
        if ($req->isMethod('POST')) {
            //echo "<pre>",print_r($org, 1),"</pre><br>\n";
            $form->bind($req);         // transfer data to form object from request
            if ($form->isValid()) {   // validate data with our defined constraints
                $where = "name = '" . $organisation->getName() . "'";
                $results = $app['repository.organisation']->findAll(1, 0, array($where));
                if (empty($results)) {
					$app['repository.organisation']->save($organisation);
					$message = 'The organisation ' . $organisation->getName() . ' has been saved.';
					$app['session']->getFlashBag()->add('success', $message);
				} else {
					$message = 'The organisation ' . $organisation->getName() . ' is not unique.';
					$app['session']->getFlashBag()->add('error', $message);
				}
            }
        }
        $id = $organisation->getId();
        $access = self::access($req, $app, $id);
        if ($access['logged']['role'] == ROLE_IS_EMPLOYEE) {
            $app->abort(404, "User not allowed.");
        }
        $access['type_oper'] = 'Edit';
        $access['type_organisation'] = 'Organisation Details';
        $access['organisation_edit'] = '/organisation/edit/'.$id;
        $access['title'] = "Edit Organisation";
        $access['form'] = $form->createView();
        return $app['twig']->render('edit_organisation.html.twig', $app['app.access'] = $access);
    }
    /*
    * render delete page of organisation
    * @param $req Request 
    * @param $app Application
    * @param $id  Organisation id to delete
    * @return file contents
    */
    public function doDelete(Request $req, Application $app, $id = 0)
    {
        $access = self::access($req, $app, $id);
        if ($req->isMethod('GET')) {
            if ($access['logged']['role'] != ROLE_IS_ADMIN) {
                $app->abort(404, "User not allowed.");
            }
        } else {
            $organisation = new Organisation();
            $organisation->setId($id);
            $result = $app['repository.organisation']->find($id);
            //var_dump($result);exit(0);
            if (empty($result)) {
                $message = 'The organisation ' . $organisation->getName() . ' does not exist.';
                $app['session']->getFlashBag()->add('error', $message);
            } elseif (empty($result['is_deleted'])) {
                $app['repository.organisation']->delete($organisation); // mark data for deletion
                //return "Deleted data successfully!";
                $message = 'The organisation ' . $organisation->getName() . ' has been deleted.';
                $app['session']->getFlashBag()->add('success', $message);
            } else {
                $message = 'The organisation ' . $organisation->getName() . ' already deleted.';
                $app['session']->getFlashBag()->add('error', $message);
            }

        }
        $access['type_oper'] = 'Delete';
        $access['type_organisation'] = 'Organisation Details';
        $access['organisationID'] = $id;
        $access['organisation'] = $app['repository.organisation']->find($id);

        $access['organisation_delete'] = '/organisation/delete/'.$id;
        $access['title'] = "Delete Organisation";
        if (empty($access['organisation'])) {
            $message = "The organisation $id does not exist.";
            $app['session']->getFlashBag()->add('error', $message);
        }
        return $app['twig']->render('delete_organisation.html.twig', $app['app.access'] = $access);
    }
    /*
    * render show page of organisation
    * @param $req Request 
    * @param $app Application
    * @param $id  Organisation id to delete
    * @return file contents
    */
    public function doShow(Request $req, Application $app, $id)
    {
        $access = self::access($req, $app, $id);
        $access['type_oper'] = 'Show';
        $access['type_organisation'] = 'Organisation Details';
        $access['organisationID'] = $id;

        //echo "<pre>",print_r($_SERVER, 1),"</pre><br>\n";
        return $app['twig']->render('organisation.html.twig', $app['app.access'] = $access);
    }
}
