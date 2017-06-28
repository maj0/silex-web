<?php

namespace MyApp\Entity;

class User extends GenericEntity
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * User name.
     *
     * @var string
     */
    protected $name;

    /**
     * User address.
     *
     * @var string
     */
    protected $address;


    /**
     * Telephone number
     *
     * @var string
     */
    protected $telephone;

    /**
     * User email.
     *
     * @var string
     */
    protected $email;

    /**
     * User employee ID.
     *
     * @var string
     */
    protected $employee_ID;

    /**
     * User role ID.
     *
     * @var number
     */
    protected $role;

    /**
     * User organisation ID.
     *
     * @var string
     */
    protected $organisation_ID;

    /**
     * User birthdate.
     *
     * @var date
     */
    protected $birthdate;

    /**
     * User probation.
     *
     * @var number
     */
    protected $probation = false;

    /**
     * User password.
     *
     * @var string
     */
    protected $password;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getEmployeeID()
    {
        return $this->employee_ID;
    }
    public function getEmployee_ID()
    {
        return $this->employee_ID;
    }

    public function setEmployeeID($employee_ID)
    {
        $this->employee_ID = $this['employee_ID'] = $employee_ID;
    }
    
    public function setEmployee_ID($employee_ID)
    {
        $this->employee_ID = $this['employee_ID'] = $employee_ID;
    }
    
    public function getOrganisationID()
    {
        return $this->organisation_ID;
    }
    
    public function getOrganisation_ID()
    {
        return $this->organisation_ID;
    }

    public function setOrganisationID($organisation_ID)
    {
        $this->organisation_ID = $this['organisation_ID'] = $organisation_ID;
    }
    
    public function setOrganisation_ID($organisation_ID)
    {
        $this->organisation_ID = $this['organisation_ID'] = $organisation_ID;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    public function getProbation()
    {
        return empty($this->probation) ? false : true;
    }

    public function setProbation($probation)
    {
        $this->probation = empty($probation) ? false : true;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }
}
