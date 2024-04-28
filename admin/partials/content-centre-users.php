<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';

$db = new Database();

$centres = new Centres($db->conn);

echo count( $centres->get_all() );

if ( isset($_GET['parent_action']) ) {
  if ( $_GET['parent_action'] === 'sync' )
	  echo '<h4>Syncing Data</h4>';
  
  if ( $_GET['parent_action'] === 'delete' )
	  echo '<h4>Deleting Data</h4>';
}

?>
<div class="container">
  <a class="button button-primary" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=users&user_action=sync&psync_page=0') ?>">Pull Centre Users data from Portal</a>
  <a class="button button-danger" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=users&user_action=delete') ?>">Delete data from Portal</a>
</div>
