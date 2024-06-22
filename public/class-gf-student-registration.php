<?php
class Comworks_GF_StudentRegistration {
    private $notification = true;

    public function __construct() {
        add_filter( 'gform_form_post_get_meta_4', array( $this, 'add_child_fields' ) );
        add_filter( 'gform_form_update_meta_4', array( $this, 'remove_child_fields' ), 10, 3 );
        add_action( 'gform_after_submission_4', array( $this, 'student_registration' ), 10, 3 );
        add_action( 'gform_after_submission_4', array( $this, 'process_payment' ), 10, 4 );
        add_action( 'gform_pre_submission_4', array( $this, 'student_registration_before_submit' ), 10, 3 );
        add_filter( 'gform_pre_render_4', array( $this, 'setup_pricing_fields' ) );
    }

    function student_registration_before_submit($form) {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
    }

    function process_payment ( $entry, $form ) {
        $product_id = $entry['49'];
        \WC()->cart->empty_cart();
        \WC()->cart->add_to_cart( $product_id );
        return $form;
    }

    function setup_pricing_fields( $form ) {
        $field_id = 48; // make sure this is the correct field id

        $product_subscription_query = new WP_Query( [
            'post_type'      => 'product',
            'posts_per_page' => 999,
            'post_status'    => 'publish',
            'orderby'        => 'id',
            'order'          => 'ASC',
            'tax_query'      => [
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'name',
                    'terms'    => 'subscription',
                ) 
            ],
        ] );

