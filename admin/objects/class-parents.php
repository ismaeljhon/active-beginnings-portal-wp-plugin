<?php
class Parents {

    private $conn;
    private $table_name = "parents";
    private $parent;
 
    public function __construct( $db ) {    
        $this->conn = $db;
    }

    public function set_parent($parent) {
        $this->parent = $parent;
    }

    function get_parent( $uid ) {
        $query = "SELECT * FROM `" . $this->table_name . "` WHERE
                    parentUID = '" . $uid . "'";
        
        $stmt = $this->conn->prepare($query);
        
        if ( $stmt->execute() ) {
            $data = $stmt->fetch();
        } else {
            $data = 'error';
        }
        
        return $data;
    }
    
    /**
     * @return array $data model
     */
    function get_all() {
        $query = "SELECT * FROM `".$this->table_name."`";
        $stmt = $this->conn->prepare($query);
        
        if( $stmt->execute() ) {
            $data = $stmt->fetchAll();
        } else {
            $data = 'error';
        }
        
        return $data;
    }
    
    function get_all_unsynced( $existing ) {
		$quotedArray = array_map(function($item) {
			return '"' . $item . '"';
		}, $existing);
        $exclude = implode( ',', $quotedArray );
        $query = "SELECT * 
                FROM
                    `{$this->table_name}`
                WHERE username NOT IN ({$exclude})
            ";
        $stmt = $this->conn->prepare($query);
        
        if( $stmt->execute() ) {
            $data = $stmt->fetchAll();
        } else {
            $data = 'error';
        }
        
        return $data;
    }

    function read() {
        $query = "SELECT
                    `ParentID`,
                    `FullName`,
                    `Email`,
                    `ReportEmail`,
                    `Phone`,
                    `Address1`,
                    `Suburb`,
                    `Postcode`,
                    `PaymentMethod`,
                    `Comment`,
                    `Status`,
                    `OrigID`,
                    `Consent`
                FROM 
                    " . $this->table_name . "
                GROUP BY
                    Email        
                ORDER BY
                    ParentID";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function read_with_students() {
        $query = "SELECT 
                    p.username,
                    p.ParentID, 
                    p.FullName,
                    p.Email,
                    p.ReportEmail,
                    p.Phone,
                    p.Address1,
                    p.Suburb,
                    p.Postcode,
                    p.PaymentMethod,
                    p.Comment,
                    p.Status
                    ParentStatus,
                    p.OrigID,
                    p.Consent,
                    s.StudentID,
                    s.FullName
                    StudentFullName,
                    s.Comment as StudentComment,
                    s.DOB,
                    c.Name as CentreName,
                    s.DaysAttending,
                    s.Sessions,
                    s.Status as StudentStatus
                FROM
                    `parents` p left outer join `students` s on p.ParentID = s.ParentID
                    left outer join centres c on s.CentreID = c.CentreID
                ORDER BY
                    ParentID,
                    StudentID
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function read_single() {
        $query = "SELECT
                    `username`,
                    `ParentID`,
                    `FullName`,
                    `Email`,
                    `ReportEmail`,
                    `Phone`,
                    `Address1`,
                    `Suburb`,
                    `Postcode`,
                    `PaymentMethod`,
                    `Comment`,
                    `Status`,
                    `OrigID`,
                    `Consent`
                FROM
                    " . $this->table_name . "
                WHERE
                    ParentID = '" . $this->parent['id'] . "'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function create() {
        $query = "INSERT INTO  " . $this->table_name . " (
                    `username`,
                    `FullName`,
                    `Email`, 
                    `ReportEmail`,
                    `Phone`,
                    `Address1`,
                    `Suburb`,
                    `Postcode`,
                    `PaymentMethod`,
                    `Comment`,
                    `Status`,
                    `OrigID`,
                    `Consent`,
                    `parentUID`
                )
            VALUES (
                '" . $this->parent['username'] . "',
                '" . $this->parent['full_name'] . "',
                '" . $this->parent['email'] . "',
                '" . $this->parent['report_email'] . "',
                '" . $this->parent['phone'] . "',
                '" . $this->parent['address1'] . "',
                '" . $this->parent['suburb'] . "',
                '" . $this->parent['postcode'] . "',
                '" . $this->parent['payment_method'] . "',
                '" . $this->parent['comment'] . "',
                '" . $this->parent['status'] . "',
                '" . $this->parent['orig_id'] . "',
                '" . $this->parent['consent'] . "',
                '" . $this->parent['uid'] . "'
            )";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    function update() {
        $query = "UPDATE parents SET
                    FullName='" . $this->parent['full_name'] . "',
                    Email='" . $this->parent['email'] . "',
                    ReportEmail='" . $this->parent['report_email'] . "',
                    Phone='" . $this->parent['phone'] . "',
                    Address1='" . $this->parent['address1'] . "',
                    Suburb='" . $this->parent['suburb'] . "',
                    Postcode='" . $this->parent['postcode'] . "',
                    PaymentMethod='" . $this->parent['payment_method'] . "',
                    Comment='" . $this->parent['comment'] . "',
                    Status='" . $this->parent['status'] . "',
                    OrigID='" . $this->parent['orig_id'] . "',
                    Consent='" . $this->parent['consent'] . "'
                WHERE
                    ParentID='" . $this->parent['id'] . "'
            ";

        $stmt = $this->conn->prepare($query);
        if( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function update_uid( $id, $uid ) {
        $query = "UPDATE `". $this->table_name. "` SET parentUID = '" . $uid .
        "' WHERE ParentID='" . $id . "'";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM
                    " . $this->table_name . "
                WHERE
                    ParentID= '".$this->parent['id']."'";

        $stmt = $this->conn->prepare($query);
        if($stmt->execute()){
            return true;
        }

        return false;
    }



    function exist() {
        $query = "SELECT *
            FROM
                " . $this->table_name . "
            WHERE
                FullName='" . $this->parent['full_name'] . "'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ( $stmt->rowCount() > 0 ) {
            return true;
        }
        
        return false;

    }

}