<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';

$db = new Database();
$centre_obj = new Centres($db->conn);

if (isset($_POST['centre_id'])) {
    $centre_id = $_POST['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);
    $centre_id = $centre['CentreID'];
} else {
    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);
    $centre = $centre_obj->get_centre($uuid);
    $centre_id = $centre['CentreID'];
}

$centre_report = $centre_obj->get_centre_reports($centre_id);

$skills = array();
foreach($centre_report as $report) {
    $slug = str_replace(' ', '_', strtolower($report['skillName']));
    $skills[$slug][] = $report;
}

?>
<section class="student-assessment">
	<div class="container">
        <div class="heading">
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
            </div>
            <div class="col">
                <h2><?php echo $centre['Name'] ?></h2>
                <h6>Center Summary Report: <?php echo date('M Y') ?></h6>
                <p>
                    <a class="btn btn-green" target="_blank" href="/dashboard?tab=report&centre_id=<?php echo $centre_id; ?>">Download Report</a>
                </p>
            </div>
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>  
        </div>
    </div>
</section>
<section class="centre-report">
<?php foreach($skills as $skill): ?>
    <div class="skill">
        <h4><?php echo $skill[0]['skillName']; ?></h4>
        <table class="data">
            <thead>
                <tr>
                    <th>Age Bracket</th>
                    <th>Age Benchmark</th>
                    <th><?php echo date('Y') ?> Count</th>
                    <th><?php echo date('Y') ?> Average</th>
                    <th>Centre Count</th>
                    <th>Centre Average</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($skill as $skill_rep): ?>
                <tr>
                    <td data-label="Age Bracket"><?php echo $skill_rep['caAgeGrp']; ?></td>
                    <td data-label="Age Benchmark"><?php echo $skill_rep['ncScore']; ?></td>
                    <td data-label="<?php echo date('Y') ?> Count"><?php echo $skill_rep['cyCount']; ?></td>
                    <td data-label="<?php echo date('Y') ?> Average">
                    <?php
                        echo $skill_rep['cyScore']; 
                        $arrow_src = $skill_rep['cyScore'] > $skill_rep['ncScore'] ? 'assets/img/icon-arrow-up.svg' : 'assets/img/icon-arrow-down.svg';
                    ?>
                        <img width="20" src="<?php echo PORTAL_URL . $arrow_src; ?>" />
                    </td>
                    <td data-label="Centre Count"><?php echo $skill_rep['caCount']; ?></td>
                    <td data-label="Centre Average">
                    <?php
                        echo $skill_rep['caScore'];
                        $arrow_class = $skill_rep['caScore'] > $skill_rep['ncScore'] ? 'up' : 'down';
                    ?>
                        <img width="20" src="<?php echo PORTAL_URL . $arrow_src; ?>" />
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
        <div class="comments">
            <p><b>Description:</b></p>
            <p><?php echo $skill[0]['skillDescription']; ?> </p>
        </div>
    </div>
<?php endforeach; ?>
</section>