        foreach ($form['fields'] as &$field) {
            if ($field['id'] == $field_id) {
                $content = '<h3>Choose a plan</h3>';
                
                $content .= '<div class="product-wrapper">';

                if ( $product_subscription_query->have_posts() ) {
                    while($product_subscription_query->have_posts() ) {
                        $product_subscription_query->the_post();
                        $post_id = get_the_ID();
                        $_product = wc_get_product( $post_id );

                        $content .= '<div class="product-container">';
                            $content .= '<h4 class="product-title">'. $_product->name .'</h4>';
                            $content .= '<span class="product-price">' . $_product->get_price_html() . '</span>';
                            $content .= '<span class="billing-type">' . get_post_meta( $post_id, 'billing_type', true ) . '</span>';
                            $content .= '<div class="short-description">' . $_product->short_description  . '</div>';
                            $content .= '<div class="description">' . $_product->description  . '</div>';
                            $content .= '<a href="#" class="select-plan-button" data-id="' . $_product->id . '">Select Plan</a>';
                        $content .= '</div>';
                    }
                } else {
                    esc_html_e( 'Sorry, no posts matched your criteria.' );
                }

                $content .= '</div>';

                // Restore original Post Data.
                wp_reset_postdata();

                $field->content = $content;
            }
        }
        return $form;
    }
    
    function add_child_fields( $form ) { 
        // Create a Single Line text field for the child's first name
        $first_name = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1002, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Child First Name',
            'pageNumber'  => 1, // Ensure this is correct
            'placeholder' => 'First Name...',
        ) );

        // Create a Single Line text field for the child's last name
        $last_name = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1003, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Child Last Name',
            'pageNumber'  => 1, // Ensure this is correct
            'placeholder' => 'Last Name...'
        ) );
    
        // Create an date field for the child's date of birth
        $dob = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1004, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Date of Birth',
            'placeholder' => 'Calendar...',
            'pageNumber'  => 1, // Ensure this is correct
            'calendarIconType' => 'calendar',
            'cssClass' => 'custom-date-js'
        ) );
    
        // Create a Single Line text field for the child's room name
        $centre_name = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1005, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'What is the ROOM NAME at the centre for your child?',
            'placeholder' => 'Room Name...',
            'pageNumber'  => 1, // Ensure this is correct
        ) );

        // Create a checkbox field for the child's days to attend.
        $days_to_attend = GF_Fields::create( array(
            'type'   => 'checkbox',
            'id'     => 1006, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Please select all days your child attends the centre',
            'pageNumber'  => 1, // Ensure this is correct
            'adminLabel' => 'Please select all days your child attends the centre',
            'cssClass' => 'days-to-attend-checkboxes',
            'choices' => array(
                array(
                    'text' => 'Monday',
                    'value' => 'Monday'
                ),
                array(
                    'text' => 'Tuesday',
                    'value' => 'Tuesday'
                ),
                array(
                    'text' => 'Wednesday',
                    'value' => 'Wednesday'
                ),
                array(
                    'text' => 'Thursday',
                    'value' => 'Thursday'
                ),
                array(
                    'text' => 'Friday',
                    'value' => 'Friday'
                ),
            )
        ) );

        $days_to_attend_data = GF_Fields::create( array(
            'type'   => 'hidden',
            'id'     => 5001, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Days to attend',
            'cssClass' => 'days-to-attend-data',
            'pageNumber'  => 1, // Ensure this is correct
        ) );

        $session_per_week_intro = GF_Fields::create( array(
            'type'   => 'html', 
            'id'     => 1007, // The Field ID must be unique on the form
            'pageNumber'  => 1,
            'content' => '
                <label class="gfield_label">Select how many sessions per week</label>
                <br>
                <br>
                <p>
                    Subject to numbers, 2 sessions per week may be available. If this is the case, and you wish your
                    <br>
                    child to attend both sessions, the second session will be half price. For Families, Any extra
                    <br>
                    child\'s session is HALF PRICE.
                </p>
            '
        ) );

        $how_many_sessions = GF_Fields::create( array(
            'type'   => 'radio',
            'id'     => 1008, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'pageNumber'  => 1, // Ensure this is correct
            'adminLabel' => '',
            'choices' => array(
                array(
                    'text' => 'One session only',
                    'value' => 'ONE'
                ),
                array(
                    'text' => 'Interested in more than one session',
                    'value' => 'MULTI'
                ),
            )
        ) );

        $child_info_intro = GF_Fields::create( array(
            'type'   => 'html', 
            'id'     => 1009, // The Field ID must be unique on the form
            'pageNumber'  => 1,
            'content' => '<h3>Child Information</h3>'
        ) );

        // Create a repeater for the team members and add the name and email fields as the fields to display inside the repeater.
        $child_fields = GF_Fields::create( array(
            'type'             => 'repeater',
            'id'               => 1000, // The Field ID must be unique on the form
            'formId'           => $form['id'],
            'label'            => 'Child Information',
            'addButtonText'    => 'Add another child', // Optional
            'removeButtonText' => 'Remove child', // Optional
            'pageNumber'       => 0, // Ensure this is correct
            'cssClass'         => 'consent-required',
            'fields'           => array ( 
                $child_info_intro, 
                $first_name, 
                $last_name, 
                $dob, 
                $centre_name, 
                $days_to_attend, 
                $days_to_attend_data,
                $session_per_week_intro, 
                $how_many_sessions
            ), // Add the fields here.
        ) );
    
        array_splice( $form['fields'], 4, 0, array( $child_fields ) );
    
        return $form;
    }
    
    function remove_child_fields ( $form_meta, $form_id, $meta_name ) {
    
        if ( $meta_name == 'display_meta' ) {
            // Remove the Repeater field: ID 1000
            $form_meta['fields'] = wp_list_filter( $form_meta['fields'], array( 'id' => 1000 ), 'NOT' );
        }
    
        return $form_meta;
    }
    function student_registration ( $entry, $form ) {
        $centre_id = $entry['13'];
        
        // parent ID should have a lookup on the portal
        $parent_id = get_user_meta($entry['created_by'], 'portal_uid', true);

        require_once PORTAL_URI . 'includes/class-db-connection.php';
        require_once PORTAL_URI . 'admin/objects/class-parents.php';
        $db = new Database();
        $parent_obj = new Parents($db->conn);
        $parent = $parent_obj->get_parent($parent_id);

        $children_information = $entry['1000'];

        foreach ($children_information as $child) {
            $first_name = $child['1002'];
            $last_name = $child['1003'];
            $dob = $child['1004'];
            $room = $child['1005'];
            $days_to_attend = $child['5001'];
            $sessions = $child['1008'];

            $user_data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'fullname' => $first_name . ' ' . $last_name,
                'comment' => '',
                'dob' => $dob,
                'parent_id' => $parent['ParentID'],
                'centre_id' => $centre_id,
                'days_attending' => $days_to_attend,
                'sessions' => $sessions,
                'status' => '',
            );

            // TODO: Optimize this one later
            $this->create_student($user_data);
        }
    }

    public function create_student ($student_data) {

        if ( !is_array($student_data) )
            return false;

        require_once PORTAL_URI . 'includes/class-db-connection.php';
        require_once PORTAL_URI . 'admin/objects/class-students.php';

        $db = new Database();
        $students_obj = new Students($db->conn);
        $students_obj->set_student($student_data);
        $students_obj->create();
        
    }
}

$gf_student_registration = new Comworks_GF_StudentRegistration();