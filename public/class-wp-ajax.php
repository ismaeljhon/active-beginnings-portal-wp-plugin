<?php
class Comworks_WP_AJAX {

    public function __construct() {
        add_action('wp_ajax_check_username', array( $this, 'check_username_exists') );
        add_action('wp_ajax_nopriv_check_username', array( $this, 'check_username_exists') );
    }

    function check_username_exists() {
        
        $username = $_POST['account_username'];
        $current_user_id = $_POST['current_user_id'];

        $user = get_user_by('login', $username);

        if ($user && $user->ID != $current_user_id) {
            wp_send_json_success(array('exist' => true)); // Username exists
        }
            
        wp_send_json_success(array('exist' => false)); // Username does not exist
    }

}

$wp_ajax = new Comworks_WP_AJAX();
