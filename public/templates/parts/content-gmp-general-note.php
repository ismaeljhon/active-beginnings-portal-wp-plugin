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

    $selected_skills = $_POST['skills'];
    $skill_ids = '';

    $users = array();
    foreach( $user_obj->get_all_asc() as $user) {
        $users[] = $user;
    } 

    foreach( $selected_skills as $key => $skill ) {
        foreach( $selected_skills[$key]['skill_step_id'] as $step_id ) {
            $gmpplan_obj->delete_skillstep_by_skill($selected_skills[$key]['gmpplanskill_id']);
            $gmpplanskillstep_id = $gmpplan_obj->insert_plan_skill_step(
                $selected_skills[$key]['gmpplanskill_id'],
                $step_id,
                $selected_skills[$key]['note']
            );
        }

        $skill_ids .= $selected_skills[$key]['skill_id'] . ',';
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
            <p>Gross Motor Plan Notes</p>
        </div>
    </div>
</section>
<section class="centre-report gmp assign-staff">
    <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=assign">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
        <input type="hidden" name="skill_ids" value="<?php echo substr_replace($skill_ids, '', -1); ?>" />
        <div class="skill">
            <h4>
                <label for="skill-0">General Notes</label>
            </h4>
            <div class="skill-actions">
                <div class="form-control">
                    <textarea name="general-note" placeholder="Add notes here..." rows="4"></textarea>
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
        <?php foreach ($selected_skills as $key => $skill) : ?>
            <input type="hidden" name="skill[]" value="<?php echo $key; ?>" />
        <?php endforeach; ?>
    </form>
</section>