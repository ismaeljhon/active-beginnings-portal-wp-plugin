<?php
class Comworks_GF_Forms {
    public function __construct() {
        add_filter( 'gform_pre_render', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_pre_validation', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_pre_submission_filter', array( $this, 'populate_dropdown_field' ) );
        add_filter( 'gform_admin_pre_render', array( $this, 'populate_dropdown_field' ) );
    }

    function populate_dropdown_field($form) {
        // Specify the form ID and the field ID you want to populate
        $form_ids = [4];
        $field_ids = [13];
    
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
                $field->placeholder = 'Centre Name...';
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

    
}
$gf_forms = new Comworks_GF_Forms();
