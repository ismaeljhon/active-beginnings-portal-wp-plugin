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

if (isset($_POST['centre_id'])) :
    $centre_id = $_POST['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);

    $gmpplan = $gmpplan_obj->get_gmpplan_by_centre_id($centre_id);

    $selected_skills = array();
    if ($gmpplan) {
        $gmpplan_skills = $gmpplan_obj->get_skills_by_gmpplan_id($gmpplan['id']); 

        foreach($gmpplan_skills as $skill) {
            array_push($selected_skills, $skill['skillId']);
        }
    }

    $centre_report = $centre_obj->get_centre_reports($centre_id);

    $skills = array();
    foreach($centre_report as $report) {
        $slug = str_replace(' ', '_', strtolower($report['skillName']));
        $skills[$slug][] = $report;
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
                <p>Please select the areas that need improvement and click next.</p>
            </div>
        </div>
    </section>
    <section class="centre-report gmp">
        <form class="report-skills-select" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=skills">
            <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
            <input type="hidden" name="gmpplan_id" value="0" />
            <?php foreach($skills as $skill): ?>
                <div class="skill">
                    <h4>
                        <input
                            type="checkbox"
                            name="skill[]"
                            id="skill-<?php echo $skill[0]['caSkill']; ?>"
                            value="<?php echo $skill[0]['caSkill']; ?>"
                        />
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
            <div class="form-actions">
                <a class="btn btn-back" href="javascript:history.back()">Back</a>
                <button class="btn btn-next" type="submit">Next</button>
            </div>
        </form>
    </section>
<?php endif; ?>