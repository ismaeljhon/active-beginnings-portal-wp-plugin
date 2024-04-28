<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-assessments.php';

$db = new Database();
$assess_obj = new Assessments($db->conn);

$student_id = $_GET['student_id'] ?? null; 

if ($student_id) :
    $assessments = $assess_obj->get_assessment_repot($student_id);
?>
<section class="student-assessment">
    <div class="container">
        <div class="heading">
            <div class="col">
                <img width="180" height="90" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
            </div>
            <div class="col">
                <h6>Report Card For</h6>
                <h2><?php echo $assessments[0]['FullName']; ?></h2>
                <p>
                    <a class="btn btn-green" target="_blank" href="/dashboard?tab=report&student_id=<?php echo $student_id; ?>">Download Report</a>
                </p>
            </div>
            <div class="col">
                <img width="180" height="90" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="greetings">
                    <p>Hi, from the team at Active Beginnings!</p>
                    <p><?php echo $assessments[0]['FullName']; ?> has participated in our program and we have assessed them in the following areas:</p>
                </div>
                <?php foreach ($assessments as $assessment) : ?>
                    <div class="assessment">
                        <h3><?php echo $assessment['skillName'] ?></h3>
                        <?php $date = date_create($assessment['tmStamp']); ?>
                        <p class="date">Assessment Date: <span><?php echo date_format($date, "F j, Y"); ?></span></p>
                        <div class="score">
                            <div class="col">
                                <p>Your Score <span class="score"><?php echo $assessment['stScore'] ?></span></p>
                            </div>
                            <div class="col">
                                <p>
                                    <span class="score avg"><?php echo round($assessment['ageScore'], 2) ?></span>
                                    <data>
                                        Age Benchmark
                                        <span>(<?php echo $assessment['ageGrp'] ?>) Age Bracket</span>
                                    </data>
                                </p>
                            </div>
                        </div>
                        <div class="score-graph">
                            <div class="col">
                                <p>Scores</p>
                            </div>
                            <div class="col">
                                <div class="graph-container">
                                    <p>Assessment Comparison vs Age Group</p>
                                    <div class="graph">
                                        <div class="bar bar-pink" style="width: <?php echo $assessment['stScore'] ?>%;">Your Score</div>
                                        <div class="bar bar-blue"  style="width: <?php echo round($assessment['ageScore'], 2) ?>%;">Age Benchmark</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="skill-description">
							<h6>Description:</h6>
							<p><?php echo $assessment['skDescription']; ?></p>
						</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php
endif;