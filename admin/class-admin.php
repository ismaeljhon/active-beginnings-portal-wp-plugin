<?php
class Comworks_Admin {
    public function __construct() {
        $this->class_loader();
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function class_loader() {
        $classes = array(
            'dashboard-page',
			'wp-setting',
            'wp-role',
            'wc-subscription',
        );

        if ( !empty(get_option( 'db_host' )) && get_option( 'db_host' ) !== '' ) {
            array_push( $classes, 'wp-parents', 'wp-centre-users', 'wp-abadmins' );
        }

        foreach ($classes as $class) {
            require_once plugin_dir_path( __FILE__ ) . 'class-' . $class . '.php';
        }
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_style('comworks-ab-portal-style', plugins_url('../assets/css/admin-styles.css', __FILE__));
    }
}

$admin = new Comworks_Admin();