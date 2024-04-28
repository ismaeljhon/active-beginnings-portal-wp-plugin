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

    $skill_lists = array();
    foreach($skill_obj->get_all() as $skill_list) {
        $skill_lists[$skill_list['SkillID']] = array(
            'id' => $skill_list['SkillID'],
            'name' => $skill_list['Name'],
            'desc' => $skill_list['Description']
        );
    }

    $users = array();
    foreach( $user_obj->get_all_asc() as $user) {
        $users[$user['userUID']] = $user;
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
<!DOCTYPE html>
<html>
<head>
    <title>Gross Motor Plan Report - <?php echo $centre['Name'] ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
        @page { 
            margin: 30px 20px 40px; 
        }
        header {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            height: 30px;
            margin-top: -50px;
        }
        header p {
            color: #999;
            display: block;
            padding-bottom: 10px;
            text-align: right;
            text-decoration: underline;
        }
        footer {
            position: fixed;
            left: 0px;
            right: auto;
            height: 60px;
            bottom: 30px;
            margin-bottom: -50px;
        }
        body { 
            margin: 30px 0; 
        }
        body * {
            font-family: 'Inter', sans-serif;
        }
        .container {
            max-width: 1080px !important;
            box-shadow: none !important;
        }
        .heading {
            text-align: center;
            padding-bottom: 50px;
        }
        .heading .col {
            display: inline-block;
            width: 20%;
            height: 100px;
            vertical-align: baseline;
        }
        .heading .col:nth-child(2) {
            width: 55%;
        }
        .heading h2 {
            color: #294146;
            font-size: 35px;
            font-weight: 600;
            line-height: 1em;
        }
        .heading h6 {
            color: #6DB2FF;
            font-size: 24px;
            font-weight: 400;
            line-height: 10px;
        }
        .student-assessment h6 {
            color: #6db2ff;
            font-size: 18px !important;
            font-weight: 400 !important;
            margin: 0;
        }
        .student-assessment p {
            display: block;
            text-align: center;
        }
        .centre-report .skill {
            border-bottom: 1px solid #e3e3e3;
            padding: 20px 0 30px;
        }
        .centre-report .skill .data {
            border-top: 1px solid #999;
            border-left: 1px solid #999;
            width: 550px;
        }
        .centre-report .skill .data th,
        .centre-report .skill .data td {
            border-bottom: 1px solid #999;
            border-right: 1px solid #999;
            font-size: 12px;
            padding: 2px;
        }
        .centre-report .skill .data th:not(:first-child),
        .centre-report .skill .data td:not(:first-child) {
            border-left: 1px solid #999;
            text-align: right;
        }
        .centre-report .skill td img {
            padding: 0 0 0 4px;
            vertical-align: bottom;
        }
        .centre-report .skill td img.down {
            padding: 0 4px 0 0;
            transform: rotate(180deg);
        }
        .centre-report .skill .comments {
            font-size: 16px;
        }
        .centre-report .report-graph {
            display: block;
            margin: 40px 0;
            height: 200px;
            padding-left: 20px;
            padding-bottom: 11px;
            position: relative;
        }
        .graph-stats {
            background: url('https://www.funfitkidz.com.au/site/wp-content/plugins/active-beginnings-portal/assets/img/bg-graph-pdf.jpg') no-repeat;
            background-size: cover;
            display: table;
            width: 100%;
            height: 200px;
            padding-left: 40px;
            position: relative;
            z-index: 10;
        }
        .row {
            display: table-row;
        }
        .stat {
            display: table-cell;
            height: 200px;
            position: relative;
            padding-bottom: 18px;
            vertical-align: bottom;
        }
        .stat span {
            position: absolute;
            bottom: -44px;
            left: 0;
            right: 0;
        }
        .stat .bar {
            font-size: 12px;
            background-color: #ffd480;
            color: #000;
            display: inline-block;
            margin-right: 2px;
            padding: 5px;
            vertical-align: bottom;
        }
        .stat .bar2 {
            background: #da81f6;
        }
        .stat .bar3 {
            background: #58acfa;
        }
        .graph-legends {
            text-align: center;
        }
        .graph-legends span {
            margin-right: 20px;
        }
        .graph-legends span:before {
            content: "";
            background-color: #ffd480;
            display: inline-block;
            height: 15px;
            margin-right: 10px;
            vertical-align: top;
            width: 15px;
        }
        .graph-legends span:nth-child(2):before {
            background: #da81f6;
        }
        .graph-legends span:nth-child(3):before {
            background: #58acfa;
        }
        .comments {
            background: #ddd;
            border-radius: 15px;
            padding: 5px 10px;
        }
        .comments p {
            font-size: 14px;
        } 

        .heading-note {
            background: #F6F5F0;
            border-radius: 20px;
            display: block;
            margin: 0 0 30px 0;
            padding: 30px 0 20px;
        }
        .gmp .stat .bar:first-child {
            background-color: #B5D333;
        }
        .gmp .stat .bar {
            color: #fff;
            font-weight: 600;
        }
        .gmp .skills-steps {
            overflow: hidden;
            position: relative;
        }
        .gmp .skill {
            position: relative;
        }
        .gmp .graph-legends span:first-child:before {
            background-color: #B5D333;
        }
        .gmp .skill .graph-legends span {
            display: block;
            text-align: left;
            margin-bottom: 8px;
        }
        .gmp .form-actions {
            padding: 10px 0;
            position: relative;
        }
        .gmp a.btn {
            background: #F6F5F0;
            border: 1px solid #58acfa;
            border-radius: 30px;
            color: #58acfa;
            font-weight: 600;
            padding: 12px 50px;
            text-decoration: none;
        }
        .gmp button.btn {
            background: #58acfa;
            border-radius: 30px;
            color: #fff;
            font-weight: 600;
            padding: 12px 50px;
            position: absolute;
            right: 0;
            text-decoration: none;
            top: 0;
        }

        .gmp a.expand-description {
            margin-top: 3px;
            display: inline-block;
            vertical-align: top;
        }
        .gmp .skill-description {
            display: none;
            margin-bottom: 20px;
            padding: 15px 20px;    
        }
        .gmp .skill-description.show {
            display: block;
        }
        .gmp .skill-description h5 {
            font-size: 20px;
            font-weight: 500;
        }
        .gmp .form-control {
            margin-bottom: 15px;
        }
        .gmp .skill-description p {
            font-size: 16px;
        }
        .gmp .skill-step-desc {
            background: #F6F5F0;
            border-radius: 15px;
            display: none;
            margin: 15px 0 30px 15px;
            padding: 15px 20px;
        }
        .gmp .skill-step-desc.show {
            display: block;
        }
        .gmp .skill-step-desc h6 {
            font-size: 16px;
        }
        .gmp label.emp-label {
            color: #294146 !important;
            font-size: 24px !important;
            font-weight: 600 !important;
            line-height: 28px !important;
            margin-bottom: 10px;
        }
        .gmp .skill-done {
            text-align: center;
        }

        .gmp .form-error {
            background: rgba(218, 114, 114, 0.8);
            border-radius: 12px;
            display: block;
            margin-bottom: 20px;
            padding: 15px 30px 1px;
        }
        .gmp .form-error p {
            color: #fff;
            font-size: 15px;
        }

        .gmp .heading-note p {
            text-align: center;
        }
        .gmp .form-steps {
            overflow: hidden;
            position: relative;
        }

        .gmp .skill-select .skill-actions {
            display: none;
        }
        .gmp .skill-select .skill-actions.show {
            background: #F6F5F0;
            border-radius: 12px;
            display: block;
            margin-bottom: 30px;
            padding: 30px 30px;
        }
        .gmp .student-selections .student-select {
            display: none;
        }
        .gmp .student-selections .student-select.student-selected {
            display: block;
        }
        .gmp .skill-select .skill-actions label {
            font-size: 20px !important;
        }
        .gmp .skill-select .skill-actions .skill-step-desc-show {
            margin-bottom: 30px;
            display: block;
        }
        .gmp .skill-select .skill-actions label.emp-label,
        .gmp .skill-select .skill-actions h6 {
            font-size: 16px !important;
        }
        .gmp .step-4 .skill {
            border: 1px solid #6db2ff;
            border-radius: 12px;
            margin-bottom: 30px;
            padding: 30px;
        }
        .gmp.student-assessment .container {
            max-width: 1280px !important;
        }
        .gmp .centre-heading h3 {
            color: #6db2ff;
            text-align: center;
        }
        .gmp.student-assessment form {
            position: relative;
        }
        .gmp.student-assessment form button.btn {
            position: relative;
        }
        .gmp .reports-container {
            overflow: hidden;
            position: relative;
        }
        .gmp.centre-report .report-graph {
            margin: 40px 0 60px;
        }

        .gmp .skill-actions {
            display: none;
        }
        .gmp .skill-actions.show {
            display: block;
        }
        .skill-done {
            display: block;
            text-align: center;
            margin-top: 60px;
        }
        .gmp .stat span.skill-name {
            display: inline-block;
            font-size: 13px;
            vertical-align: top;
            min-height: 50px;
            padding-right: 8px;
        }

        .wrapper-page {
            page-break-after: always;
        }

        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        </style>
</head>
<body class="student-assessment">
    <div class="container wrapper-page">
        <div class="heading">
            <div class="col">
                <img width="200" height="110" src="https://www.funfitkidz.com.au/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png" alt="funfit-logo">
            </div>
            <div class="col">
                <h6>Center Summary Report: <?php echo date('M Y') ?></h6>
                <h3 style="font-size: 38px;"><?php echo $centre['Name'] ?></h3>
            </div>
            <div class="col">
                <img width="180" height="90" src="https://staging.comworks.com.au/active/site/wp-content/uploads/main-logo.png" alt="active-logo">
            </div>
        </div>
        <div class="skill skill-done ">
            <img width="360" src="https://staging.comworks.com.au/active/site/wp-content/uploads/icon-gmp-certified.png" />
        </div>
    </div>
    <div class="content">   
        <section class="centre-report gmp">
            <form class="report-form" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=report&gmp=<?php echo $plan_id; ?>">
                <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
                <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
                <div class="reports-container">
                    <div class="report report-screen-1 animate__animated wrapper-page">
                        <div class="heading-note">
                            <p>Areas selected for improvement</p>
                        </div>
                    <?php foreach($gmp_skills as $skill): ?>
                        <div class="skill wrapper-page">
                            <h4>
                                <label style="font-size: 28px;" for="skill-<?php echo $skill[0]['caSkill']; ?>"><?php echo $skill[0]['skillName']; ?></label>
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
                    <div class="report report-screen-2 animate__animated wrapper-page">
                        <div class="heading-note">
                            <p>Skills and Actions</p>
                        </div>
                    <?php foreach ($selected_skills  as $selected_skill) : ?>
                        <?php if ($selected_skill) : ?>
                        <div class="skill skill-<?php echo $selected_skill[0]['skillID']; ?> animate__animated">
                            <h4>
                                <label style="font-size: 28px;" for="skill-<?php echo $selected_skill[0]['skillID']; ?>"><?php echo $selected_skill[0]['skillName']; ?></label>
                            </h4>
                            <div class="skill-description">
                                <h5>Description</h5>
                                <p><?php echo $selected_skill[0]['skillDesc']; ?></p>
                            </div>
                            <div class="skill-actions show wrapper-page">

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
                    <div class="report report-screen-3 animate__animated wrapper-page">
                        <div class="heading-note">
                            <p>Gross Motor Plan Notes</p>
                        </div>
                        <div class="">
                            <div class="form-control">
                                <h4><label>General Notes</label></h4>
                                <textarea name="general-note" rows="4" disabled="disabled"><?php echo $general_note != '' ? $general_note: 'No notes added in this plan'; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="report report-screen-3 animate__animated wrapper-page">
                        <div class="heading-note">
                            <p>Students Exceeding</p>
                        </div>
                        <?php foreach ($best_students as $student) : ?>
                            <div class="centre-report">
                                <div class="skill wrapper-page">
                                    <h4>
                                        <label style="font-size: 28px;"><?php echo $student[0]['student_name']; ?></label>
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
                                                    <span class="skill-name"><?php echo $skill_lists[$skill['skill_id']]['name']; ?></span>
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
                    <div class="report report-screen-4 animate__animated wrapper-page">
                        <div class="heading-note">
                            <p>Students Working Towards</p>
                        </div>
                        <?php foreach ($student_scores as $key => $student) : ?>
                            <div class="centre-report student-select student-<?php echo $student[0]['StudentID']; ?> animate__animated">
                                <div class="skill wrapper-page">
                                    <h4>
                                        <label style="font-size: 28px;"><?php echo $student[0]['FullName']; ?></label>
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
                                                    <span class="skill-name"><?php echo $skill_lists[$skill['SkillID']]['name']; ?></span>
                                                    
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
                                                <p><?php if( isset($selected_skill[$key]) ) echo $selected_skill[$key]['skillDesc']; ?></p>
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
                    <div class="report report-screen-5 animate__animated wrapper-page">
                        <div class="">
                            <strong>Meeting Attendees</strong>
                        </div>
                        <div class="skill skill-actions show">
                            <ul>
                            <?php foreach ($staffs as $staff) : ?>
                                <li><?php echo $staff; ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </div> 
                    </div>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
