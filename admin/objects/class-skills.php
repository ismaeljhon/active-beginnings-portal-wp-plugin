<?php

class Skills {

    // database connection and table name
    private $conn;
    private $table_name = "skills";

    // object properties
    public $skills;

    // constructor with $db as database connection
    public function __construct($db){

        $this->conn = $db;

    }

    function get_skill( $uid ) {
        
        $query = "SELECT * FROM `{$this->table_name}` WHERE
                    skillUID = '{$uid}'";
        
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
        $query = "SELECT * FROM `{$this->table_name}`";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return 'error';
    }

    
    // read all skills
    function read() {
        $query = "SELECT
                    `SkillID`,
                    `Seq`,
                    `Name`,
                    `Description`
                FROM
                    `{$this->table_name}`
                ORDER BY
                    Seq
            ";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }





    // get single student data
    function read_single() {
        $query = "SELECT
                      `SkillID`,
                      `Seq`,
                      `Name`,
                      `Description`
                FROM
                    `{$this->table_name}`
                WHERE
                    SkillID = '{$this->skills['skill_id']}'
            ";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }



    function skillsteps_byskill() {
      $query = "SELECT
                    `SkillStepID`,
                    `SkillID`,
                    `Seq`,
                    `Description`
                FROM
                    `skillsteps`
                WHERE
                    `SkillID` = {$this->skills['skill_id']}
                ORDER BY
                    Seq
            ";

      // prepare query statement
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      return $stmt;
    }



    // create student

    function create() {
        if ( $this->exist() ) {
            return false;
        }
        // query to insert record
        $query = "INSERT INTO  `{$this->table_name}`
                    (
                        `Seq`,
                        `Name`,
                        `Description`
                    )
                    VALUES
                    (
                        '{$this->skills['seq']}',
                        '{$this->skills['name']}',
                        '{$this->skills['desc']}'
                    )
            ";

        // prepare query
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            $this->skills['skill_id'] = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }



    // update student
    function update() {
        $query = "UPDATE {$this->table_name}
                SET
                    Seq = '{$this->skills['seq']}',
                    Name = '{$this->skills['name']}',
                    Description ='{$this->skills['desc']}'
                WHERE
                    SkillID = '{$this->skills['skill_id']}'
            ";

        // prepare query
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function update_UID( $id, $uid ) {
        $dateUpdated = date('Y-m-d H:i:s');
        $query = "UPDATE `{$this->table_name}`
                SET 
                    skillUID = '{$uid}',
                    dateUpdated = '{$dateUpdated}'
                WHERE
                    SkillID = '{$id}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    
    // delete student

    function delete() {
        // query to insert record
        $query = "DELETE FROM
                    `{$this->table_name}`
                WHERE
                    SkillID= '{$this->skills['skill_id']}'
            ";

        // prepare query
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }



    function exist() {
        $query = "SELECT *
            FROM
                `{$this->table_name}`
            WHERE
                Name = '{$this->skills['name']}'
        ";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ( $stmt->rowCount() > 0 ) {
            return true;
        }

        return false;
    }

}