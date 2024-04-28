<?php
class WP_Parents {
	private $parents;

	public function __construct() {
		$this->include_classes();
		if ( isset($_GET['parent_action']) ) {
			if ( $_GET['parent_action'] === 'sync' )
				add_action( 'acf/init', array( $this, 'sync_parents') );
			
			if ( $_GET['parent_action'] === 'delete' )
				add_action( 'admin_init', array( $this, 'delete_parents') );
				
			if ( $_GET['parent_action'] === 'unsynced' ) {
				add_action( 'acf/init', array( $this, 'get_unsynced') );
				//add_action( 'acf/init', array( $this, 'sync_parents') );
			}
		}
		
		add_action( 'sync_unsynced_users', array( $this, 'get_unsynced') );
    }

	private function include_classes() {
		require_once PORTAL_URI . 'includes/class-db-connection.php';
		require_once PORTAL_URI . 'admin/objects/class-parents.php';

		$db = new Database();

		$parent = new Parents($db->conn);
		$this->set_parents($parent->get_all());
	}

	private function set_parents($parents) {
		$this->parents = $parents;
	}
	
	public function get_unsynced() {
		require_once( ABSPATH.'wp-admin/includes/user.php' );
		$args = array(
			'role' => 'parent_role',
		);
		
		$users = get_users( $args );
		$usernames = [];
		foreach ( $users as $user ) {
			$usernames[] = $user->user_login;
		}
		
		$this->usernames = $usernames;
		
		require_once PORTAL_URI . 'includes/class-db-connection.php';
		require_once PORTAL_URI . 'admin/objects/class-parents.php';

		$db = new Database();

		$parent = new Parents($db->conn);
		$this->set_parents($parent->get_all_unsynced($usernames));
		
		$this->sync_parents();
	}

	public function delete_parents() {
		require_once( ABSPATH.'wp-admin/includes/user.php' );
		$args = array(
			'role' => 'parent_role',
		);
		$users = get_users( $args );
		
		foreach ($users as $user) {
			wp_delete_user($user->ID);
		}
	}

	public function sync_parents() {
		$page = isset($_GET['psync_page']) ? $_GET['psync_page'] : 0;
		$limit = 1000;
		$o = $limit * $page;
		$i = 0;

		foreach ($this->parents as $parent) {
			if ( $i++ < $o )
				continue;
    		
			if ( $i > $o + $limit )
				break;

			$emails = explode("; ", $parent['Email']);
			$cred = explode('@', $emails[0]);
			$username = $parent['username'];
			$password = $parent['username'];
		
			$user_id = wp_create_user(
				$username,
				$password,
				$emails[0]
			);
		
			if ( is_wp_error($user_id) ) {
				// User creation failed, handle the error
				$error_message = $user_id->get_error_message();
				//echo "Error creating user '$username': $error_message\n";
				
				continue;
			}
			
			// User created successfully
			$user = new WP_User($user_id);
			$user->set_role('parent_role');
			update_field('portal_uid', $parent['parentUID'], 'user_' . $user_id);
			update_field('full_name', $parent['FullName'], 'user_' . $user_id);
			
			if ( isset($emails[1]) )
				update_field('extra_email', $emails[1], 'user_' . $user_id);

			update_user_meta( $user_id, 'billing_address_1', $parent['Address1'] );
			update_user_meta( $user_id, 'billing_address_2', $parent['Suburb'] );
			update_user_meta( $user_id, 'billing_postcode', $parent['Postcode'] );
		}
	}
}

$wp_parents = new WP_Parents();
