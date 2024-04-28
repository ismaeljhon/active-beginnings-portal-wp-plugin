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
		submitHandler: function () { 
            $("#account_display_name").val($('#account_first_name').val())
            
            $form.submit()
        },
    })
})(jQuery)