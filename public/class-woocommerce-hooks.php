<?php
class Comworks_Woocommerce_Hooks {
    public function __construct() {
        add_filter('woocommerce_locate_template', array( $this, 'wc_template_override' ), 10, 3);
        add_action('woocommerce_save_account_details', array( $this, 'update_user_meta_on_profile_update' ), 10, 1);
		add_action('template_redirect', array( $this, 'woocommerce_account_redirect' ) );
    }

    function wc_template_override($template, $template_name, $template_path) {
        // Check if the template being requested is 'product-archive.php'
        if ($template_name == 'myaccount/form-edit-account.php') {  
            // Set the new template file path within your plugin directory
            $new_template = PORTAL_URI . 'public/templates/woocommerce/my-account/form-edit-account.php';
    
            // Check if the new template file exists
            if (file_exists($new_template)) {
                return $new_template;
            }
        }
    
        return $template;
    }

    function update_user_meta_on_profile_update($user_id) {
        // Get the submitted form data
        $first_name = isset($_POST['account_first_name']) ? sanitize_text_field($_POST['account_first_name']) : '';
        $last_name = isset($_POST['account_last_name']) ? sanitize_text_field($_POST['account_last_name']) : '';
        $email = isset($_POST['account_email']) ? sanitize_text_field($_POST['account_email']) : '';
        
        
        // Update user meta based on the form data
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'first_login', '1');

        $user_data = get_userdata($user_id);
        $user_data->display_name = $first_name . ' ' . $last_name;
        wp_update_user($user_data);

        //if (isset($_POST['account_username']) && $_POST['account_username'] != '') {
            //$this->update_username_by_id($user_id, $_POST['account_username']);
            //$this->login_user_by_username($_POST['account_username']);
        //}
        
        wp_safe_redirect( home_url() . '/dashboard/?tab=dashboard' ); 
        exit;
    }

    function update_username_by_id($user_id, $new_username) {
        global $wpdb;
    
        // Update the wp_users table
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}users SET user_login = %s WHERE ID = %d",
                $new_username,
                $user_id
            )
        );
    
        // Update the wp_posts table
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}posts SET post_author = %d WHERE post_author = %d",
                $user_id,
                $user_id
            )
        );
    
        echo 'Username updated successfully!';
    }

    function login_user_by_username($username) {
        // Get the user data by username
        $user = get_user_by('login', $username);
    
        if ($user) {
            // Generate login session for the user
            clean_user_cache($user->ID);
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true, false);
            update_user_caches($user);
            
            wp_safe_redirect( home_url() . '/dashboard/?tab=dashboard' );
            // Successful login
            echo 'User logged in successfully!';
        } else {
            // User not found
            echo 'User not found.';
        }
    }
	
    function woocommerce_account_redirect() {
        if ( is_user_logged_in() && is_account_page() ) {
            wp_redirect('/dashboard'); // Replace with the URL you want to redirect to
            exit;
        }
    }

}
new Comworks_Woocommerce_Hooks();