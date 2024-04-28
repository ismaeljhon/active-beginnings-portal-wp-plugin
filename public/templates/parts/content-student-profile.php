<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-students.php';

$db = new Database();
$student_obj = new Students($db->conn);

$student_id = $_GET['student_id'] ?? null; 

if ($student_id) :
    $student = $student_obj->read_single($student_id);
?>
<section class="dashboard centre-students">
    <div class="container">
        <div class="heading">
            <h6>Profile</h6>
            <h2><?php echo $student['FullName']; ?></h2>
        </div>
    </div>
</section>
<section class="students student-profile">
    <div class="container">
        <div class="col profile-image">
            <img width="100%" src="<?php echo PORTAL_URL . 'assets/img/icon-user.svg'?>" alt="icon-user" />
        </div>
        <div class="col profile-details">
            <ul>
                <li><span>Full Name:</span> <?php echo $student['FullName']; ?></li>
                <li><span>DOB:</span> <?php echo $student['DOB']; ?></li>
                <li><span>Status:</span> <?php echo $student['Status'] == 'Y' ? 'Active' : 'Inactive'; ?></li>
                <li><span>Parent:</span> <?php echo $student['ParentName']; ?></li>
                <li><span>Phone:</span> <?php echo $student['ParentPhone']; ?></li>
                <li><span>Email:</span> <?php echo $student['ParentEmail']; ?></li>
                <li class="comments">
                    <span>Comments:</span>
                    <p><?php echo $student['Comment'] != '' ? $student['Comment'] : 'N/A'; ?></p>
                </li>
                <li class="action">
                <?php if ($student['Status'] == 'Y') : ?>
                <a href="?tab=assessments&student_id=<?php echo $student['StudentID'] ?>">
                    <span>View Report</span>
                </a>
                <?php else: ?>
                    <a class="activate" href="?tab=assessments&active=<?php echo $active ?>&activate=<?php echo $student['StudentID'] ?>">
                        <span>Activate</span>
                    </a>
                <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</section>
<?php
endif;