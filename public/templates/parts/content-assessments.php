<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-assessments.php';

$db = new Database();
$assessments_obj = new Assessments($db->conn);

$user_id = get_current_user_id();
$uuid = get_field('portal_uid', 'user_' . $user_id);

$p_assessments = $assessments_obj->get_assessment_by_parent($uuid);


?>
<h3>Student Assessments</h3>
<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Skill</th>
            <th>Score</th>
            <th>Centre</th>
            <th>Time</th>
            <th>Assessor</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($p_assessments as $key => $assessment) {
            echo "<tr>";
            echo "<td>{$assessment['FullName']}</td>";
            echo "<td>{$assessment['SkillName']}</td>";
            echo "<td>{$assessment['Score']}</td>";
            echo "<td>{$assessment['CentreName']}</td>";
            echo "<td>{$assessment['TimeStamp']}</td>";
            echo "<td>{$assessment['Username']}</td>";
            echo "</tr>";
        }	
    ?>
    </tbody>
</table>

