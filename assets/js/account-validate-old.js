(function($) {
    $.validator.addMethod("notEqualTo", function(value, element, param) {
        var notEqual = true
        value = $.trim(value)
    
        for (i = 0; i < param.length; i++) {
            if (value == $.trim($(param[i]).val())) { 
                notEqual = false
            }
        }
    
        return this.optional(element) || notEqual
    }, "<span class='error'>Display name must be different from your Email</span>")


    const $form = $('.woocommerce-EditAccountForm')
    $form.validate({
        rules: {
            account_username: {
                required: true,
                minlength: 2,
                remote: {
                    url: my_ajax_object.ajax_url,
                    type: "post",
                    data: {
                        action: "check_username",
                        username: $('#account_username').val(),
                        current_user_id: $('#account_user_id').val() // Replace with the actual ID of the current user
                    },
                    beforeSend: function(xhr, settings) {
					    settings.data += "&custom_data=true"; // Manipulate the request data
					},
					dataFilter: function(response) {
                        var result = JSON.parse(response);
                        if (result.data.exist == true) {
                            $("#account_username-error").text("Username already exists. Please choose a different username.")
                            return false; // Validation failed
                        } else {
                            return true; // Validation passed
                        }
					}
                },
            },
            account_display_name: {
                required: true,
                minlength: 2,
                notEqualTo: ['#account_email']
            },
			account_first_name: {
                required: true,
                minlength: 2
            },
            account_last_name: {
                required: true,
                minlength: 2
            },
            account_email: {
                required: true,
                email: true
            },
            password_2: {
                equalTo: '#password_1',
            },
        },
        messages: {
            account_username: {
                remote: "Username already exists. Please choose a different username.",
                required: "Please enter a username.",
            },
            account_first_name: "Please specify your First Name",
            account_last_name: "Please specify your Last Name",
            email: {
                required: "We need your email address to contact you",
                email: "Your email address must be in the format of name@domain.com"
            },
            password_2: {
                equalTo: "Entered password does not match",
            },
        },
        showErrors: function(errorMap, errorList) {
            // Update the error message for the username field
            if (errorMap.account_username) {
              var response = errorMap.account_username;
              var errorMessage = "Username already exists. Please choose a different username.";
              $(this.currentForm).find("#account_username-error").text(errorMessage);
            }
        
            this.defaultShowErrors(); // Display the default error messages for other fields
        },
		submitHandler: function () { 
            $("#account_display_name").val($('#account_first_name').val())
            
            $form.submit()
        },
    })
})(jQuery)