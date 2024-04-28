<?php
class Centres {

    private $conn;
    private $table_name = "centres";

    // object properties
    public $centre;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * in var(36) UID
     * @return array $data model
     */
    function get_centre($uid) {
        
        $query = "SELECT * FROM `{$this->table_name}` 
                WHERE
                    centreUID = '{$uid}' ||
                    centreID = '{$uid}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }
            
        return 'error';
    }
    
    /**
     *
     * @return array $datamodel
     */
    function get_all() {
        
        $query = "SELECT * FROM `{$this->table_name}` ORDER BY `Name` ASC";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
            
        }
        return 'error';
        
    }

    function get_all_active() {
        $query = "SELECT
                    s.`StudentID`,
                    s.`FullName`,
                    c.`CentreID`,
                    c.`Name`
                FROM
                    `students` s
                LEFT JOIN
                    `{$this->table_name}` c on s.`CentreID` = c.`CentreID`
                WHERE 
                    s.`Status` = 'Y'
                GROUP BY
                    c.`CentreID`
                ORDER BY
                    c.`CentreID`
        ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
            
        }
        return 'error';
    }
    
    function get_all_with_active_days() {
                
        $query = "SELECT * FROM `{$this->table_name}`
            WHERE
                `Mon` = 'Y' OR
                `Tue` = 'Y' OR
                `Wed` = 'Y' OR
                `Thu` = 'Y' OR
                `Fri` = 'Y'
            ORDER BY 
                `Name` ASC
        ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
            
        }
        return 'error';
    }

    function get_centre_reports($center_id) {
        $query = "CALL `centre_report`({$center_id})";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
            
        return 'error';
    }

    // read all centres
    function read() {
        $query = "SELECT * FROM `centres`ORDER BY `Name`";

        $stmt = $this->conn->prepare($query);
        // execute query

        $stmt->execute();

        return $stmt;
    }

    function read_single() {
        $query = "SELECT * FROM {$this->table_name}
                    WHERE
                        CentreID = '{$this->centre['centre_id']}'
                ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }



    // create parent
    function create() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        $currentUser = $_SESSION['username'];
        $dateAdded = date('Y-m-d H:i:s');
        $uuid = substr( uniqid(), -8 ) . '-' .
            substr( uniqid(), 0, 4 ) . '-' .
            substr( uniqid(), -4 ) . '-' .
            substr( uniqid() ,-8 ,4 ) . '-' .
            substr( uniqid(), -12 );

        $query = "INSERT INTO  `{$this->table_name}`
                    (
                        `Name`,
                        `Subgroup`,
                        `ContactName`,
                        `ContactPhone`,
                        `EmailAddress`,
                        `dateCreated`,
                        `editorID`,
                        `centreUID`
                    )
                    VALUES
                    (
                        '{$this->centre['name']}',
                        '{$this->centre['subgroup']}',
                        '{$this->centre['contact_name']}',
                        '{$this->centre['contact_phone']}',
                        '{$this->centre['email_address']}',
                        '{$dateAdded}',
                        '{$currentUser}',
                        '{$uuid}'
                    )
                ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            $this->centre['centre_id'] = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // update parent
    function update() {
        $query = "UPDATE
                    {$this->table_name}
                SET
                    Name = '" . addslashes($this->centre['name']) . "',
                    Subgroup = '" . $this->centre['subgroup'] . "',
                    ContactName = '" . $this->centre['contact_name'] . "',
                    ContactPhone = '" . $this->centre['contact_phone'] . "',
                    EmailAddress = '" . $this->centre['email_address'] . "'
                WHERE
                    CentreID = '" . $this->centre['centre_id'] . "'
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function updateUID($id, $uid) {
        $dateUpdated = date('Y-m-d H:i:s');
        $query = "UPDATE {$this->table_name} 
                SET
                    centreUID = '{$uid}',
                    dateUpdated = '{$dateUpdated}'
                WHERE
                    CentreID = '{$id}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    // delete student
    function delete() {
        $query = "DELETE FROM
                    {$this->table_name}
                WHERE
                    CentreID = '{$this->centre['centre_id']}'
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }


    function exist() {
        $query = "SELECT *
                FROM
                    {$this->table_name}
                WHERE
                    Name = '{$this->centre['name']}'
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ( $stmt->rowCount() > 0 ) {
            return true;
        }
        
        return false;
    }

}
