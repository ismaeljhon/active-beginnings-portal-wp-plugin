<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-parents.php';
require_once PORTAL_URI . 'admin/objects/class-students.php';

$db = new Database();
$parent_obj = new Parents($db->conn);
$parent = $parent_obj->get_parent($uuid);

$student_obj = new Students($db->conn);
$students = $student_obj->read_by_parent($parent['ParentID']);

?>
<section class="student-assessment">
	<div class="container">
        <div class="heading">
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
            </div>
            <div class="col">
                <h2>Students</h2>
            </div>
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>
        </div>
    </div>
</section>	
<section class="students ">
    <div class="container">
        <?php foreach( $students as $student ) : ?>
        <div class="blurb">
            <a href="?tab=assessments&student_id=<?php echo $student['StudentID'] ?>">
                <img width="80" height="70" src="<?php echo PORTAL_URL . 'assets/img/icon-user.svg'?>" alt="icon-user" />
                <span><?php echo $student['FullName'] ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>