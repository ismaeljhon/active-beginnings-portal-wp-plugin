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

    $plan_id = $_POST['gmpplan_id'];
    if ( $plan_id == '0' )
        $plan_id = $gmpplan_obj->create_gmpplan($uuid, $centre_id);

    $selected_skills = array();

    foreach ($_POST['skill'] as $skill_id) {
        $gmpplanskill_id = $gmpplan_obj->insert_plan_skill($plan_id, $skill_id);
        $skill_plans = $gmpplan_obj->get_skill_plans($centre_id, $skill_id);
        $plan = array();
        foreach ( $skill_plans as $skill_plan ) {
            $plan[] = $skill_plan + array('gmpplanskill_id' => $gmpplanskill_id);
        }
        $selected_skills[] = $plan;
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
            <p>Gross Motor Program Planner - Skills Selected <span class="page">1</span> of <?php echo count($selected_skills); ?></p>  </p>
        </div>
    </div>
</section>
<section class="centre-report gmp">
    <form class="skills-form" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=general-note">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
        <div class="skills-steps">

        <?php foreach ($selected_skills  as $selected_skill) : ?>
            <?php if ($selected_skill) : ?>
            <div class="skill skill-<?php echo $selected_skill[0]['skillID']; ?> animate__animated">
                <h4>
                    <label for="skill-<?php echo $selected_skill[0]['skillID']; ?>"><?php echo $selected_skill[0]['skillName']; ?></label>
                    <a class="expand-description" href="#"><img width="30" src="<?php echo PORTAL_URL . 'assets/img/'; ?>icon-burger.svg" /></a>
                </h4>
                <div class="skill-description">
                    <h5>Description</h5>
                    <p><?php echo $selected_skill[0]['skillDesc']; ?></p>
                </div>
                <div class="skill-actions">

                    <?php foreach ($selected_skill as $skill_step) : ?>

                    <div class="form-control">
                        <input type="hidden" name="skills[<?php echo $skill_step['skillID']; ?>][gmpplanskill_id]" value="<?php echo $selected_skill[0]['gmpplanskill_id']; ?>" />
                        <input type="hidden" name="skills[<?php echo $skill_step['skillID']; ?>][skill_id]" value="<?php echo $skill_step['skillID']; ?>" />
                        <input 
                            type="checkbox"
                            name="skills[<?php echo $skill_step['skillID']; ?>][skill_step_id][]"
                            value="<?php echo $skill_step['skillStepId']; ?>"
                            id="skill-step-<?php echo $skill_step['skillStepId']; ?>"
                            class="checkbox-skill-step"
                        />

                        <label for="skill-step-<?php echo $skill_step['skillStepId']; ?>"><?php echo $skill_step['rptDescription']; ?></label>
                        <div class="skill-step-desc">
                            <h6>Actionable Advice</h6>
                            <ul>
                                <li><?php echo $skill_step['rptActionAdvice']; ?></li>
                            </ul>
                            <br/>
                            <h6>Improvement Technique</h6>
                            <ul>
                                <li><?php echo $skill_step['rptHomeIdeas']; ?></li>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="form-control">
                        <label class="emp-label">Notes</label>
                        <textarea placeholder="Notes..." name="skills[<?php echo $selected_skill[0]['skillID']; ?>][note]"></textarea>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
        <div class="form-actions">
            <a class="btn btn-back" href="#">Back</a>
            <button class="btn btn-next" type="button">Next</button>
        </div>
    </form>
    <form class="back-form" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report">
        <input type="hidden" id="centre_id" name="centre_id" value="<?php echo $centre_id; ?>" />
    </form>
</section>