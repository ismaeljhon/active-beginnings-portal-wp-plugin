<?php
class Comworks_Public {
    public function __construct() {
        $this->class_loader();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('template_redirect', function () {
            ob_start();
        });
        $templates_loader = new Comworks_Register_Templates();
        add_action( 'plugins_loaded', array( $templates_loader, 'get_instance' ) );
    }

    public function class_loader() {
        $classes = array(
            'register-templates',
            'shortcodes',
            'woocommerce-hooks',
            'gf-forms',
            'gf-signup',
            'gf-student-registration',
            'wp-ajax',
            'wp-redirects'
        );

        foreach ($classes as $class) {
            require_once plugin_dir_path( __FILE__ ) . 'class-' . $class . '.php';
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 
            'comworks-inter-font',
            'https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap',
            false
        );
        wp_enqueue_style( 
            'comworks-ab-portal-public-style',
            plugins_url('../assets/css/public-styles.css', __FILE__ )
        );

        if ( isset($_GET['tab']) || is_page_template('templates/dashboard-template.php') ) {
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
            $user_id = get_current_user_id();
            $user = new WP_User($user_id);

            wp_enqueue_script(
                'comworks-jq-validate',
                'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );
            wp_enqueue_script(
                'comworks-wp-ajax',
                plugins_url('../assets/js/comworks-wp-ajax.js', __FILE__ ),
                array( 'jquery' ),
                '1.0.0',
                true
            );
            wp_localize_script(
                'comworks-wp-ajax',
                'my_ajax_object',
                array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) 
            );

            $current_user = wp_get_current_user();
            $current_user_id = $current_user->ID;
    
            if ( isset($current_user->roles ) &&
                ( $current_user->roles[0] == 'parent_role' || $current_user->roles[0] == 'centre_user_role' ) && 
                get_user_meta( $current_user_id, 'first_login', true ) != '1' 
                ) {
                wp_enqueue_script(
                    'comworks-account-validate-first',
                    plugins_url('../assets/js/account-validate-first.js', __FILE__ ),
                    array( 'jquery' ),
                    '1.0.0',
                    true
                );
            } else {
                wp_enqueue_script( 
                    'comworks-account-validate',
                    plugins_url('../assets/js/account-validate.js', __FILE__ ),
                    array( 'jquery' ),
                    '2.0.0',
                    true
                );
            }

            if ( isset($_GET['tab']) && $_GET['tab'] == 'centre-students' ) {
                wp_enqueue_script(
                    'comworks-centre-students',
                    plugins_url('../assets/js/centre-students.js', __FILE__ ),
                    array( 'jquery' ),
                    '1.0.0',
                    true
                );
            }
        
            if ( isset($_GET['tab']) && $_GET['tab'] == 'gross-motor-report' ) {
                wp_enqueue_style( 
                    'comworks-ab-portal-animate',
                    'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css'
                );
                wp_enqueue_script(
                    'comworks-gmp-script',
                    plugins_url('../assets/js/script.js', __FILE__ ),
                    array( 'jquery' ),
                    '1.0.0',
                    true
                );
            }
            
        }

    }

}

$public = new Comworks_Public();