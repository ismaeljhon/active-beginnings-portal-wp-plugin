<?php

class Database {

    private $host       = "";
    private $db_name    = "";
    private $username   = "";
    private $password   = "";
    
    public $conn;

    public function __construct () {
        $this->conn = null;
        $this->set_credentials();

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch ( PDOException $exception ) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    private function set_credentials() {
        $this->host =  get_option( 'db_host' );
        $this->db_name =  get_option( 'db_name' );
        $this->username =  get_option( 'db_user' );
        $this->password = get_option( 'db_password' );
    }

}
