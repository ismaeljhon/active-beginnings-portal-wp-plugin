<?php
class Comworks_WP_Redirects {
    public function __construct() {
        add_action('init', array( $this, 'redirect_login_page') );
        add_filter( 'logout_redirect',  array( $this, 'logout_redirect' ) );
        add_filter( 'authenticate', array( $this, 'verify_username_password' ), 1, 3);
        add_filter( 'wp_authenticate_user', array( $this, 'custom_authenticate_user' ), 10, 2);
        add_action( 'wp_login_failed', array( $this, 'login_failed' ) ); 
    }

    public function logout_redirect() {
        return esc_url( home_url('/login')  );
    }

    function verify_username_password( $user, $username, $password ) {
        $login_page  = home_url( '/login/' );
        if ( $username == "" || $password == "" ) {
            wp_redirect( $login_page . "?login=empty" );
            exit;
        }
    }

    function custom_authenticate_user($user, $password) {
        // Check if the user is verified
        if (is_a($user, 'WP_User') && in_array( 'subscriber', $user->roles, true ) ) {
            // Unverified user, prevent login
            return new WP_Error('unverified_user', __('You need to verify your email address before logging in.'));
        }

        // Allow login for verified users
        return $user;
    }

    function redirect_login_page() {
        $login_page  = home_url( '/login/' );
        $page_viewed = basename($_SERVER['REQUEST_URI']);
        
        if ( isset($_GET['verification']) ) {
            $verification_code = $_GET['verification'];
            $email = $_GET['user'];

            
        }

        if ( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
          wp_redirect($login_page);
          exit;
        }
    }
      
    function login_failed($username) {
        $login_page  = home_url( '/login/' );

        $user = get_user_by('email', $username);
        if ( in_array( 'subscriber', $user->roles ) ) {
            wp_redirect( $login_page . '?login=unverified' );
            exit;
        }

        wp_redirect( $login_page . '?login=failed' );
        exit;
    }
}
$redirects = new Comworks_WP_Redirects();