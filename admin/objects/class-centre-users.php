<?php
class Users {

    private $conn;
    private $table_name = "users";

    public $users;

    // constructor with $db as database connection
    public function __construct( $db ) {
        $this->conn = $db;
    }

    function get_user( $uid ) {
        $query = "SELECT * FROM {$this->table_name}
                WHERE
                    userUID = '{$uid}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }
            
        return $data = 'error';
    }
    
    function get_all() {
        $query = "SELECT * FROM {$this->table_name}";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
            
        return $data = 'error';
    }
    
    function get_all_asc() {
        $query = "SELECT * FROM {$this->table_name} ORDER BY `fullname`";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
            
        return $data = 'error';
    }

    function read() {
        $query = "SELECT * 
                FROM
                    {$this->table_name}
                WHERE
                    type != 'S' AND
                    status != 0 
                ORDER BY
                    fullname
            ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt;
    }
    
    
    function read_single() {
        $query = "SELECT *
                FROM
                    {$this->table_name}
                WHERE
                    id = '{$this->users['id']}'
            ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt;
    }

    
    function create() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }
        
        if ( !isset($_SESSION['username']) ) {
            return false;
        }
        $max_trys = 5;
        $attempts = 0;
        
        do {
            try {
                $result = $this->insert_model();
            } catch (PDOException $e) {
                
                $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
                if ( strpos( $e->getMessage(), $existingkey ) !== FALSE ) {
                    $attempts++;
                    sleep(1);

                    if ( $attempts == 3 ) {
                        return false;
                    }
                    
                    continue;
                }

                return false;
            }
            
            break;
            
        } while($attempts < $max_trys);
            
        return $result;
    }
    
    
    function insert_model() {
        $date_added = date('Y-m-d H:i:s');
        $raw_pass = $this->users['password'];
        $pass = hash('sha256', $raw_pass);
        $current_user = $_SESSION['username'];
        $status = 1;
        $uuid = substr( uniqid(), -8 ) . '-' .
            substr( uniqid(), 0, 4 ) . '-' .
            substr( uniqid(), -4 ) . '-' .
            substr( uniqid(), -8, 4 ) . '-' .
            substr( uniqid(), -12 );
        
        $query = "INSERT INTO  {$this->table_name} (
                    `fullname`,
                    `username`,
                    `password`,
                    `type`,
                    `created`,
                    `editorId`,
                    `userUID`,
                    `status`
                )
                VALUES (
                    '{$this->users['fullname']}',
                    '{$this->users['username']}',
                    '{$pass}',
                    '{$this->users['type']}',
                    '{$date_added}',
                    '{$current_user}',
                    '{$uuid}',
                    '{$status}'
                )
            ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    
    function update() {
    
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }
        
        if ( isset($_SESSION['username']) ) {
            return false;
        }
            
        $date_updated = date('Y-m-d H:i:s');
        $current_user = $_SESSION['username'];

        $query = "UPDATE {$this->table_name}
                SET
                    fullname = '{$this->users['fullname']}',
                    type = '{$this->users['type']}',
                    dateUpdated = '{$date_updated}',
                    editorId = '{$current_user}'        
                WHERE
                    id = '{$this->users['id']}'
            ";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }
    }
    
    function update_uid( $id, $uid ) {
        $date_updated = date('Y-m-d H:i:s');
        $query = "UPDATE {$this->table_name}
                SET
                    userUID = '{$uid}',
                    dateUpdated = '{$date_updated}'
                WHERE
                    id = '{$id}'
            ";

        $stmt = $this->conn->prepare($query);
        
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }
    
    /**
     * PJS - Delete modified to use status = 0 rather than Delete permanently
     * either for possible return and/or for data integrity eg staff<->student etc
     * @return boolean
     */
    function delete() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ( !isset($_SESSION['username']) ) {
            return false;
        }
        
        $query = "UPDATE {$this->table_name}
                SET
                    status = 0 
                WHERE
                    id = " . $this->users['id']
            ;
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }


    function login() {
        $raw_pass = $this->users['password'];
        $pass = hash('sha256', $raw_pass);

        $query = "SELECT
                    `id`,
                    `fullname`,
                    `username`,
                    `password`,
                    `created`,
                    `type` 
                FROM
                    {$this->table_name}
                WHERE
                    username = '{$this->users['username']}' AND
                    password = '{$pass}'
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    
    function exist() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE
                    username = '{$this->users['username']}'
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ( $stmt->rowCount() > 0 ) {
            return true;
        }
        
        return false;
    }
}