<?php

namespace MyApp\Repository;

use Doctrine\DBAL\Connection;
use MyApp\Entity\User;

/**
 * User repository
 */
class UserRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Saves the user to the database.
     *
     * @param \MyApp\Entity\User $user
     */
    public function save($user)
    {
        $userData = array(
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'password' => $user->getPassword(),
            'probation' => $user->getProbation() ? 1 : 0,
        );
        foreach (explode(' ', 'address employee_ID organisation_ID birthdate') as $k) {
            //if(!empty($_REQUEST[$k]))
            $val = call_user_func(array($user,'get'.ucfirst($k)));
            if (!empty($val)) {
                //$userData[$k] = $_REQUEST[$k];
                $userData[$k] = $val;
            }
        }
        //echo "<pre>",print_r($userData,1),"</pre><br/>\n";
        if ($user->getId()) {
            $this->db->update('user', $userData, array('id' => $user->getId()));
        } else {
            $this->db->insert('user', $userData);
            // Get the id of the newly created user and set it on the entity.
            $id = $this->db->lastInsertId();
            $user->setId($id);
        }
    }

    /**
     * Deletes the user.
     *
     * @param \MyApp\Entity\User $user
     */
    public function delete($user)
    {
        // mark user for deletion
        $this->db->update('user', array('is_deleted' => 1), array('id' => $user->getId()));
        //return $this->db->delete('user', array('id' => $user->getId()));
    }

    /**
     * Returns the total number of user.
     *
     * @return integer The total number of user.
     */
    public function getCount()
    {
        return $this->db->fetchColumn('SELECT COUNT(id) FROM user');
    }

    /**
     * Returns an user matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MyApp\Entity\User|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $userData = $this->db->fetchAssoc('SELECT * FROM user WHERE id = ?', array($id));
        return $userData ? $this->buildUser($userData) : false;
    }

    /**
     * Returns a collection of user, sorted by name.
     *
     * @param integer $limit
     *   The number of user to return.
     * @param integer $offset
     *   The number of user to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of user, keyed by user id.
     */
    public function findAll($limit, $offset = 0, $whereCond = array(), $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('name' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        //$whereCond[] = 'is_deleted != 1';
        if ($whereCond) {
            $where = join(' AND ', $whereCond);
            $queryBuilder
                ->select('o.*')
                ->from('user', 'o')
                ->where($where)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('o.' . key($orderBy), current($orderBy));
        } else {
            $queryBuilder
                ->select('o.*')
                ->from('user', 'o')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('o.' . key($orderBy), current($orderBy));
        }
        $statement = $queryBuilder->execute();
        $userData = $statement->fetchAll();

        $user = array();
        foreach ($userData as $userData) {
            $userId = $userData['id'];
            $user[$userId] = $this->buildUser($userData);
        }
        return $user;
    }

    /**
     * Instantiates an user entity and sets its properties using db data.
     *
     * @param array $userData
     *   The array of db data.
     *
     * @return \MyApp\Entity\User
     */
    protected function buildUser($userData)
    {
        $user = new User();
        $user->setId($userData['id']);
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $user->setAddress($userData['address']);
        $user->setTelephone($userData['telephone']);
        $user->setEmployeeID($userData['employee_ID']);
        $user->setRole($userData['role']);
        $user->setOrganisationID($userData['organisation_ID']);
        $user->setBirthdate($userData['birthdate']);
        $user->setProbation($userData['probation']);
        $user->setPassword($userData['password']);

        $user['hide_edit_user'] = $user['hide_delete_user'] = '';
        $text = 'name email address password role employee_ID organisation_ID birthdate probation telephone';
        foreach (explode(' ', $text) as $key) {
            $user["{$key}_readonly"] = '';
        }
        $user['probation_checked'] = empty($userData['probation']) ? '' : 'checked';
        return $user;
    }
}
