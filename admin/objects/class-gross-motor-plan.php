<?php

class GMP_Plan {

    // database connection and table name
    private $conn;
    private $table_name = "gmpplan";

    // object properties
    public $plan;

    // constructor with $db as database connection
    public function __construct($db){

        $this->conn = $db;

    }

    function get_gmpplan( $id ) {
        
        $query = "SELECT * FROM `{$this->table_name}` WHERE
                    id = '{$id }'";
        
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

    function get_all_by_centre( $centre_id ) {
        $query = "SELECT 
                    gmp.id as id,
                    gmp.userUid as uid,
                    gmp.centreId as centreId,
                    gmp.staffNames as staff,
                    gmp.notes as notes,
                    gmp.dateUpdated as dateAdded,
                    u.fullname as fullname
                FROM
                    `{$this->table_name}` gmp,
                    `users` u
                WHERE
                    gmp.userUid = u.userUID AND
                    `centreId` = {$centre_id}
                ORDER BY
                    gmp.dateUpdated DESC
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return 'error';
    }

    function get_gmpplan_by_centre_id( $centre_id ) {
        $query = "SELECT * 
            FROM
                `{$this->table_name}`
            WHERE
                `centreId` = {$centre_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }
        
        return false;
    }


    function create_gmpplan( $userUid, $centre_id ) {
        //$query = "CALL insert_gmp_plan({$userUid}, {$centre_id}, '', @newPlanId);";
        $query = "INSERT INTO `{$this->table_name}` (userUid, centreId) VALUES ('{$userUid}', {$centre_id})";

        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $this->conn->lastInsertId();
        }
        
        return 0;
    }


    // update gmpplan
    function update_gmpplan( $gmpplan_id, $staff, $notes = '' ) {
        $current_date = date('Y-m-d h:i:s');
        $query = "UPDATE {$this->table_name}
                SET
                   `staffNames` = ?,
                   `notes` = ?,
                   `dateUpdated` = ?
                WHERE
                    id = ?
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $staff, PDO::PARAM_STR);
        $stmt->bindParam(2, $notes, PDO::PARAM_STR);
        $stmt->bindParam(3, $current_date, PDO::PARAM_STR);
        $stmt->bindParam(4, $gmpplan_id, PDO::PARAM_INT);

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

    function get_skills_by_gmpplan_id( $gmpplan_id ) {
        $query = "SELECT * 
            FROM
                `gmpplanskill`
            WHERE
                `gmpplanId` = {$gmpplan_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return false;
    }

    function get_skill_plans($centre_id, $skill_id) {
        $query = "CALL gmp_skills({$centre_id}, {$skill_id})";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return false;
    }

    function get_student_skill_plans($student_id, $skill_id) {
        $query = "CALL gmp_student_skills({$student_id}, {$skill_id})";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }
        
        return false;
    }

    function plan_skill_exist($gmpplan_id, $skill_id) {
        $query = "SELECT * 
            FROM
                `gmpplanskill`
            WHERE
                `gmpplanId` = {$gmpplan_id} AND
                `skillId` = {$skill_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }

        return 0;
    }

    function insert_plan_skill($gmpplan_id, $skill_id) {
        $current_date = date('Y-m-d h:i:s');
        $query = "INSERT INTO `gmpplanskill` (gmpplanId, skillId, dateAdded) VALUES ('{$gmpplan_id}', {$skill_id}, '{$current_date}')";

        $plan_skill_id = $this->plan_skill_exist( $gmpplan_id, $skill_id );
        if ($plan_skill_id != 0) {
            $gmpplanskill_id = $plan_skill_id['id'];
            $query = "UPDATE 
                    `gmpplanskill`
                SET
                    gmpplanId = {$gmpplan_id},
                    skillId = {$skill_id},
                    dateUpdated = '{$current_date}'
                WHERE
                    id = {$gmpplanskill_id}
                ";
        }


        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {

            if ($plan_skill_id != 0)
                return $plan_skill_id['id'];

            return $this->conn->lastInsertId();
        }
        
        return 0;
    }

