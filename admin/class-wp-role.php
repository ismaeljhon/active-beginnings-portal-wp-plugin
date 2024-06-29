<?php
class Comworks_User_Role {

    public function __construct() {
        add_action( 'init', array( $this, 'add_roles' ) );
        add_action( 'init', array( $this, 'hide_admin_bar' ) );
        add_action( 'profile_update', array( $this, 'password_change_action' ) );

    }
    
    // Add a new user role
    public function add_roles() {
        // Set role capabilities
        $capabilities = array(
            'read'         => true,
            'edit_posts'   => true,
            'upload_files' => false,
        );
    
        // Add the role
        add_role( 'parent_role', 'Parent', $capabilities );

        // Set role capabilities
        $capabilities = array(
            'read'         => true,
            'edit_posts'   => true,
            'upload_files' => false,
        );
    
        // Add the role
        add_role( 'parent_pending_role', 'Parent - pending', $capabilities );

        // Set role capabilities
        $capabilities = array(
            'edit_posts' => true,
            'delete_posts' => true,
            'edit_pages' => true,
            'delete_pages' => true,
            'manage_categories' => true,
        );
    
        // Add the role
        add_role( 'centre_user_role', 'Centre User', $capabilities );

        // Set role capabilities
        $capabilities = array(
            'edit_posts' => true,
            'delete_posts' => true,
            'edit_pages' => true,
            'delete_pages' => true,
            'manage_categories' => true,
        );
    
        // Add the role
        add_role( 'ab_admin', 'AB Admin', $capabilities );
        
        $capabilities = array(
            'edit_posts' => true,
            'delete_posts' => true,
            'edit_pages' => true,
            'delete_pages' => true,
            'manage_categories' => true,
        );
    
        // Add the role
        add_role( 'ab_planner', 'AB Planner', $capabilities );
    }

    function tf_check_user_role( $roles ) {
        /*@ Check user logged-in */
        if ( is_user_logged_in() ) :
            /*@ Get current logged-in user data */
            $user = wp_get_current_user();
            /*@ Fetch only roles */
            $currentUserRoles = $user->roles;
            /*@ Intersect both array to check any matching value */
            $isMatching = array_intersect( $currentUserRoles, $roles);
            $response = false;
            /*@ If any role matched then return true */
            if ( !empty($isMatching) ) :
                $response = true;        
            endif;
            return $response;
        endif;
    }

    function hide_admin_bar() {
        $roles = [ 
            'customer',
            'subscriber',
            'parent_role',
            'centre_user_role',
            'ab_admin',
        ];
        if ( $this->tf_check_user_role($roles) ) :
            add_filter('show_admin_bar', '__return_false');
        endif;
    }

    function password_change_action($user_id) {
        // Do something when the user's password is changed
        // For example, you can send a notification email to the user or perform other custom actions.
        // You can access the user's information using $user_id.
    
        // Example: Send an email to the user after password change
        $user = get_user_by('ID', $user_id);
        $user_email = $user->user_email;

        update_user_meta($user_id, 'first_login', '1');
    }
}
    
$custom_user_role = new Comworks_User_Role();