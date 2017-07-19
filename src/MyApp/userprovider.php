<?php

namespace MyApp;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\DBAL\Connection;

class UserProvider implements UserProviderInterface
{
    private $conn;

    public function __construct(Connection $conn)
    {
        //error_log("IN " . __FUNCTION__);
        $this->conn = $conn;
    }

    public function loadUserByUsername($username)
    {
        //error_log("IN " . __FUNCTION__);
        //error_log("login user $username");
        //$user = array('role' => 1);
        //$password = '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a';
        $sql = 'SELECT * FROM user WHERE ' . (strpos($username, '@') ? 'email' : 'name') . ' = ?';
        //error_log("sql=$sql");
        $stmt = $this->conn->executeQuery($sql, array(strtolower($username)));
        $user = $stmt->fetch();
        
        //$user = User::getUser($username);
        if (empty($user)) {
            $app = SilexApplication::instance();
            $message = sprintf('Username "%s" does not exist.', $username);
            $app['session']->getFlashBag()->add('error', $message);
            //error_log("login user $username not exist");
            throw new UsernameNotFoundException($message);
        }
        
        //error_log("login user $username exist");
        //$user['password'] = $password;
        $roles = array(1=>'ROLE_ADMIN',2=>'ROLE_ADMIN',3=>'ROLE_USER');
        return new User($username, $user['password'], explode(',', $roles[$user['role']]), true, true, true, true);
        
        /*$dummyusers = array(
            1   => array('ROLE_ADMIN', $password,'admin'),
            array('ROLE_ADMIN', $password,'employer'),
            array('ROLE_USER', $password,'employee'),
        );
        $user = $dummyusers[$user['role']];
        if (empty($user)) {
            throw new UsernameNotFoundException(sprintf('Dummy username "%s" does not exist.', $username));
        }
        return new User($user[2], $user[1], explode(',', $user[0]), true, true, true, true);
        */
    }

    public function refreshUser(UserInterface $user)
    {
        //error_log("IN " . __FUNCTION__);
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        $username = $user->getUsername();
        //error_log("try to login user $username");
        return $this->loadUserByUsername($username);
    }

    public function supportsClass($class)
    {
        ///error_log("IN " . __FUNCTION__);
        //error_log("class =" . $class);
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
