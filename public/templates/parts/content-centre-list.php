<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';

$db = new Database();
$centre_obj = new Centres($db->conn);
$centres = $centre_obj->get_all();


?>
<section class="dashboard centre-students student-assessment">
    <div class="container">
        <div class="heading">
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="funfit-logo" />
            </div>
            <div class="col">
                <h2>Centres</h2>
                <p>Select Centre to View Report</p>
            </div>
            <div class="col">
                <img width="200" height="100" src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="active-logo" />
            </div>  
        </div>
        <div class="form-control">
            <form method="post" action="/dashboard/?tab=centre-report">
                <label for="select_centres">Select Centre:</label>
                <select id="centre_id" name="centre_id" >
                    <option value="all" selected>All Centres</option>
                <?php foreach( $centres as $centre ) : ?>
                    <option value="<?php echo $centre['CentreID']; ?>">
                        <?php echo $centre['Name']; ?>
                    </option>
                <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-blue" href="#">Get Report</button>
            </form>
        </div>
    </div>
</section>
