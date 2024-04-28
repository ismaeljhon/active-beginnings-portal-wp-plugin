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

    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);

    $gmp_plans = $gmpplan_obj->get_all_by_centre($centre_id);

?>
    <section class="student-assessment gmp">
        <div class="container">
            <div class="heading">
                <div class="col">
                    <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
                </div>
                <div class="col">
                    <h6>GMP Reports</h6>
                    <h2><?php echo $centre['Name'] ?></h2>
                </div>
                <div class="col">
                    <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
                </div>  
            </div>
        </div>
    </section>
    <section class="centre-report gmp">
        <table class="gmp-table">
            <thead>
                <tr>
                    <th style="max-width: 300px">Date</th>
                    <th>User</th>
                    <!-- <th>Staffs</th> -->
                    <th>Actions</th>
                </tr>    
            </thead>
            <tbody>
            <?php foreach($gmp_plans as $plan) : ?>
                <tr>
                    <td><?php if (isset($plan['dateAdded']) ) echo date('F j, Y G:i:s', strtotime($plan['dateAdded'])); ?></td>
                    <td><?php echo $plan['fullname']; ?></td>
                    <!-- <td><?php echo $plan['staff']; ?></td> -->
                    <td>
                        <form class="pdf-report-form" target="_blank" method="post" action="<?php echo home_url(); ?>/dashboard/?tab=report&gmp=<?php echo $plan['id']; ?>">
                            <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>" />
                            <button type="submit">PDF</button>
                        </form>
                        <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gross-motor-report&action=review">
                            <input type="hidden" name="centre_id" value="<?php echo $centre_id; ?>" />
                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>" />
                            <button type="submit">View</button>
                        </form>
                    </td>
                </tr>    
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php else: ?>
<?php $centres = $centre_obj->get_all(); ?>
    <section class="dashboard centre-students student-assessment gmp">
        <div class="container">
            <div class="heading">
                <div class="col">
                    <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
                </div>
                <div class="col">
                    <h2>Our Centres</h2>
                </div>
                <div class="col">
                    <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
                </div>  
            </div>
            <div class="form-control">
                <div class="centre-heading">
                    <h3>Gross Motor Program Summary Report <?php echo date("F Y") . "<br>" ?></h3>
                </div>
                <form method="post" action="<?php echo home_url(); ?>/dashboard/?tab=gmp-plans">
                    <label for="select_centres">Select Centre:</label>
                    <select id="centre_id" name="centre_id" >
                        <option value="all" selected>All Centres</option>
                    <?php foreach( $centres as $centre ) : ?>
                        <option value="<?php echo $centre['CentreID']; ?>">
                            <?php echo $centre['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-blue" href="#">Next</button>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>