    function delete_all_skill_by_plan_id($plan_id) {
        $query = "DELETE FROM
                    `gmpplanskill`
                WHERE
                gmpplanId = '{$plan_id}'
            ";

        // prepare query
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function plan_skill_step_exist($gmpplanskill_id, $skill_step_id) {
        $query = "SELECT * 
            FROM
                `gmpplanskillstep`
            WHERE
                `gmpplanskillId` = {$gmpplanskill_id} AND
                `skilIStepId` = {$skill_step_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetch();
        }

        return 0;
    }
    
    function delete_skillstep_by_skill($skill_id) {
        $query = "DELETE FROM
                    `gmpplanskillstep`
                WHERE
                gmpplanskillId = '{$skill_id}'
            ";

        // prepare query
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return true;
        }

        return false;
    }

    function get_skillplans_by_id($gmpplanskill_id) {
        $query = "SELECT * 
            FROM
                `gmpplanskillstep`
            WHERE
                `gmpplanskillId` = {$gmpplanskill_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return 0;
    }

    function insert_plan_skill_step($gmpplanskill_id, $skill_step_id, $notes) {
        $current_date = date('Y-m-d h:i:s');
        //$plan_skill_step_id = $this->plan_skill_step_exist( $gmpplanskill_id, $skill_step_id );
        $plan_skill_step_id = 0;

        if ($plan_skill_step_id != 0) {
            $gmpplanskill_id = $plan_skill_step_id['id'];
            $query = "UPDATE 
                    `gmpplanskillstep`
                SET
                    gmpplanskillId = ?,
                    skilIStepId = ?,
                    notes = ?,
                    dateUpdated = ?
                WHERE
                    id = ?
                ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $gmpplanskill_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $skill_step_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $notes, PDO::PARAM_STR);
            $stmt->bindParam(4, $current_date, PDO::PARAM_STR);
            $stmt->bindParam(5, $gmpplanskill_id, PDO::PARAM_INT);

            if ( $stmt->execute() ) {
                return $gmpplanskill_id;
            }

            return 0;
        } else {
            $query = "INSERT INTO `gmpplanskillstep` (gmpplanskillId, skilIStepId, notes, dateAdded) VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $gmpplanskill_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $skill_step_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $notes, PDO::PARAM_STR);
            $stmt->bindParam(4, $current_date, PDO::PARAM_STR);

            if ( $stmt->execute() ) {
                return $this->conn->lastInsertId();
            }

            return 0;
        }
          
    }

    function get_best_students($centre_id) {
        $query = "CALL gmp_students_best({$centre_id})";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return false;
        
    }

    function get_worst_students($centre_id) {
        $query = "CALL gmp_students_worst({$centre_id})";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return false;
        
    }

    function get_student_scores($student_id) {
        $query = "CALL student_skill_scores({$student_id})";
    
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return false;
        
    }

    function insert_student_plan($data) {
        $current_date = date('Y-m-d h:i:s');
        $query = "INSERT INTO `gmpplanstudent` (gmpplanId, studentId, skillId, staffNames, notes, dateAdded) VALUES (?, ?, ?, ?, ?, ?)";
            
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $data['gmpplan_id'], PDO::PARAM_INT);
        $stmt->bindParam(2, $data['student_id'], PDO::PARAM_INT);
        $stmt->bindParam(3, $data['skill_id'], PDO::PARAM_INT);
        $stmt->bindParam(4, $data['staff'], PDO::PARAM_STR);
        $stmt->bindParam(5, $data['notes'], PDO::PARAM_STR);
        $stmt->bindParam(6, $current_date, PDO::PARAM_STR);

        if ( $stmt->execute() ) {
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    function get_studentplan_by_plan_id($plan_id) {
        $query = "SELECT * 
            FROM
                `gmpplanstudent`
            WHERE
                `gmpplanId` = {$plan_id}
            ";
        
        $stmt = $this->conn->prepare($query);
        if ( $stmt->execute() ) {
            return $data = $stmt->fetchAll();
        }

        return 0;
    }

}