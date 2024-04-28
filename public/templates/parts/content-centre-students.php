<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';
require_once PORTAL_URI . 'admin/objects/class-students.php';

$db = new Database();
$centre_obj = new Centres($db->conn);
$centres = $centre_obj->get_all();

$student_obj = new Students($db->conn);

$status = $_GET['active'] ?? 'Y'; 
$students = $student_obj->get_all_by_status($status);
$title = $status == 'Y' ? 'Active Kids' : 'Inactive Kids';

?>
<section class="dashboard centre-students">
    <div class="container">
        <div class="heading">
            <h6>Assessments</h6>
            <h2><?php echo $title; ?></h2>
            <p>If a child has participated in our program, you will be able to check the child’s report below. </p>
        </div>
        <div class="form-control">
            <label for="select_centres">Filter by Centre:</label>
            <select id="select_centres" name="select_centres" >
                <option value="all" selected>All Centres</option>
            <?php foreach( $centres as $centre ) : ?>
                <option value="<?php echo $centre['CentreID']; ?>">
                    <?php echo $centre['Name']; ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>
</section>
<section class="centre-students">
    <div class="container">
        <table>
            <tr>
                <td>Name</td>
                <td>Status</td>
                <td></td>
            </tr>
            <?php foreach( $students as $student ) : ?>
            <tr class="blurb centre-<?php echo $student['CentreID'] ?>">
                <td><a class="link" href="?tab=student-profile&student_id=<?php echo $student['StudentID'] ?>"><?php echo $student['FullName']; ?></a></td>
                <td><?php echo ($student['Status'] == 'Y' ? 'Active' : 'Inactive');  ?></td>
                <td>
                <?php if ($status == 'Y') : ?>
                    <a href="?tab=assessments&student_id=<?php echo $student['StudentID'] ?>">
                        <span>View Report</span>
                    </a>
                <?php                                   else: ?>
                    <a href="?tab=assessments&student_id=<?php echo $student['StudentID'] ?>">
                        <span>View Report</span>
                    </a>
                <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>
