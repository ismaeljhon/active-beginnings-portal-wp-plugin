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
    //$skills = explode(",", $_POST['skill_ids']);
    $plan = $gmpplan_obj->get_gmpplan($plan_id);
    $staffs = explode(', ', $plan['staffNames']);

    $users = array();
    foreach( $user_obj->get_all_asc() as $user) {
        $users[] = $user;
    }

    $skill_lists = array();
    $skill_plans = array();
    foreach($skill_obj->get_all() as $skill_list) {
        $skill_lists[$skill_list['SkillID']] = array(
            'id' => $skill_list['SkillID'],
            'name' => $skill_list['Name'],
            'desc' => $skill_list['Description']
        );
    }

    $best_students = array();
    $best_students_ids = $gmpplan_obj->get_best_students($centre_id);
    if ( $best_students_ids ) {
        $student_scores = array();
        $average_scores = array();
        foreach ($best_students_ids as $best_student) {
            $student_scores = array_merge($student_scores, $gmpplan_obj->get_student_scores($best_student['studentId']));
        }

        foreach ($student_scores as $scores) {
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

    $worst_students = array();
    $worst_students_ids = $gmpplan_obj->get_worst_students($centre_id);
    if ( $worst_students_ids ) {
        $student_scores = array();
        $average_scores = array();
        foreach ($worst_students_ids as $worst_student) {
            $student_scores = array_merge($student_scores, $gmpplan_obj->get_student_scores($worst_student['studentId']));
        }

        foreach ($student_scores as $scores) {
            //if ( in_array($scores['SkillID'], $skills)) {
                $worst_students[$scores['StudentID']][] = array(
                    'student_id' => $scores['StudentID'],
                    'student_name' => $scores['FullName'],
                    'skill_id' => $scores['SkillID'],
                    'student_score' => $scores['studentScore'],
                    'centre_avg' =>$scores['cntrAvg'],
                    'all_avg' => $scores['allCentrAvg']
                );
            //}

            $skill_plans[$scores['StudentID']][$scores['SkillID']] = $gmpplan_obj->get_student_skill_plans($scores['StudentID'], $scores['SkillID']);

        }
    }

?>
<!-- <pre>
<?php //print_r($skill_plans); ?>
<?php //print_r($worst_students); ?>
</pre> -->
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
                <img width="300" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>  
        </div>
    </div>
</section>
<section class="student student-select gmp">
    <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=student-done">
        <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
        <input type="hidden" name="gmpplan_id" value="<?php echo $plan_id; ?>" />
        <div class="form-steps">
            <div class="form-step step-1 animate__animated">
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
            <div class="form-step step-2 animate__animated">
                <div class="heading-note">
                    <p>Students Working Towards</p>
                </div>
                <?php foreach ($worst_students as $student) : ?>
                    <div class="centre-report">
                        <div class="skill">
                            <h4>
                                <input 
                                    type="checkbox"
                                    name="student[<?php echo $student[0]['student_id']; ?>][id]"
                                    value="<?php echo $student[0]['student_id']; ?>"
                                    id="student-<?php echo $student[0]['student_id']; ?>"
                                />
                                <label for="student-<?php echo $student[0]['student_id']; ?>"><?php echo $student[0]['student_name']; ?></label>
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
            <div class="form-step step-3 animate__animated">
                <div class="heading-note">
                    <p>Students Working Towards</p>
                </div>
                <div class="student-selections">
                <?php foreach ($worst_students as $student) : ?>
                    <div class="centre-report student-select student-<?php echo $student[0]['student_id']; ?> animate__animated">
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
                            <div class="skill-selection">
                            <?php foreach ($skill_plans[$student[0]['student_id']]  as $selected_skill) : ?>
                                <?php if ($selected_skill) : ?>
                                <div class="skill-select">
                                    <h4>
                                    <input 
                                        type="checkbox"
                                        class="student-skill-select"
                                        name="student[<?php echo $student[0]['student_id']; ?>][skill][<?php echo $selected_skill[0]['skillID']; ?>][id]"
                                        value="<?php echo $selected_skill[0]['skillID']; ?>"
                                        id="student-<?php echo $student[0]['student_id']; ?>-skill-<?php echo $selected_skill[0]['skillID']; ?>"
                                    />
                                        <label for="student-<?php echo $student[0]['student_id']; ?>-skill-<?php echo $selected_skill[0]['skillID']; ?>"><?php echo $selected_skill[0]['skillName']; ?></label>
                                    </h4>
                                    <div class="skill-description">
                                        <h5>Description</h5>
                                        <p><?php echo $selected_skill[0]['skillDesc']; ?></p>
                                    </div>
                                    <div class="skill-actions student-<?php echo $student[0]['student_id']; ?>-skill-<?php echo $selected_skill[0]['skillID']; ?>">

                                        <?php foreach ($selected_skill as $skill_step) : ?>

                                        <div class="form-control">
                                            <label for="skill-step-<?php echo $skill_step['skillStepId']; ?>"><?php echo $skill_step['rptDescription']; ?></label>
                                            <div class="skill-step-desc-show">
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
                                            <textarea placeholder="Notes..." name="student[<?php echo $student[0]['student_id']; ?>][skill][<?php echo $selected_skill[0]['skillID']; ?>][note]"></textarea>
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
            </div>
            <div class="form-step step-4 animate__animated">
                <div class="heading-note">
                    <p>Meeting Attendees</p>
                </div>
                <div class="skill skill-actions">
                    <?php foreach ($staffs as $staff) : ?>
                    <div class="form-control">
                        <input type="text" name="staff[]" placeholder="Staff Name..." value="<?php echo $staff; ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>                           
            </div>
        </div>
        <div class="form-actions">
            <a class="btn btn-back" href="javascript:history.back()">Back</a>
            <button class="btn btn-next" type="button">Next</button>
        </div>
    </form>
</section>
<?php } ?>