<?php
class Students {
    private $conn;
    private $table_name = "students";

    public $student;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function set_student ($student) {
        $this->student = $student; 
    }

    function get_all() {
        $query = "SELECT * FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return $data = 'error';
        
    }
	
	function get_all_active() {
        $query = "SELECT *
                FROM
                    `{$this->table_name}`
                WHERE
                    `Status` = 'Y'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return $data = 'error';
    }
    
    function get_all_by_status($status = 'Y') {
        $status = strtoupper($status);
        $query = "SELECT *
                FROM
                    `{$this->table_name}`
                WHERE
                    `Status` = '{$status}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return $data = 'error';
    }

    function get_all_by_centre_and_status($centreID = 1, $status = 'Y') {
        $status = strtoupper($status);
        $query = "SELECT *
                FROM
                    `{$this->table_name}`
                WHERE
                    `CentreID` = {$centreID} AND
                    `Status` = '{$status}'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return $data = 'error';
    }

    function read() {
        $query = "SELECT
                    count(*),
                    s.StudentID,
                    s.FullName,
                    s.Comment,
                    s.DOB,
                    s.ParentID,
                    s.CentreID,
                    s.DaysAttending,
                    s.Sessions,
                    s.Status,
                    p.FullName as ParentName,
                    p.username as ParentUsername,
                    c.Name as CentreName
                FROM
                    {$this->table_name} s 
                    JOIN centres c on s.CentreID = c.CentreID
                    JOIN parents p on s.ParentID = p.ParentID
                ORDER BY
                    StudentID
            ";

 
    }

    function read_single( $id ) {
        $query = "SELECT
                    s.`StudentID`,
                    s.`FullName`,
                    s.`Comment`,
                    s.`DOB`,
                    s.`ParentID`,
                    s.`CentreID`,
                    s.`DaysAttending`,
                    s.`Sessions`,
                    s.`Status`,
                    p.FullName as ParentName,
                    p.username as ParentUsername,
                    p.Email as ParentEmail,
                    p.Phone as ParentPhone,
                    c.Name as CentreName
                FROM
                    {$this->table_name} s 
                    JOIN centres c on s.CentreID = c.CentreID
                    JOIN parents p on s.ParentID = p.ParentID
                WHERE
                    s.StudentID = {$id}
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }

        return $data = 'error';
    }

    function read_by_centre() {
        $query = "SELECT
                    `StudentID`,
                    `FullName`,
                    `Comment`,
                    `DOB`,
                    `ParentID`,
                    `CentreID`,
                    `DaysAttending`,
                    `Sessions`,
                    `Status`
                FROM
                    " . $this->table_name . "
                WHERE
                    CentreID = '" . $this->student['centre_id'] . "'
                    AND Status = 'Y'
                ORDER BY
                    FullName
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function read_by_parent( $parent_id ) {
        $query = "SELECT
                    s.StudentID,
                    s.FullName,
                    s.Comment,
                    s.DOB,
                    s.ParentID,
                    s.CentreID,
                    s.Room,
                    s.DaysAttending,
                    s.Sessions,
                    s.Status
                FROM
                    students s
                WHERE
                    s.ParentID = {$parent_id}
                ORDER BY
                    s.StudentID
            ";
        $stmt = $this->conn->prepare($query);

        if ( $stmt->execute() ) {
            $data = $stmt->fetchAll();
        } else {
            $data = 'error';
        }
        
        return $data;
    }

    function get_youngest_by_skill($skill_id) {
        $query = "SELECT
                    a.StudentID, a.Score,
                    s.DOB as bdate
                FROM 
                    `assessments` a JOIN
                    `students` s on a.StudentID = s.StudentID
                WHERE
                    SkillID = {$skill_id}
                ORDER BY
                    s.DOB DESC
                LIMIT 
                    1
            ";
        $stmt = $this->conn->prepare($query);

        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }

        return $data;
    }

    function get_oldest_by_skill($skill_id) {
        $query = "SELECT
                    a.StudentID, a.Score,
                    s.DOB as bdate
                FROM 
                    `assessments` a JOIN
                    `students` s on a.StudentID = s.StudentID
                WHERE
                    SkillID = {$skill_id}
                ORDER BY
                    s.DOB ASC
                LIMIT 
                    1
            ";
        $stmt = $this->conn->prepare($query);

        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }

        return $data;
    }

    function create() {
        $query = "INSERT INTO  " . $this->table_name . " (
                    `FirstName`,
                    `LastName`,
                    `FullName`,
                    `Comment`,
                    `DOB`,
                    `ParentID`,
                    `CentreID`,
                    `DaysAttending`,
                    `Sessions`,
                    `Status`
                )
                VALUES (
                    '" . $this->student['first_name'] . "',
                    '" . $this->student['last_name'] . "',
                    '" . $this->student['fullname'] . "',
                    '" . $this->student['comment'] . "',
                    '" . $this->student['dob'] . "',
                    '" . $this->student['parent_id'] . "',
                    '" . $this->student['centre_id'] . "',
                    '" . $this->student['days_attending'] . "',
                    '" . $this->student['sessions'] . "',
                    '" . $this->student['status'] . "'
                )
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            $this->student['id'] = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    function update() {
        $dateUpdated = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table_name . "
                SET
                    FullName = '" . $this->student['fullname'] . "',
                    Comment = '" . $this->student['comment'] . "',
                    DOB = '" . $this->student['dob'] . "',
                    ParentID = '" . $this->student['parent_id'] . "',
                    CentreID = '" . $this->student['centre_id'] . "',
                    DaysAttending = '" . $this->student['days_attending'] . "',
                    Sessions= '" . $this->student['sessions'] . "',
                    Status = '" . $this->student['status'] . "',
                    dateUpdated = '" . $dateUpdated . "'
                WHERE
                    StudentID = '" . $this->student['id'] . "'
            ";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function update_uid( $id, $uid ) {
        $dateUpdated = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table_name . "
                SET
                    studentUID = '" . $uid . "',
                    dateUpdated = '" . $dateUpdated . "'
                WHERE
                    StudentID = '" . $id . "'
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE
                    StudentID = '" . $this->student['id'] . "'
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
                " . $this->table_name . "
            WHERE
                LastName = '" . $this->student['lastname'] . "'
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ( $stmt->rowCount() > 0 ) {
            return true;
        }

        return false;
    }

}
