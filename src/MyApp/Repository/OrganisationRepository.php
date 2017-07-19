<?php

namespace MyApp\Repository;

use Doctrine\DBAL\Connection;
use MyApp\Entity\Organisation;

/**
 * Organisation repository
 */
class OrganisationRepository implements RepositoryInterface
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
     * Saves the organisation to the database.
     *
     * @param \MyApp\Entity\Organisation $organisation
     */
    public function save($organisation)
    {
        $organisationData = array(
            'name' => $organisation->getName(),
            'address' => $organisation->getAddress(),
            'telephone' => $organisation->getTelephone(),
        );

        if ($organisation->getId()) {
            $this->db->update('organisation', $organisationData, array('id' => $organisation->getId()));
        } else {
            // The organisation is new, note the creation timestamp.
            //$organisationData['created_at'] = time();

            $this->db->insert('organisation', $organisationData);
            // Get the id of the newly created organisation and set it on the entity.
            $id = $this->db->lastInsertId();
            $organisation->setId($id);
        }
    }

    /**
     * Deletes the organisation.
     *
     * @param \MyApp\Entity\Organisation $organisation
     */
    public function delete($organisation)
    {
        $this->db->update(
            'organisation',
            array('is_deleted' => 1, 'Updated' => strftime('%F %T', time())),
            array('id' => $organisation->getId())
        );
        //return $this->db->delete('organisation', array('id' => $organisation->getId()));
    }

    /**
     * Returns the total number of organisation.
     *
     * @return integer The total number of organisation.
     */
    public function getCount()
    {
        return $this->db->fetchColumn('SELECT COUNT(id) FROM organisation');
    }

    /**
     * Returns an organisation matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MyApp\Entity\Organisation|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $organisationData = $this->db->fetchAssoc('SELECT * FROM organisation WHERE id = ?', array($id));
        return $organisationData ? $this->buildOrganisation($organisationData) : false;
    }

    /**
     * Returns a collection of organisation, sorted by name.
     *
     * @param integer $limit
     *   The number of organisation to return.
     * @param integer $offset
     *   The number of organisation to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of organisation, keyed by organisation id.
     */
    public function findAll($limit, $offset = 0, $whereCond = array(), $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('name' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        if ($whereCond) {
            $where = join(' AND ', $whereCond);
            $queryBuilder
                ->select('o.*')
                ->from('organisation', 'o')
                ->where($where)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('o.' . key($orderBy), current($orderBy));
        } else {
            $queryBuilder
                ->select('o.*')
                ->from('organisation', 'o')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('o.' . key($orderBy), current($orderBy));
        }
        $statement = $queryBuilder->execute();
        $organisationData = $statement->fetchAll();

        $organisation = array();
        foreach ($organisationData as $organisationData) {
            $organisationId = $organisationData['id'];
            $organisation[$organisationId] = $this->buildOrganisation($organisationData);
        }
        return $organisation;
    }

    /**
     * Instantiates an organisation entity and sets its properties using db data.
     *
     * @param array $organisationData
     *   The array of db data.
     *
     * @return \MyApp\Entity\Organisation
     */
    protected function buildOrganisation($organisationData)
    {
        $organisation = new Organisation($organisationData);
        $organisation->setId($organisationData['id']);
        $organisation->setName($organisationData['name']);
        $organisation->setAddress($organisationData['address']);
        $organisation->setTelephone($organisationData['telephone']);
        return $organisation;
    }
}
