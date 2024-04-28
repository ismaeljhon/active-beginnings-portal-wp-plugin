<?php
class WP_Centre_Users {
	private $centres;

	public function __construct() {
		$this->include_classes();
		if ( isset($_GET['user_action']) ) {
			if ( $_GET['user_action'] === 'sync' )
				add_action( 'acf/init', array( $this, 'sync_users') );
			
			if ( $_GET['user_action'] === 'delete' )
				add_action( 'admin_init', array( $this, 'delete_users') );
		}
  }

	private function include_classes() {
		require_once PORTAL_URI . 'includes/class-db-connection.php';
		require_once PORTAL_URI . 'admin/objects/class-centres.php';

		$db = new Database();

		$centre = new Centres($db->conn);
		$this->set_centres($centre->get_all());
	}

	private function set_centres($centres) {
		$this->centres = $centres;
	}

	public function delete_users() {
		require_once( ABSPATH.'wp-admin/includes/user.php' );
		$args = array(
			'role' => 'centre_user_role',
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

		foreach ($this->centres as $user) {
			if ( $i++ < $o )
				continue;
    		
			if ( $i > $o + $limit )
				break;

			
			$emails = explode("; ", $user['EmailAddress']);
			$email = count($emails) > 1 ? $emails[0] : $user['EmailAddress'];
			$email = email_exists($email) ? $email . '_' . $user['CentreID'] : $email;
			if ( !isset($user['EmailAddress']) || $user['EmailAddress'] == '' ) {
				$parts = explode(' ', $user['Name']);
				$email = strtolower($parts[0]);
				if ( count($parts) > 1 ) {
					$email = '';
					foreach( $parts as $part ) {
						$email .= strtolower($part);
					}
				}
				
			}
			
			$username = $email;
			$password = $email;
		
			$user_id = wp_create_user(
				$username,
				$password,
				$email
			);
		
			if ( is_wp_error($user_id) ) {
				// User creation failed, handle the error
				$error_message = $user_id->get_error_message();
				echo "Error creating user '$username': $error_message\n";
				
				continue;
			}
			
			// User created successfully
			$new_user = new WP_User($user_id);
			$new_user->set_role('centre_user_role');

			$display_name = $user['EmailAddress'] != '' ? $user['EmailAddress']: $user['Name'];
			$phone = $user['ContactPhone'] != '' ? $user['ContactPhone'] : '';
			$user_data = array(
				'ID'           => $user_id,
				'display_name' => $display_name,

			);

			wp_update_user($user_data);

			update_user_meta($user_id, 'billing_phone', $phone);
			update_field('full_name', $user['ContactName'], 'user_' . $user_id);
			update_field('user_uid', $user['centreUID'], 'user_' . $user_id);

		}
	}
}

$wp_centre_users = new WP_Centre_Users();
