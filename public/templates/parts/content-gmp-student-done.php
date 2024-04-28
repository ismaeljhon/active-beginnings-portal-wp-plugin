<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';
require_once PORTAL_URI . 'admin/objects/class-gross-motor-plan.php';

$db = new Database();
$centre_obj = new Centres($db->conn);
$gmpplan_obj = new GMP_Plan($db->conn);

if (isset($_POST['centre_id'])) {
    $centre_id = $_POST['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);

    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);

//print_r($_POST);
    $plan_id = $_POST['gmpplan_id'];
    $staff = implode(", ", $_POST['staff']);
    $student_plan = $_POST['student'];

    foreach($student_plan as $student) {
        foreach($student['skill'] as $student_skill) {
            if ( isset($student_skill['id']) ) {
                $student_plan_data = array(
                    'gmpplan_id' => $plan_id,
                    'student_id' => $student['id'],
                    'skill_id'   => $student_skill['id'],
                    'staff'      => $staff,
                    'notes'      => $student_skill['note']
                );
                $gmpplan_obj->insert_student_plan($student_plan_data);
            }
        }
    }

}

?>
<section class="student-assessment gmp">
	<div class="container">
        <div class="heading">
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
            </div>
            <div class="col">
                <h6>Center Summary Report: <?php echo date('M Y') ?></h6>
                <h2><?php echo $centre['Name'] ?></h2>
            </div>
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>  
        </div>
    </div>
</section>
<section class="centre-report gmp">
    <div class="heading-note">
        <p>GMP Plan Completed</p>
    </div>
    <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=review">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />

        <div class="skill skill-done">
            <img class="" width="300" src="<?php echo PORTAL_URL . 'assets/img/'; ?>icon-gmp-certified.svg" />
        </div>
        <div class="form-actions">
            <a class="btn btn-back" href="#">Back</a>
            <button class="btn btn-next" type="submit">Show Report</button>
        </div>
    </form>
</section>