<?php
class Comworks_GF_Forms {
    public function __construct() {
        add_filter( 'gform_pre_render', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_pre_validation', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_pre_submission_filter', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_admin_pre_render', array( $this, 'populate_dropdown_field' ) );

        //add_filter( 'gform_form_post_get_meta_2', array( $this, 'add_my_field' ) );
        //add_filter( 'gform_form_update_meta_2', array( $this, 'remove_my_field' ), 10, 3 );
    }

    function populate_dropdown_field($form) {
        // Specify the form ID and the field ID you want to populate
        $form_ids = [2, 3];
        $field_ids = [5, 6];
    
        // Check if the current form matches the specified form ID
        if ( !in_array($form['id'], $form_ids) )
            return $form;

        require_once PORTAL_URI . 'includes/class-db-connection.php';
        require_once PORTAL_URI . 'admin/objects/class-centres.php';

        $db = new Database();
        $centre_obj = new Centres($db->conn);
        $centres = $centre_obj->get_all();

        // Find the target field by ID
        foreach ($form['fields'] as &$field) {
            if ( in_array($field['id'], $field_ids) && $field->type == 'select') {
                $field->placeholder = 'Select Center';
                // Populate the choices with dynamic data
                $field->choices = array();

                foreach ($centres as $centre) {
                    $field->choices[] = array(
                        'text' => $centre['Name'],
                        'value' => $centre['CentreID'],
                    );
                }

                break;
            }
        }
        
        return $form;
    }

    function add_my_field( $form ) {
    
        // Create a Single Line text field for the team member's name
        $centre = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1001, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Room Name @ Your Centre',
            'pageNumber'  => 1, // Ensure this is correct
        ) );

        // Create a Single Line text field for the team member's name
        $name = GF_Fields::create( array(
            'type'   => 'text',
            'id'     => 1002, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Child Name',
            'pageNumber'  => 1, // Ensure this is correct
        ) );
    
        // Create an email field for the team member's email address
        $dob = GF_Fields::create( array(
            'type'   => 'date',
            'id'     => 1003, // The Field ID must be unique on the form
            'formId' => $form['id'],
            'label'  => 'Date of Birth',
            'pageNumber'  => 1, // Ensure this is correct
        ) );
    
        // Create a repeater for the team members and add the name and email fields as the fields to display inside the repeater.
        $team = GF_Fields::create( array(
            'type'             => 'repeater',
            'description'      => 'Maximum of 3 children',
            'id'               => 1000, // The Field ID must be unique on the form
            'formId'           => $form['id'],
            'label'            => 'Child/Children',
            'addButtonText'    => 'Add Child', // Optional
            'removeButtonText' => 'Remove Child', // Optional
            'maxItems'         => 3, // Optional
            'pageNumber'       => 0, // Ensure this is correct
            'fields'           => array( $centre, $name, $dob ), // Add the fields here.
        ) );
    
        array_splice( $form['fields'], 3, 0, array( $team ) );
    
        return $form;
    }
    
    function remove_my_field( $form_meta, $form_id, $meta_name ) {
    
        if ( $meta_name == 'display_meta' ) {
            // Remove the Repeater field: ID 1000
            $form_meta['fields'] = wp_list_filter( $form_meta['fields'], array( 'id' => 1000 ), 'NOT' );
        }
    
        return $form_meta;
    }
}
$gf_forms = new Comworks_GF_Forms();