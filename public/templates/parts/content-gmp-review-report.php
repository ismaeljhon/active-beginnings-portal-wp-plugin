<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';
require_once PORTAL_URI . 'admin/objects/class-gross-motor-plan.php';
require_once PORTAL_URI . 'admin/objects/class-skills.php';
require_once PORTAL_URI . 'admin/objects/class-centre-users.php';

$db = new Database();
$centre_obj = new Centres($db->conn);
$gmpplan_obj = new GMP_Plan($db->conn);
$skill_obj = new Skills($db->conn);
$user_obj = new Users($db->conn);

if (isset($_POST['centre_id'])) {
    $centre_id = $_POST['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);

    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);

    $plan_id = $_POST['plan_id'];
    $plan = $gmpplan_obj->get_gmpplan($plan_id);
    $staffs = explode(', ', $plan['staffNames']);
    $general_note = $plan['notes'];

    $gmp_skills = $gmpplan_obj->get_skills_by_gmpplan_id($plan_id);
    $centre_report = $centre_obj->get_centre_reports($centre_id);

    $skills = array();
    $gmpplanskill_ids = array();
    $gmpplanskillsteps_ids = array();
    $gmp_selected_skills = array();

    $users = array();
    foreach( $user_obj->get_all_asc() as $user) {
        $users[$user['userUID']] = $user;
    }

    $skill_lists = array();
    foreach($skill_obj->get_all() as $skill_list) {
        $skill_lists[$skill_list['SkillID']] = array(
            'id' => $skill_list['SkillID'],
            'name' => $skill_list['Name'],
            'desc' => $skill_list['Description']
        );
    }

    foreach ($gmp_skills as $skill) {
        $skills[$skill['skillId']] = $skill['skillId'];
        $gmpplanskill_ids[$skill['skillId']] = $skill['id'];
    }

    $gmp_skills = array();
    foreach ($centre_report as $current_skill) {
        if ( in_array($current_skill['caSkill'], $skills) )  {
            $gmp_skills[$current_skill['caSkill']][] = $current_skill;
        }       
    }

    $gmpplanskillsteps = array();
    foreach($gmpplanskill_ids as $key => $skill_id) {
        $gmpplanskillsteps[$key] = $gmpplan_obj->get_skillplans_by_id($skill_id);
    }

    $selected_skills = array();
    foreach ($skills as $skill_id) {
        $skill_plans = $gmpplan_obj->get_skill_plans($centre_id, $skill_id);
        $plan = array();
        foreach ( $skill_plans as $skill_plan ) {
            $plan[] = $skill_plan;
        }
        $selected_skills[$skill_id] = $plan;
    }
    
    foreach ($gmpplanskillsteps as $key => $gmpplanskillstep) {
        foreach($gmpplanskillstep as $step) {
            $gmpplanskillsteps_ids[] = $step['skilIStepId'];
        }
    }
    
    $studentplans = $gmpplan_obj->get_studentplan_by_plan_id($plan_id);

    $studentplans_ids = array();
    $studentplans_selected = array();
    $studentplans_selected_ids = array();
    $student_scores = array();
    $skill_plans = array();
    foreach ($studentplans as $student) {
        $studentplans_ids[] = $student['studentId'];
        $studentplans_selected_ids[$student['studentId']][] = $student['skillId'];

        $student_scores[$student['studentId']] = $gmpplan_obj->get_student_scores($student['studentId']);
        $skill_plans[$student['skillId']] = $gmpplan_obj->get_student_skill_plans($student['studentId'], $student['skillId']);
        $studentplans_selected[$student['studentId']][] = array(
            'id' => $student['studentId'],
            'skill_id' => $student['skillId'],
            'staff' => $student['staffNames'],
            'notes' => $student['notes'],
        );
    }

    $best_students = array();
    $best_students_ids = $gmpplan_obj->get_best_students($centre_id);
    if ( $best_students_ids ) {
        $best_student_scores = array();
        foreach ($best_students_ids as $best_student) {
            $best_student_scores = array_merge($best_student_scores, $gmpplan_obj->get_student_scores($best_student['studentId']));
        }

        foreach ($best_student_scores as $scores) {
            //if ( in_array($scores['SkillID'], $skills)) {
                $best_students[$scores['StudentID']][] = array(
                    'student_id' => $scores['StudentID'],
                    'student_name' => $scores['FullName'],
                    'skill_id' => $scores['SkillID'],
                    'student_score' => $scores['studentScore'],
                    'centre_avg' =>$scores['cntrAvg'],
                    'all_avg' => $scores['allCentrAvg']
                );
            //}

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
    <form class="report-form" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=report&gmp=<?php echo $plan_id; ?>">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
        <div class="reports-container">
            <div class="report report-screen-1 animate__animated">
                <div class="heading-note">
                    <p>Areas selected for improvement</p>
                </div>
            <?php foreach($gmp_skills as $skill): ?>
                <div class="skill">
                    <h4>
                        <label for="skill-<?php echo $skill[0]['caSkill']; ?>"><?php echo $skill[0]['skillName']; ?></label>
                    </h4>
                    <h6>Centre Comparisons vs Overall Age Group</h6>
                    <div class="report-graph">
                        <div class="graph-bg">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="graph-stats">
                            <div class="row">
                            <?php foreach($skill as $skill_rep): ?>
                                <div class="stat">
                                    <div class="bar bar1" style="height:<?php echo $skill_rep['ncScore']; ?>%;" >
                                        <?php echo $skill_rep['ncScore']; ?>
                                    </div>
                                    <div class="bar bar2" style="height:<?php echo $skill_rep['cyScore']; ?>%;" >
                                        <?php echo $skill_rep['cyScore']; ?>
                                    </div>
                                    <div class="bar bar3" style="height:<?php echo $skill_rep['caScore']; ?>%;" >
                                        <?php echo $skill_rep['caScore']; ?>
                                    </div>
                                    <span><?php echo $skill_rep['caAgeGrp']; ?></span>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="graph-legends">
                        <p>
                            <span>Age Benchmark</span>
                            <span><?php echo date('Y') ?> Center Average</span>
                            <span>Center Average</span>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <div class="report report-screen-2 animate__animated">
                <div class="heading-note">
                    <p>Skills and Actions</p>
                </div>
            <?php foreach ($selected_skills  as $selected_skill) : ?>
                <?php if ($selected_skill) : ?>
                <div class="skill skill-<?php echo $selected_skill[0]['skillID']; ?> animate__animated">
                    <h4>
                        <label for="skill-<?php echo $selected_skill[0]['skillID']; ?>"><?php echo $selected_skill[0]['skillName']; ?></label>
                    </h4>
                    <div class="skill-description">
                        <h5>Description</h5>
                        <p><?php echo $selected_skill[0]['skillDesc']; ?></p>
                    </div>
                    <div class="skill-actions">

                        <?php foreach ($selected_skill as $skill_step) : ?>

                        <div class="form-control">
                            <label for="skill-step-<?php echo $skill_step['skillStepId']; ?>"><?php echo $skill_step['rptDescription']; ?></label>
                            <div class="skill-step-desc <?php if ( in_array($skill_step['skillStepId'], $gmpplanskillsteps_ids) ) echo "show" ?>">
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
                            <textarea placeholder="Notes..." name="skills[<?php echo $selected_skill[0]['skillID']; ?>][note]" disabled><?php echo $gmpplanskillsteps[$selected_skill[0]['skillID']][0]['notes']; ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
            <div class="report report-screen-3 animate__animated">
                <div class="heading-note">
                    <p>Gross Motor Plan Notes</p>
                </div>
                <div class="skill skill-actions">
                    <div class="form-control">
                        <h4><label>General Notes</label></h4>
                        <textarea name="general-note" rows="4" disabled="disabled"><?php echo $general_note != '' ? $general_note: 'No notes added in this plan'; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="report report-screen-4 animate__animated">
                <div class="heading-note">
                    <p>Students Exceeding</p>
                </div>
                <?php foreach ($best_students as $student) : ?>
                    <div class="centre-report">
                        <div class="skill">
                            <h4>
                                <label><?php echo $student[0]['student_name']; ?></label>
                            </h4>
                            <div class="report-graph">
                                <div class="graph-bg">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="graph-stats">
                                    <div class="row">
                                    <?php foreach($student as $skill): ?>
                                        <div class="stat">
                                            <div class="bar bar1" style="height:<?php echo $skill['student_score']; ?>%;" >
                                                <?php echo $skill['student_score']; ?>
                                            </div>
                                            <div class="bar bar2" style="height:<?php echo $skill['centre_avg']; ?>%;" >
                                                <?php echo $skill['centre_avg']; ?>
                                            </div>
                                            <div class="bar bar3" style="height:<?php echo $skill['all_avg']; ?>%;" >
                                                <?php echo $skill['all_avg']; ?>
                                            </div>
                                            <span><?php echo $skill_lists[$skill['skill_id']]['name']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="graph-legends">
                                <p>
                                    <span><?php echo $student[0]['student_name']; ?></span>
                                    <span><?php echo date('Y') ?> Student Average</span>
                                    <span>All Time Average</span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="report report-screen-5 animate__animated">
                <div class="heading-note">
                    <p>Students Working Towards</p>
                </div>
                <?php foreach ($student_scores as $key => $student) : ?>
                    <div class="centre-report student-select student-<?php echo $student[0]['StudentID']; ?> animate__animated">
                        <div class="skill">
                            <h4>
                                <label><?php echo $student[0]['FullName']; ?></label>
                            </h4>
                            <div class="report-graph">
                                <div class="graph-bg">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="graph-stats">
                                    <div class="row">
                                    <?php foreach($student as $skill): ?>
                                        <div class="stat">
                                            <div class="bar bar1" style="height:<?php echo $skill['studentScore']; ?>%;" >
                                                <?php echo $skill['studentScore']; ?>
                                            </div>
                                            <div class="bar bar2" style="height:<?php echo $skill['cntrAvg']; ?>%;" >
                                                <?php echo $skill['cntrAvg']; ?>
                                            </div>
                                            <div class="bar bar3" style="height:<?php echo $skill['allCentrAvg']; ?>%;" >
                                                <?php echo $skill['allCentrAvg']; ?>
                                            </div>
                                            <span><?php echo $skill_lists[$skill['SkillID']]['name']; ?></span>
                                            
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="graph-legends">
                                <p>
                                    <span><?php echo $student[0]['FullName']; ?></span>
                                    <span><?php echo date('Y') ?> Student Average</span>
                                    <span>All Time Average</span>
                                </p>
                            </div>
                            <div class="skill-selection">
                            <?php foreach ($skill_plans  as $selected_skill) : ?>
                                <?php if ($selected_skill) : ?>
                                <div class="skill-select">
                                    <h4>
                                        <label for="student-<?php echo $student[0]['StudentID']; ?>-skill-<?php echo $selected_skill[0]['skillID']; ?>"><?php echo $selected_skill[0]['skillName']; ?></label>
                                    </h4>
                                    <div class="skill-description">
                                        <h5>Description</h5>
                                        <p><?php echo $selected_skill[$key]['skillDesc']; ?></p>
                                    </div>
                                    <div class="skill-actions student-<?php echo $student[0]['StudentID']; ?>-skill-<?php echo $selected_skill[0]['skillID']; ?>  <?php if ( in_array( $selected_skill[0]['skillID'], $studentplans_selected_ids[$student[0]['StudentID']]) ) echo "show" ?>">

                                        <?php foreach ($selected_skill as $skill_step) : ?>

                                        <div class="form-control">
                                            <label for="skill-step-<?php echo $skill_step['skillStepId']; ?>"><?php echo $skill_step['rptDescription']; ?></label>
                                            <div class="skill-step-desc show">
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
                                            <textarea placeholder="Notes..." name="student[<?php echo $student[$key]['student_id']; ?>][skill][<?php echo $selected_skill[0]['skillID']; ?>][note]" disabled><?php echo $studentplans_selected[$student[0]['StudentID']][0]['notes']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="report report-screen-6 animate__animated">
                <div class="heading-note">
                    <p>Responsible Staff Assigned</p>
                </div>
                <div class="skill skill-actions">
                    <?php foreach ($staffs as $staff) : ?>
                    <div class="form-control">
                        <input type="text" name="staff[]" placeholder="Staff Name..." value="<?php echo $staff; ?>" disabled="disabled" />
                    </div>
                    <?php endforeach; ?>
                </div> 
            </div>
            <div class="report report-screen-7 animate__animated">
                <div class="skill skill-done">
                    <img class="" width="300" src="<?php echo PORTAL_URL . 'assets/img/'; ?>icon-gmp-certified.svg" />
                </div>
            </div>
        </div>
        <div class="form-actions">
            <a class="btn btn-back" href="#">Back</a>
            <button class="btn btn-next" type="button">Next</button>
        </div>
    </form>
</section>