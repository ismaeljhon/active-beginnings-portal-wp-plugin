<?php
class Comworks_WP_Settings {
    public function __construct() {
		  add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function register_settings() {
        register_setting( 'portal_settings', 'db_host' );
        register_setting( 'portal_settings', 'db_name' );
        register_setting( 'portal_settings', 'db_user' );
        register_setting( 'portal_settings', 'db_password' );

        register_setting( 'portal_dashboard_settings', 'pd_welcome_text' );
        register_setting( 'portal_dashboard_settings', 'pd_title_text' );
        register_setting( 'portal_dashboard_settings', 'pd_desc_text' );
        register_setting( 'portal_dashboard_settings', 'pd_assess_text' );
        register_setting( 'portal_dashboard_settings', 'pd_account_text' );
        register_setting( 'portal_dashboard_settings', 'pd_contact_text' );
    }
}

$db_settings = new Comworks_WP_Settings();