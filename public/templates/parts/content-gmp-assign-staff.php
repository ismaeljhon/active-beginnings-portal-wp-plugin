<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';
require_once PORTAL_URI . 'admin/objects/class-gross-motor-plan.php';
require_once PORTAL_URI . 'admin/objects/class-centre-users.php';

$db = new Database();
$centre_obj = new Centres($db->conn);
$gmpplan_obj = new GMP_Plan($db->conn);
$user_obj = new Users($db->conn);

if (isset($_POST['centre_id'])) {
    $centre_id = $_POST['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);

    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);

    $plan_id = $_POST['plan_id'];

    $skill_ids = $_POST['skill_ids'];
    $general_note = isset($_POST['general-note']) ? $_POST['general-note'] : '';

    $users = array();
    foreach( $user_obj->get_all_asc() as $user) {
        $users[] = $user;
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
        <div class="heading-note">
            <p>Assign Staff</p>
        </div>
    </div>
</section>
<section class="centre-report gmp assign-staff">
    <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=done">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
        <input type="hidden" name="general_note" value="<?php echo $general_note; ?>" />
        <input type="hidden" name="skill_ids" value="<?php echo $skill_ids; ?>" />
        <div class="skill">
            <h4>
                <label for="skill-0">Assign Responsible Staff </label>
            </h4>
            <div class="skill-description">
                <h5>Description</h5>
                <p>Dribbling a ball will help children develop agility and coordination. More importantly it will help improve their foot-eye coordination. In soccer dribbling, the ability to keep the ball close may be a little tricky for most children. When the ball goes a little too far, the child tends to feel they have lost control over the ball. When this happens we tend to see them stop using their feet and use their hands to retrieve the ball. This is why soccer dribbling is important for a child's development, as their coordination with their feet will only improve.</p>
            </div>
            <div class="skill-actions">
                <div class="form-control">
                    <input type="text" name="staff[]" placeholder="Staff Names..." />
                </div>
                <div class="form-control">
                    <!--<select name="staff[]">-->
                    <!--    <option value="0" disabled selected>Select Staff</option>-->
                    <!--    <?php //foreach($users as $user) : ?>-->
                    <!--        <option value="<?php //echo $user['userUID']; ?>" /><?php //echo $user['fullname']; ?></option>-->
                    <!--    <?php //endforeach; ?>-->
                    <!--</select>-->
                    <input type="text" name="staff[]" placeholder="Staff Names..." />
                </div>
                <div class="form-control">
                    <input type="text" name="staff[]" placeholder="Staff Names..." />
                </div>
            </div>
        </div>
        <div class="form-actions">
            <a class="btn btn-back back-form" href="#">Back</a>
            <button class="btn btn-next" type="submit">Done</button>
        </div>
    </form>
    <form class="back-form" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=skills">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="gmpplan_id" value="<?php echo $plan_id; ?>" />
        <input type="hidden" name="general_note" value="<?php echo $general_note; ?>" />
        <input type="hidden" name="skill_ids" value="<?php echo $skill_ids; ?>" />
    </form>
</section>