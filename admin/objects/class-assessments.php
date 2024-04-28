<?php

class Assessments {

    private $conn;
    private $table_name = "assessments";

    public $assessment;

    public function __construct( $db) {
        $this->conn = $db;
    }

    function get_assessment( $uid ) {
        $query = "SELECT * FROM `" . $this->table_name . "` WHERE
            assessUID = '" . $uid . "'
          ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
          return $data = $stmt->fetch();
        }

        return $data = 'error';
    }
    
    function get_assessment_by_parent( $uid ) {
        $query = "SELECT
                    a.AssessmentID as AssessmentID,
                    a.EventID as EventID,
                    a.StudentID as StudentID,
                    a.SkillID as SkillID,
                    c.Name as CentreName,
                    s.FullName as FullName,
                    p.username as ParentUsername,
                    p.FullName as ParentName,
                    a.Score as Score,
                    k.Name as SkillName,
                    a.TimeStamp as TimeStamp,
                    a.username as Username
                FROM
                    assessments a,
                    events e,
                    students s,
                    skills k,
                    parents p,
                    centres c
                WHERE
                    a.EventID = e.EventID and
                    e.CentreID = c.CentreID and
                    a.StudentID = s.StudentID and
                    a.SkillID = k.SkillID and
                    s.ParentID = p.ParentID and
                    p.parentUID = '{$uid}'
                ORDER BY
                    a.AssessmentID desc
                LIMIT
                    1500
            ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function get_latest_assessments_by_student($student_id) {
        $query = "CALL latest_assessments({$student_id})";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
          return $data = $stmt->fetchAll();
        }

        return $data = 'error';
    }
    
    function get_assessment_repot($student_id) {
        $query = "CALL assessment_report({$student_id})";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
          return $data = $stmt->fetchAll();
        }

        return $data = 'error';
    }

    function get_assessment_by_student( $student_id ) {
        $query = "SELECT
                    a.AssessmentID as AssessmentID,
                    a.EventID as EventID,
                    a.StudentID as StudentID,
                    a.SkillID as SkillID,
                    c.Name as CentreName,
                    s.FullName as FullName,
                    a.Score as Score,
                    k.Name as SkillName,
                    a.TimeStamp as TimeStamp,
                    a.username as Username
                FROM
                    assessments a,
                    events e,
                    students s,
                    skills k,
                    centres c
                WHERE
                    a.EventID = e.EventID and
                    e.CentreID = c.CentreID and
                    a.StudentID = s.StudentID and
                    a.SkillID = k.SkillID and
                    s.StudentID = '{$student_id}'
                ORDER BY
                    a.AssessmentID desc
                LIMIT
                    1500
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
          return $data = $stmt->fetchAll();
        }

        return $data = 'error';
    }

    function get_assessment_avg($skill_id) {
        $query = "SELECT
                    AssessmentID,
                    EventID,
                    SkillID,
                    AVG(Score) as AverageScore
                FROM
                    {$this->table_name}
                WHERE
                    SkillID = {$skill_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return $data = 'error';
    }
    
    /**
     *
     * @return array $datamodel
     */
    function get_all() {
        $query = "SELECT * FROM assessments WHERE assessUID is null";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return $data = 'error';
    }

    function read() {
        $query = "SELECT
                    a.AssessmentID as AssessmentID,
                    a.EventID as EventID, 
                    a.StudentID as StudentID,
                    a.SkillID as SkillID,
                    a.Score as Score,
                    s.FullName as FullName,
                    k.Name as SkillName,
                    a.TimeStamp as TimeStamp,
                    a.username as Username
                FROM
                    assessments a,
                    events e,
                    students s,
                    skills k
                WHERE 
                    a.EventID = e.EventID and
                    a.StudentID = s.StudentID and
                    a.SkillID = k.SkillID
                ORDER BY
                    a.AssessmentID desc
                LIMIT
                    1500
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function read_single () {

        $query = "SELECT
                        `AssessmentID`,
                        `EventID`,
                        `StudentID`,
                        `SkillID`,
                        `Score`
                    FROM
                        '" . $this->table_name . "'
                    WHERE
                        AssessmentID = '" . $this->assessment['id'] . "'
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
                $result = $this->insertmodel();
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
        
        } while( $attempts < $max_trys );

        return $result;
    }
    
    function insert_model() {
        
        $date_added = date('Y-m-d H:i:s');
        $raw_pass = $this->assessment['password'];
        $pass = hash('sha256', $raw_pass);
        $currentUser = $_SESSION['username'];
        $status = 1;
        $uuid = substr( uniqid(), -8 ) . '-' .
            substr( uniqid(), 0, 4 ) . '-' .
            substr( uniqid(), -4 ) . '-' .
            substr( uniqid(), -8 , 4) . '-' .
            substr( uniqid(), -12 );
        
        
        $query1 = "INSERT INTO  ". $this->table_name ." (
                    `EventID`,
                    `StudentID`,
                    `SkillID`,
                    `Score`,
                    `username`,
                    `res1`,
                    `res2`,
                    `res3`,
                    `res4`,
                    `res5`,
                    `res6`,
                    `res7`,
                    `res8`,
                    `res9`,
                    `res10`
                )
                VALUES (
                    '" . $this->assessment['event_id'] . "',
                    '" . $this->assessment['student_id'] . "',
                    '" . $this->assessment['skill_id'] . "',
                    '" . $this->assessment['score'] . "',
                    '" . $this->assessment['username'] . "',
                    " . $this->assessment['res1'] . ",
                    " . $this->assessment['res2'] . ",
                    " . $this->assessment['res3'] . ",
                    " . $this->assessment['res4'] . ",
                    " . $this->assessment['res5'] . ",
                    " . $this->assessment['res6'] . ",
                    " . $this->assessment['res7'] . ",
                    " . $this->assessment['res8'] . ",
                    " . $this->assessment['res9'] . ",
                    " . $this->assessment['res10'] ."
                )
            ";
        
        $query = "INSERT INTO  users (
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
                    '" . $this->assessment['full_name'] . "',
                    '" . $this->assessment['username'] . "',\
                    '" . $pass . "',
                    '" . $this->type . "',
                    '" . $date_added . "',
                    '" . $currentUser . "',
                    '" . $uuid . "',
                    '" . $status . "'
                )
            ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    EventID='" . $this->assessment['event_id'] . "',
                    StudentID='" . $this->assessment['student_id'] . "',
                    SkillID='" . $this->assessment['skill_id'] . "',
                    Score='" . $this->assessment['score'] . "'
                WHERE
                    AssessmentID='" .  $this->assessment['id'] . "'
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
          return true;
        }

        return false;
    }

    function update_uid( $id, $uid ) {
        $dateUpdated = date('Y-m-d H:i:s');
        
        $query = "UPDATE assessments
                SET
                    AssessUID = '" . $uid . "',
                    dateCreated = TimeStamp,
                    dateUpdated = '" . $dateUpdated . "'
                WHERE
                    AssessmentID = '" . $id . "'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE
                FROM
                    " . $this->table_name . "
                WHERE
                    AssessmentID = '" . $this->assessment['id'] . "'
                ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function isAlreadyExist(){
        $query = "SELECT *
            FROM
                " . $this->table_name . "
            WHERE
                EventID = '" . $this->assessment['event_id'] . "' and
                StudentID = '" . $this->assessment['student_id'] . "' and
                SkillID = '" . $this->assessment['skill_id'] . "'
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ( $stmt->rowCount() > 0 ) {
            return true;
        }
        
        return false;
    }

}
