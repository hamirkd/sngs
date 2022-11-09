<?php

class DB {

    const DB_SERVER = "127.0.0.1";
    const DB_USER = "root";
    const DB_PASSWORD = "adminroot";
    const DB = "bd_songo_sgns";
    const PROPRIETAIRE = "LINA ENTREPRISE";

    protected $proprietaire;
    private $characterSet;
    private $mysqli = NULL;
    private $options = array();

    public function __construct(array $options = array('db_host' => self::DB_SERVER,
        'db_user' => self::DB_USER,
        'db_password' => self::DB_PASSWORD,
        'db_name' => self::DB)) {
        $this->options = $options;
        $this->proprietaire = self::PROPRIETAIRE;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getProprietaire() {
        return $this->proprietaire;
    }

    public function getMysqlObject() {
        return $this->mysqli;
    }

    public function dbConnect(array $opt) {
        $this->mysqli = new mysqli("p:" . $opt['db_host'], $opt['db_user'], $opt['db_password'], $opt['db_name']);
    }

    public function setCharacterSet($characterSet) {

        $this->characterSet = $characterSet;
        $this->mysqli->set_charset($this->getCharacterSet());
    }

    public function getCharacterSet() {

        return $this->characterSet;
    }

}

?>