<section class="dashboard student-registration">
    <div class="container">
        <div class="heading">
            <div class="heading-wrapper">
              <img src="<?php echo PORTAL_URL . 'assets/img/logo-funfit.svg'?>" alt="icon-dashboard" />
              <div>
                <h6>Getting Started</h6>
                <h2>Registration Form</h2>
              </div>
              <img src="<?php echo PORTAL_URL . 'assets/img/logo-active.svg'?>" alt="icon-dashboard" />
            </div>
        </div>
        <div class="content">
          <?php echo do_shortcode('[gravityform id="4" title="false"]')?>
        </div>
    </div>
</section>
<script type="text/javascript">
  jQuery(document).ready(function () {
    // setting checkboxes input workaround
    jQuery('.gfield_repeater_items').on('click', '.days-to-attend-checkboxes .gfield_checkbox input[type=checkbox]', function() {
      const daysToAttendCheckboxes = jQuery(this).parents('.gfield_repeater_item').find('.days-to-attend-checkboxes .gfield_checkbox input[type=checkbox]')
      const daysToAttendInputData = jQuery(this).parents('.gfield_repeater_item').find('.days-to-attend-data input[type=hidden]')

      const daysToAttendInputArray = []
      daysToAttendCheckboxes.map((checkbox) => {
        const currentCheckbox = jQuery(daysToAttendCheckboxes[checkbox])
        if (currentCheckbox.is(':checked')) {
          daysToAttendInputArray.push(currentCheckbox.val())
        }
      })
      daysToAttendInputData.val(daysToAttendInputArray.join(','))
    })

    // fixing date calendar input
    jQuery('.custom-date-js input[type=text]').attr('type', 'date')

    jQuery('.select-plan-button').on('click', function (e) {
      e.preventDefault();
      jQuery('input[name=input_49]').val(jQuery(this).data('id'));

      // set timeout to make sure the value has been set properly
      jQuery('#gform_4').submit();
      return false;
    })

    // hide consent required field 
    const consentCheckbox = jQuery('.consent input[type=checkbox]')
    const consentRequiredFields = jQuery('.gform_page_footer, .consent-required')

    const consentRequiredFieldsInput = consentRequiredFields.find('select, input, button')

    if (!consentCheckbox.is(':checked')) {
      consentRequiredFieldsInput.attr('disabled', 'disabled')
    }

    setTimeout(() => {
      const centreNameSelection = jQuery('.centre-name-selection')
      const parentInfoField = jQuery('.parent-info-field')
      const acknowledgementCheckbox = jQuery('.acknowledgement-checkbox')

      // arrange the divs 
      centreNameSelection.appendTo('.choose-centre-html')
      parentInfoField.appendTo('.parent-info-title-html')
      acknowledgementCheckbox.appendTo('.acknowledgement-html')

      consentRequiredFields.show();
      centreNameSelection.show()
      parentInfoField.show()
      acknowledgementCheckbox.show()
    }, 1000);

    consentCheckbox.on('click', function() {
      if (jQuery(this).is(':checked')) {
        consentRequiredFieldsInput.removeAttr('disabled')
      } else {
        consentRequiredFieldsInput.attr('disabled', 'disabled')
      }
    })

  })
</script>