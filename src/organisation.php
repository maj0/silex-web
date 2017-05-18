<?php

require_once __DIR__.'/../src/utils.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class Organisation
{
	public static function olist(Application $app) 
	{
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$sql = 'select * from user WHERE '. (strpos($username,'@')? 'email' : 'name') . ' = ?';
		$db_user = $app['db']->fetchAssoc($sql, array($username));
		//echo '<pre>'.print_r($db_user,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$access = array(
		'last_username' => $username,
		'user_add' => '/user/add',
		'hide_add_organisation' => '',
		);
		$access['hide_delete_organisation'] = '';
		$access['hide_edit_organisation'] = '';

		$output = '';
		$user = $db_user;
		if($user['role'] == User::$USER_AS_ADMIN)
		{
			$sql = 'select * from organisation;';
			$db_organisations = $app['db']->fetchAll($sql);
		} else {
			$sql = 'select * from organisation WHERE id = ?;';
			$db_organisations = $app['db']->fetchAll($sql, array((int)$user['organisation_ID']));
			$access['hide_add_organisation'] = 'hidden';
			if($user['role'] == User::$USER_AS_EMPLOYEE)
			{
				$access['hide_delete_organisation'] = 'hidden';
				$access['hide_edit_organisation'] = 'hidden';
			}
		}
		$access['organisations'] = $db_organisations;
		/*$sql = 'select * from organisation;';
		$db_organisations = $app['db']->fetchAll($sql);
		foreach ($db_organisations as $id => $o) {
			$output .= '<a href="/organisation/show/'.$o['id'].'">'.$o['name'].'</a>';
			$output .= '<br />';
		}*/
		$output .= "<br><a href='/organisation/add'>Add Organisation</a>&nbsp;";
		//$user = $app['security.token_storage']->getToken()->getUser()->getUsername();
		//var_dump($user);
		//return $output."<br /><a href='/users'>users</a>";
		return $app['twig']->render('list_organisation.html', $access/*array(
			'organisations' => $db_organisations,
			'list_organisation' => '/organisations',
			'last_username' => $app['security.token_storage']->getToken()->getUser()->getUsername(),
		)*/);

	}
	
	public static function show(Silex\Application $app, $id) {
		$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
		$db_user = User::getUser($username);
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		if ($id != $db_user['organisation_ID']) {
			$app->abort(404, "Organisation not allowed.");
		}
		
		$sql = 'select * from organisation WHERE id = ?;';
		$db_org = $app['db']->fetchAssoc($sql, array((int)$id));
		if (empty($db_org)) {
			$app->abort(404, "User $id does not exist.");
		}
		
		$o = &$db_org;
		$sql = 'select * from user WHERE organisation_ID = ?;';
		$db_employees = $app['db']->fetchAll($sql, array((int)$id));
		
		$access = array(
			'last_username' => $username,
			'hide_add_employee' => '',
			'hide_add_employer' => '',
			'hide_add_organisation' => 'hidden',
			'hide_delete_user' => '',
			'hide_edit_user' => '',
			'user_type' => 'Employee',
		);
		if($db_user['role'] == User::$USER_AS_EMPLOYEE)
		{
			$access['hide_add_employee'] = 'hidden';
			$access['hide_add_employer'] = 'hidden';
		}
		$output = '';
		foreach ($db_employees as $id => &$user) {
			$output .= ($id+1).')&nbsp;<a href="/user/'.$user['id'].'">'.$user['name'].'</a>&nbsp;Actions:&nbsp;';
			$output .= "<a href='/user/".$user['id']."/edit'>Edit</a>&nbsp;";
			$output .= "<a href='/user/".$user['id']."/delete'>Delete</a>".'<br/>';
			
			$user['hide_delete_user'] = '';
			$user['hide_edit_user'] = '';
			$user['type'] = 'employer';


			if($db_user['role'] == User::$USER_AS_EMPLOYEE)
			{
				$user['hide_delete_user'] = 'hidden';
				$user['hide_edit_user'] = 'hidden';
			}
			if($user['id'] == $db_user['id']) // only self edit
			{
				$user['hide_edit_user'] = '';
				$user['type'] = 'employee';
			}
			//if(empty($id)) echo "<pre>",print_r($user,1),"</pre><br>\n";
		}
		if($output) $output = "Employees:<br/>\n$output";

		/*return  "<h1>{$o['name']}</h1>".
				"<p>{$o['address']}</p><br>$output<br/>".
				"<a href='/organisation/employer/add/".$o['id']."'>Add Employer</a>&nbsp;".
				"<a href='/organisation/employee/add/".$o['id']."'>Add Employee</a>&nbsp;".
				"<br><a href='/organisations'>organisations</a>&nbsp;<a href='/users'>users</a>";*/
		$access['organisation_add'] = '/organisation/add';
		$access['users'] = $db_employees;
		$access['organisation'] = $db_org;
		$access['add_employer'] = "/organisation/employer/add/".$o['id'];
		$access['add_employee'] = "/organisation/employee/add/".$o['id'];
		return $app['twig']->render('show_organisation.html', $access);

	}
	
	/*
	 * This function show form for adding new organisation
	 */
	public static function add(Request $req, Application $app) {
		if($req->getMethod() == 'GET') {
			$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
			$db_user = User::getUser($username);
			if (empty($db_user)) {
				$app->abort(404, "User $username does not exist.");
			}
			$access = array(
				'last_username' => $username,
				'organisation_add' => '/organisation/add',
				'hide_add_user' => '',
				'hide_add_organisation' => 'hidden',
				'hide_delete_user' => '',
				'hide_edit_user' => '',
			);
			return $app['twig']->render('add_organisation.html', $access);
		} else {
			return self::do_add($req, $app);
		}
	}
	
	/*
	 * This function add new organisation in database
	 */
	public static function do_add(Request $req, Application $app) {
		$sql = "INSERT INTO organisation ";
		$val = array();
		
		if(!empty($_REQUEST['name'])) $val['name'] = $_REQUEST['name'];
		if(!empty($_REQUEST['address'])) $val['address'] = $_REQUEST['address'];
		if(!empty($_REQUEST['telephone'])) $val['telephone'] = $_REQUEST['telephone'];
		
		$sql .= "(" . join(',',array_keys($val)) . ") VALUES ('" . join("','",array_values($val)). "')";
		error_log("sql=$sql");
		$app['db']->executeUpdate($sql);
		return $app->redirect('/organisations');;
	}

	/*
	 * This function show form for adding new organisation
	 */
	public static function edit(Request $req, Application $app, $id) {
		if($req->getMethod() == 'GET') {
			$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
			$db_user = User::getUser($username);
			if (empty($db_user)) {
				$app->abort(404, "User $username does not exist.");
			}
			//$id = $req->get('id');
			$sql = 'select * from organisation WHERE id = ?;';
			$organisation = $app['db']->fetchAssoc($sql, array((int)$id));
			//echo '<pre>'.print_r($db_organisation,1).'</pre>';
			if (empty($organisation)) {
				$app->abort(404, "Organisation $id does not exist.");
			}
			$access = array_merge($organisation, array(
				'last_username' => $username,
				'user_add' => '/user/add',
				'hide_add_user' => '',
				'hide_add_organisation' => 'hidden',
				'hide_delete_user' => '',
				'hide_edit_user' => '',
				'http_referer' => $_SERVER['HTTP_REFERER'],
			));

			$access['organisation_update'] = '/organisation/edit/'.$id;
			return $app['twig']->render('edit_organisation.html', $access);
		} else {
			return self::update($req, $app, $id);
		}
	}
	
	/*
	 * This function update organisation in database
	 */
	public static function update(Request $req, Application $app, $id) 
	{
		//$id = $_REQUEST['id'];
		$sql = 'select * from organisation WHERE id = ?;';
		$db_organisation = $app['db']->fetchAssoc($sql, array((int)$id));
		//echo '<pre>'.print_r($db_organisation,1).'</pre>';
		if (empty($db_organisation)) {
			$app->abort(404, "Organisation $id does not exist.");
		}
		$organisation = $db_organisation;

		$sql = "Update organisation ";

		$val = array();
		
		if(Utility\updated('name',$_REQUEST, $organisation)) $val['name'] = $_REQUEST['name'];
		if(Utility\updated('address',$_REQUEST, $organisation)) $val['address'] = $_REQUEST['address'];
		if(Utility\updated('telephone',$_REQUEST, $organisation)) $val['telephone'] = $_REQUEST['telephone'];
		
		$set_value = '';
		foreach($val as $k => $v)
		{
			$set_value[] = "$k = '$v'";
		}
		if(!empty($set_value))
		{
			$sql .= "SET " . join(',', $set_value) . " WHERE id = ?";
			error_log("sql=$sql");
			$app['db']->executeUpdate($sql, array((int)$id));
		}
		return $app->redirect('/organisations');;
	}
	
	/*
	 * This function delete organisation from database
	 */
	public static function delete(Request $req, Application $app) 
	{
		$id = $req->get('id');
		$sql = 'select * from organisation WHERE id = ?;';
		$db_organisation = $app['db']->fetchAssoc($sql, array((int)$id));
		//echo '<pre>'.print_r($db_organisation,1).'</pre>';
		if (empty($db_organisation)) {
			$app->abort(404, "Organisation $id does not exist.");
		}
		$organisation = $db_organisation;

		$sql = "Delete from organisation WHERE id = ?;";
		error_log("sql=$sql");
		$app['db']->executeUpdate($sql, array((int)$id));
		return $app->redirect('/organisations');;
	}
	
	/*
	* This function show form for new employee
	*/
	public static function add_employee(Request $req, Application $app, $id) {
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
			
			if($db_user['role'] == User::$USER_AS_EMPLOYEE) {
				$roles = array(array('value'=>3,'name'=>'employee','selected' => 'selected'));
				$access['user_type'] = 'Employee';
			}
			elseif($db_user['role'] == User::$USER_AS_EMPLOYER)
			{
				$roles = array(
					array('value'=>2,'name'=>'employer','selected' => 'selected'),
					array('value'=>3,'name'=>'employee','selected' => ''),
				);
				$access['user_type'] = 'Employer';
			}
			else {
				$roles = array(
					array('value'=>1,'name'=>'admin','selected' => 'selected'),
					array('value'=>2,'name'=>'employer','selected' => ''),
					array('value'=>3,'name'=>'employee','selected' => ''),
				);
				$access['user_type'] = 'Admin';
				if(!empty($id))
				{
					$roles[0]['selected'] = '';
					if(preg_match('#employer#i', $_SERVER['REQUEST_URI']))
					{
						$roles[1]['selected'] = 'selected';
						$access['user_type'] = 'Employer';
					}
					else
					{
						$roles[2]['selected'] = 'selected';
						$access['user_type'] = 'Employee';
					}
					$access['user_add'] = "/organisation/employee/add/$id";
				}
			}

			$user = array(
				'last_organisation_ID' => $id,
				'readonly_organisation_ID' => 'readonly',
				'roles' => $roles,
				'http_referer' => $_SERVER['HTTP_REFERER'],
			);
			
			//echo "<pre>", print_r($_SERVER, 1), "</pre>";
			return $app['twig']->render('add_user.html', array_merge($access, $user));
		}
		return self::do_add_employee($req, $app, $id);
	}
	
	/*
	* This function add new employee in database
	*/
	public static function do_add_employee(Request $req, Application $app, $id) {
		$sql = "INSERT INTO user ";
		$val = array('probation' => '0');
		
		if(!empty($_REQUEST['name'])) $val['name'] = $_REQUEST['name'];
		if(!empty($_REQUEST['address'])) $val['address'] = $_REQUEST['address'];
		if(!empty($_REQUEST['email'])) $val['email'] = $_REQUEST['email'];
		if(!empty($_REQUEST['employee_ID'])) $val['employee_ID'] = $_REQUEST['employee_ID'];
		if(!empty($_REQUEST['organisation_ID'])) $val['organisation_ID'] = $_REQUEST['organisation_ID'];
		if(!empty($_REQUEST['role'])) $val['role'] = $_REQUEST['role'];
		if(!empty($_REQUEST['birthdate'])) $val['birthdate'] = $_REQUEST['birthdate'];
		if(!empty($_REQUEST['probation'])) $val['probation'] = empty($_REQUEST['probation']) ? '0' : '1';
		if(!empty($_REQUEST['password'])) $val['password'] = $_REQUEST['password'];
		if(!empty($_REQUEST['telphone'])) $val['telephone'] = $_REQUEST['telephone'];
		
		$sql .= "(" . join(',',array_keys($val)) . ") VALUES ('" . join("','",array_values($val)). "')";
		//echo "sql=$sql";
		$app['db']->executeUpdate($sql);
		return $app->redirect('/organisation/show/'.$_REQUEST['organisation_ID']);
	}
	
	public static function edit_employee(Request $req, Application $app, $id) {
		if($req->getMethod() == 'GET') {
			$username = $app['security.token_storage']->getToken()->getUser()->getUsername();
			$sql = 'select * from user WHERE '. (strpos($username,'@')? 'email' : 'name') . ' = ?';
			$db_user = $app['db']->fetchAssoc($sql, array($username));
			//echo '<pre>'.print_r($db_user,1).'</pre>';
			if (empty($db_user)) {
				$app->abort(404, "User $id does not exist.");
			}

			//echo "<pre>",print_r($_SERVER,1),"</pre>\n";
			if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = '/users';
			$access = array(
				'last_username' => $username,
				'user_add' => '/user/add',
				'hide_add_user' => '',
				'hide_add_organisation' => 'hidden',
				'hide_delete_user' => '',
				'hide_edit_user' => '',
				'user_type' => 'Employee',
				'http_referer' => $_SERVER['HTTP_REFERER'],
			);

			$roles = array(
				1=>array('value'=>1,'name'=>'admin'),
				   array('value'=>2,'name'=>'employer'),
				   array('value'=>3,'name'=>'employee'),
			);
			$sql = 'select * from user WHERE id = ?;';
			$edit_user = $app['db']->fetchAssoc($sql, array((int)$id));
			//echo '<pre>'.print_r($db_user,1).'</pre>';
			if (empty($edit_user)) {
				$app->abort(404, "User $id does not exist.");
			}
			if ($db_user['role'] != User::$USER_AS_ADMIN && $edit_user['organisation_ID'] != $db_user['organisation_ID']) {
				$app->abort(404, "User $id not allowed.");
			}
			$user = $edit_user;
			// by default enable all fields
			foreach($edit_user as $k => $v) $user["{$k}_disabled"] = "";
			// here disable fields which are needed
			if($db_user['role'] != 1)
			{
				foreach(explode(' ','role employee_ID organisation_ID probation') as $k)
				{
					//error_log("disable $k");
					$user["{$k}_disabled"] = "disabled";
				}
				
				if($db_user['role'] == 2)
				{
					$user["employee_ID_disabled"] = "";
					$user["probation_disabled"] = "";
				}
			}
			switch($db_user['role'])
			{
				case 1:break;
				case 2:unset($roles[1]);break;
				default:unset($roles[1],$roles[2]);break;
			}
			//echo "<pre>",print_r($_SERVER,1),"</pre>\n";
			$user["probation_checked"] = empty($user['probation'])?'':'checked';
			//$user['user_update'] = '/user/update';
			$user['user_edit'] = $_SERVER['REQUEST_URI'];
			// mark selected role from options
			foreach($roles as &$role)
			{
				$role['selected'] = $role['value'] == $user['role'] ? 'selected' : '';
			}
			$user['roles'] = $roles;
			//echo "<pre>",print_r($user,1),"</pre><br/>\n";
			return $app['twig']->render('edit_user.html', array_merge($access,$user));
		} else {
			return self::update_employee($req, $app, $id);
		}
	}
	
	public static function update_employee(Request $req, Application $app, $id) {
		$db_user = User::getUser($id);
		//echo '<pre>'.print_r($_REQUEST,1).'</pre>';
		if (empty($db_user)) {
			$app->abort(404, "User $id does not exist.");
		}
		$user = $db_user;

		$sql = "Update user ";

		$val = array('probation' => '0');
		
		if(Utility\updated('name',$_REQUEST, $user)) $val['name'] = $_REQUEST['name'];
		if(Utility\updated('address',$_REQUEST, $user)) $val['address'] = $_REQUEST['address'];
		if(Utility\updated('email',$_REQUEST, $user)) $val['email'] = $_REQUEST['email'];
		if(Utility\updated('employee_ID',$_REQUEST, $user)) $val['employeeID'] = $_REQUEST['employeeID'];
		if(Utility\updated('organisation_ID',$_REQUEST, $user)) $val['organisationID'] = $_REQUEST['organisationID'];
		if(Utility\updated('role',$_REQUEST, $user)) $val['role'] = $_REQUEST['role'];
		if(Utility\updated('birthdate',$_REQUEST, $user)) $val['birthdate'] = $_REQUEST['birthdate'];
		if(Utility\updated('probation',$_REQUEST, $user)) $val['probation'] = empty($_REQUEST['probation']) ? '0' : '1';
		if(Utility\updated('password',$_REQUEST, $user)) $val['password'] = $_REQUEST['password'];
		if(Utility\updated('telephone',$_REQUEST, $user)) $val['telephone'] = $_REQUEST['telephone'];
		
		$set_value = '';
		foreach($val as $k => $v)
		{
			$set_value[] = "$k = '$v'";
		}
		if(!empty($set_value))
		{
			$sql .= "SET " . join(',', $set_value) . " WHERE id = ?";
			//error_log("sql=$sql");
			$app['db']->executeUpdate($sql, array((int)$id));
		}
		//exit(0);
		if(empty($db_user['organisation_ID']))
		{
			return $app->redirect('/users');
		}
		return $app->redirect('/organisation/show/'.$db_user['organisation_ID']);

	}
}