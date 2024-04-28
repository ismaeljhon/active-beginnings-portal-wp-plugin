<?php
class WP_AB_Admin {
	private $users;

	public function __construct() {
		$this->include_classes();
		if ( isset($_GET['admin_action']) ) {
			if ( $_GET['admin_action'] === 'sync' )
				add_action( 'acf/init', array( $this, 'sync_users') );
			
			if ( $_GET['admin_action'] === 'delete' )
				add_action( 'admin_init', array( $this, 'delete_users') );
		}
  }

	private function include_classes() {
		require_once PORTAL_URI . 'includes/class-db-connection.php';
		require_once PORTAL_URI . 'admin/objects/class-centre-users.php';

		$db = new Database();

		$user = new Users($db->conn);
		$this->set_users($user->get_all());
	}

	private function set_users($users) {
		$this->users = $users;
	}

	public function delete_users() {
		require_once( ABSPATH.'wp-admin/includes/user.php' );
		$args = array(
			'role' => 'ab_admin',
		);
		$users = get_users( $args );
		
		foreach ($users as $user) {
			wp_delete_user($user->ID);
		}
	}

	public function sync_users() {
		$page = isset($_GET['usync_page']) ? $_GET['usync_page'] : 0;
		$limit = 1000;
		$o = $limit * $page;
		$i = 0;

		foreach ($this->users as $user) {
			if ( $i++ < $o )
				continue;
    		
			if ( $i > $o + $limit )
				break;

			$email = $user['username'] . '@funfitkidz.com.au';
			$username = $user['username'];
			$password = $user['username'];
		
			$user_id = wp_create_user(
				$username,
				$password,
				$email,
			);
		
			if ( is_wp_error($user_id) ) {
				// User creation failed, handle the error
				$error_message = $user_id->get_error_message();
				//echo "Error creating user '$username': $error_message\n";
				
				continue;
			}
			
			// User created successfully
			$new_user = new WP_User($user_id);
			$new_user->set_role('ab_admin');
			update_field('full_name', $user['fullname'], 'user_' . $user_id);
			update_field('user_uid', $user['userUID'], 'user_' . $user_id);

		}
	}
}

$wp_ad_admin = new WP_AB_Admin();
