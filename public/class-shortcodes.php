<?php
class Comworks_Shortcodes {
    public function __construct() {
        add_shortcode( 'login_redirect_url', array( $this, 'get_login_redirect' ) );
    }

    private function get_current_user_role() {
        $current_user = wp_get_current_user();
        
        if ( empty($current_user->roles) )
            return 'Guest';
        
        return $current_user->roles[0];
    }

    public function get_login_redirect() {
        
        $role = get_current_user_role();

        if ( $role == 'parent_role' )
            return get_home_url() . '/dashboard';

    }
}
