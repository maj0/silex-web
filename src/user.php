<?php

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class User
{
	public static $USER_AS_ADMIN = 1;
	public static $USER_AS_EMPLOYER = 2;
	public static $USER_AS_EMPLOYEE = 3;
	
	public static function list(Application $app) {
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$db_user = self::getUser($username);
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$access = array(
			'last_username' => $username,
			'add_user' => '/user/add',
			'hide_add_user' => '',
			'hide_add_organisation' => 'hidden',
			'hide_delete_user' => '',
			'hide_edit_user' => '',
			'header_content' => __DIR__.'/../views/header_content.html',
			'footer_content' => __DIR__.'/../views/footer_content.html',
			);

		$output = '';
		$user = $db_user;
		if($user['role'] == User::$USER_AS_ADMIN)
		{
			$sql = 'select * from user;';
			$db_users = $app['db']->fetchAll($sql);
			$access['hide_add_organisation'] = '';
			$access['add_user'] = '/user/add';
		} else {
			$sql = 'select * from user WHERE organisation_ID = ?;';
			$db_users = $app['db']->fetchAll($sql, array((int)$user['organisation_ID']));
			if($user['role'] == User::$USER_AS_EMPLOYEE)
			{
				$access['hide_add_user'] = 'hidden';
				$access['hide_delete_user'] = 'hidden';
				$access['hide_edit_user'] = 'hidden';
			}
			else // as Employer
			{
				$access['add_user'] = '/organisation/employee/add/'.$user['organisation_ID'];
			}
		}

		//$output .= '<pre>'.print_r($db_users,1).'</pre>';
		// manage visibilty of actions for each user here
		foreach ($db_users as &$db_user) {
			//$output .= '<a href="/user/show/'.$db_user['id'].'">'.$db_user['name'].'</a>';
			//$output .= '<br/>';
			if($db_user['id'] == $user['id'])
			{
				$db_user['hide_edit_user'] = '';
			}
			else
			{
				$db_user['hide_edit_user'] = $access['hide_edit_user'];
			}
		}
		$access['users'] = &$db_users;
		return $app['twig']->render('list_user.html', $access);
	}
	
	/*
	* this function shows detail of user
	*/
	public static function show(Application $app, $id) {
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$db_user = self::getUser($username);
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}

		$access = $db_user;
		$access['last_username'] = $username;

		//return  "<h1>{$user['name']}</h1>".
		//		"<p>{$user['email']}</p>".
		//		"<br><a href='/organisations'>organisations</a>&nbsp;<a href='/users'>users</a>";
		//$access['user'] = &$db_user;

		foreach($db_user as $k => $v)
		{
			$access["{$k}_disabled"] = "disabled";
		}
		$access['type_user'] = 'User';
		return $app['twig']->render('show_user.html', $access);
	}
	
	/*
	* This function show form for new employee
	*/
	public static function add_user(Request $req, Application $app, $id = '') {
	  try {
		if($req->getMethod() == 'GET') {			
			$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
			$db_user = User::getUser($username);
			if (empty($db_user)) {
				$app->abort(404, "User $username does not exist.");
			}

			$access = array(
				'last_username' => $username,
				'user_add' => '/user/add',
				'hide_add_user' => '',
				'hide_add_organisation' => 'hidden',
				'hide_delete_user' => '',
				'hide_edit_user' => '',
			);
			
			if($db_user['role'] == User::$USER_AS_EMPLOYER)
			{
				$roles = array(
					array('value'=>2,'name'=>'employer','selected' => 'selected'),
					array('value'=>3,'name'=>'employee','selected' => ''),
				);
				$access['user_type'] = 'Employer';
			}
			elseif($db_user['role'] == User::$USER_AS_EMPLOYEE) {
				$roles = array(array('value'=>3,'name'=>'employee','selected' => 'selected'));
				$access['user_type'] = 'Employee';
			}
			else {
				$roles = array(
					array('value'=>1,'name'=>'admin','selected' => 'selected'),
					array('value'=>2,'name'=>'employer','selected' => ''),
					array('value'=>3,'name'=>'employee','selected' => ''),
				);
				$access['user_type'] = 'Admin';
	
				if(preg_match('#employer#i', $_SERVER['REQUEST_URI']))
				{
					$roles[0]['selected'] = '';
					$roles[1]['selected'] = 'selected';
					$access['user_type'] = 'Employer';
				}
				elseif(preg_match('#employer#i', $_SERVER['REQUEST_URI']))
				{
					$roles[0]['selected'] = '';
					$roles[2]['selected'] = 'selected';
					$access['user_type'] = 'Employee';
				}
			}			

			$user = array(
				'employee_role' => User::$USER_AS_EMPLOYEE,
				'last_organisation_ID' => $id,
				'readonly_organisation_ID' => 'readonly',
				'roles' => $roles,
				'http_referer' => $_SERVER['HTTP_REFERER'],
			);
			return $app['twig']->render('add_user.html', array_merge($access, $user));
		}
		return self::do_add_user($req, $app, $id);
	  } catch (Exception $e) {
			echo "Error:" . $e->getMessage();
	  }
	}
	
	
	/*
	* This function add new employee in database
	*/
	public static function do_add_user(Request $req, Application $app, $id = 0) {
		$errors = User::validate($req, $app);
		if(empty($errors))
		{
			$sql = "INSERT INTO user ";
			$val = array('probation' => '0');
			
			if(!empty($_REQUEST['name'])) $val['name'] = $_REQUEST['name'];
			if(!empty($_REQUEST['address'])) $val['address'] = $_REQUEST['address'];
			if(!empty($_REQUEST['email'])) $val['email'] = $_REQUEST['email'];
			if(!empty($_REQUEST['employee_ID'])) $val['employee_ID'] = $_REQUEST['employee_ID'];
			if(!empty($_REQUEST['organisation_ID'])) $val['organisation_ID'] = $_REQUEST['organisation_ID'];
			if(!empty($_REQUEST['role'])) $val['role'] = $_REQUEST['role'];
			if(!empty($_REQUEST['birthdate'])) $val['birthdate'] = $_REQUEST['birthdate'];
			if(!empty($_REQUEST['probation'])) $val['probation'] = $_REQUEST['probation'] == 'true' ? '1' : '0';
			if(!empty($_REQUEST['password'])) $val['password'] = $_REQUEST['password'];
			if(!empty($_REQUEST['telphone'])) $val['telephone'] = $_REQUEST['telephone'];
			
			$sql .= "(" . join(',',array_keys($val)) . ") VALUES ('" . join("','",array_values($val)). "')";
			//echo "sql=$sql";
			$app['db']->executeUpdate($sql);
			if(empty($_REQUEST['organisation_ID']))
			{
				return $app->redirect('/users');
			}
			return $app->redirect('/organisation/show/'.$_REQUEST['organisation_ID']);
		}
		var_dump($errors);
	}
	
	/*
	 * This function delete user from database
	 */
	public static function delete(Request $req, Application $app, $id) 
	{
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		$db_user = self::getUser($id);
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$user = $db_user;

		$sql = "Delete from user WHERE id = ?;";
		error_log("sql=$sql");
		$app['db']->executeUpdate($sql, array((int)$id));
		return $app->redirect('/users');;
	}
		
	/*
	 * This function validate user data before writing in database
	 */
	public static function validate(Request $req, Application $app) 
	{	$errors = array();
		$constraint = new Assert\Collection(array(
			'email' => new Assert\Email(),
			'name'  => new Assert\NotBlank(),
			'password'  => array(new Assert\NotBlank(), new Assert\Length(array('min'=>6))),
			'birthdate' => new Assert\Date(),
		));

		//var_dump($app->get('validator'));return 1;
		/*$violationList = $app->get('validator')->validate($req->request->all(), $constraint);

		foreach ($violationList as $violation){
			$field = preg_replace('/\[|\]/', "", $violation->getPropertyPath());
			$error = $violation->getMessage();
			$errors[$field] = $error;
		}*/		
		return $errors;
  }

  public static function getUser($id)
  {
	  $app = Silex_Application::instance();
	  if(preg_match('#^\d+$#',$id))
	  {
	  	$sql = 'select * from user WHERE id = ?;';
		//error_log("sql=$sql");
		$user = $app['db']->fetchAssoc($sql, array((int)$id));
	  }
	  else 
	  {
		$sql = 'select * from user WHERE '. (strpos($id,'@')? 'email' : 'name') . ' = ?';
		//error_log("sql=$sql");
		$user = $app['db']->fetchAssoc($sql, array($id));
	  }
	  return $user;

  }
}