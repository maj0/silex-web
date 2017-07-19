<?php

namespace MyApp\Repository;

use Doctrine\DBAL\Connection;
use MyApp\Entity\Search;

/**
 * Search repository
 */
class SearchRepository /*implements RepositoryInterface*/
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
     * Returns a collection of search, sorted by name.
     *
     * @param integer $limit
     *   The number of search to return.
     * @param integer $offset
     *   The number of search to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of search, keyed by search id.
     */
    public function findAll($limit, $offset = 0, $whereCond = array(), $orderBy = array())
    {
        $organisations = $this->findAllOrganisation($limit, $offset, $whereCond);
        $users = $this->findAllUser($limit, $offset, $whereCond);
        return array_merge($organisations, $users);
    }
    
    public function findAllOrganisation($limit, $offset = 0, $whereCond = array(), $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('name' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();

        $where = join(' AND ', $whereCond);
        $queryBuilder
            ->select('o.*')
            ->from('organisation', 'o')
            ->where($where)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('o.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $searchData = $statement->fetchAll();

        $search = array();
        foreach ($searchData as $searchData) {
            $searchId = $searchData['id'];
            $searchData['type'] = 'organisation';
            $search[$searchId] = $this->buildSearch($searchData);
        }
        return $search;
    }

    public function findAllUser($limit, $offset = 0, $whereCond = array(), $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('name' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();

        $where = join(' AND ', $whereCond);
        $queryBuilder
            ->select('u.*')
            ->from('user', 'u')
            ->where($where)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('u.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $searchData = $statement->fetchAll();

        $search = array();
        foreach ($searchData as $searchData) {
            $searchId = $searchData['id'];
            $searchData['type'] = 'user';
            $search[$searchId] = $this->buildSearch($searchData);
        }
        return $search;
    }

    /**
     * Instantiates an search entity and sets its properties using db data.
     *
     * @param array $searchData
     *   The array of db data.
     *
     * @return \MyApp\Entity\Search
     */
    protected function buildSearch($searchData)
    {
        $search = new Search();
        $search->setId($searchData['id']);
        $search->setName($searchData['name']);
        $search->setType($searchData['type']);
        return $search;
    }
}
