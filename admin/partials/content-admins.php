<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centre-users.php';

$db = new Database();

$users = new Users($db->conn);

echo count( $users->get_all() );

if ( isset($_GET['parent_action']) ) {
  if ( $_GET['admin_action'] === 'sync' )
	  echo '<h4>Syncing Data</h4>';
  
  if ( $_GET['admin_action'] === 'delete' )
	  echo '<h4>Deleting Data</h4>';
}

?>
<div class="container">
  <a class="button button-primary" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=admins&admin_action=sync&psync_page=0') ?>">Pull Admins data from Portal</a>
  <a class="button button-danger" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=admins&admin_action=delete') ?>">Delete data from Portal</a>
</div>
