<?php
class Comworks_GF_Signup {
    private $notification = true;

    public function __construct() {
        add_filter( 'gform_validation_3', array( $this, 'check_username' ) );
        add_action( 'gform_after_submission_3', array( $this, 'parent_registration' ), 10, 4 );
        //add_filter( 'gform_notification_3', array( $this, 'cancel_user_notification' ), 10, 3 );
    }

    function check_username( $validation_result ) {
        $form  = $validation_result['form'];
        
        if ( email_exists(rgpost( 'input_6' )) ) {
            // set the form validation to false
            $validation_result['is_valid'] = false;
            $login_url = home_url('/login');
      
            //finding Field with ID of 1 and marking it as failed validation
            foreach( $form['fields'] as &$field ) {
      
                //NOTE: replace 1 with the field you would like to validate
                if ( $field->id == '6' ) {
                    $field->failed_validation = true;
                    $field->validation_message = "Email already exist. Login <a href='{$login_url}'>here.</a>";
                    break;
                }
            }
      
        }
      
        //Assign modified $form object back to the validation result
        $validation_result['form'] = $form;
        return $validation_result;
    }

    public function parent_registration( $entry, $form ) {
        $email = $entry['6'];
        $password = $entry['7'];
        $uid = substr( uniqid(), -8 ) . '-' .
            substr( uniqid(), 0, 4 ) . '-' .
            substr( uniqid(), -4 ) . '-' .
            substr( uniqid(), -8, 4 ) . '-' .
            substr( md5($email), -12 );
        $first_name = $entry['1'];
        $last_name = $entry['3'];
        $full_name = $first_name . ' ' . $last_name;

        // Add record to wp db
        $user_id = wp_create_user($email, $password, $email);
        $user_id_role = new WP_User($user_id);
        // $user_id_role->set_role('parent_role');
        update_user_meta( $user_id, 'full_name', $full_name );
        update_user_meta( $user_id, 'portal_uid', $uid );
        update_user_meta( $user_id, 'first_name',  $first_name );
        update_user_meta( $user_id, 'last_name', $last_name );

        GFAPI::update_entry_field($entry['id'], 8, md5($email));
        
        if (is_wp_error($user_id)) {
            $this->notification = false;
            return;
        }

        // add user record to fred (Portal)
        $user_data = array(
            'username' => $email,
            'full_name' => $full_name,
            'email' => $email,
            'report_email' => $email,
            'phone' => $entry['4'],
            'address1' => '',
            'suburb' => '',
            'postcode' => '',
            'payment_method' => '',
            'comment' => '',
            'status' => 'N',
            'orig_id' => 0,
            'consent' => '',
            'uid' => $uid,
        );

        $this->create_parent($user_data);
        
    }

    public function create_parent($parent_data) {

        if ( !is_array($parent_data) )
            return false;

        require_once PORTAL_URI . 'includes/class-db-connection.php';
        require_once PORTAL_URI . 'admin/objects/class-parents.php';

        $db = new Database();
        $parents_obj = new Parents($db->conn);
        $parents_obj->set_parent($parent_data);
        $parents_obj->create();
        
    }

    private function send_activation_email($email, $name) {
        $home_url = home_url();
        $verification_url = "{$home_url}/?verification=" . md5($email) . "&user={$email}";
        $subject = 'FunFit Kidz - Thank you for signing up!';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $body = "<img class='size-medium wp-image-9 aligncenter' src='{$home_url}/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png' alt='' width='300' height='208' />
            &nbsp;
            Hi {$name},
            &nbsp;
            Thank you for signing up to FunFit Kidz. Click this <a href='{$verification_url}'>link</a> to verify your account.
            ";

        wp_mail( $email, $subject, $body, $headers );
    }

    function cancel_user_notification($is_active, $notification, $form, $entry) {
        // Change 1 to the notification ID you want to cancel
        $notification_id_to_cancel = '65b88954446a4';

        // Check if this is the user notification you want to cancel
        if ($notification['id'] == $notification_id_to_cancel) {
            return false; // Cancel the notification
        }

        return $is_active; // Continue with other notifications
    }
}
$gf_signup = new Comworks_GF_Signup();
