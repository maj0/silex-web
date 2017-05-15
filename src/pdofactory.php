<?php

namespace app\config;

abstract class PDOFactory {

    protected $db;

    public function __construct()
    {               
        $db = new \PDO('mysql:host=localhost;dbname=silex', 'root', '');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->db = $db;
    }
}