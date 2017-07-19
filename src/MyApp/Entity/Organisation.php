<?php

namespace MyApp\Entity;

class Organisation extends GenericEntity
{
    /**
     * Organisation id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Organisation name.
     *
     * @var string
     */
    protected $name;

    /**
     * Organisation address.
     *
     * @var string
     */
    protected $address;


    /**
     * The filename of the main artist telephone.
     *
     * @var string
     */
    protected $telephone;


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

    public function getShortAddress()
    {
        return $this->shortAddress;
    }

    public function setShortAddress($shortAddress)
    {
        $this->shortAddress = $shortAddress;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
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
