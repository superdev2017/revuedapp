$(function() {
    // Form Validations
    $( "#user-register-form" ).validate({
        errorPlacement: function(){
            return false;
        },
        rules: {
            username: {
                required: true,
                remote: custom_config.baseUrl + "users/checkUsername"
            },
            password: {
                required: true
            },
            confpassword: {
                required: true,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true,
                //remote: custom_config.baseUrl + "users/checkEmail"
            },
            first_name: {
                required: true,
                minlength: 2
            },
            last_name: {
                required: true,
                minlength: 2
            },
            phone: {
                required: true,
                phoneUS: true
            },
            address: {
                required: true
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
            zip: {
                required: true,
                digits: true,
                minlength: 5,
                maxlength: 5
            },
            terms: {
                required: true
            },
            "usersetting[domain]": {
                required: true
            },
            "billing[card_number]": {
                required: true,
                creditcard: true
            },
            "billing[expiration_date]": {
                required: true
            },
            "billing[cvc]": {
                required: true,
                minlength: 3,
                maxlength: 4
            },
            "billing[first_name]": {
                required: true,
                minlength: 2
            },
            "billing[last_name]": {
                required: true,
                minlength: 2
            },
            "billing[address]": {
                required: true
            },
            "billing[city]": {
                required: true
            },
            "billing[state]": {
                required: true
            },
            "billing[zip]": {
                required: true,
                digits: true,
                minlength: 5,
                maxlength: 5
            }
        },
        submitHandler: function(form){
            form.submit();
        }
    });

    $( "#reseller-register-form" ).validate({
        errorPlacement: function(){
            return false;
        },
        rules: {
            username: {
                required: true,
                remote: custom_config.baseUrl + "users/checkUsername"
            },
            password: {
                required: true
            },
            confpassword: {
                required: true,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true,
                //remote: custom_config.baseUrl + "users/checkEmail"
            },
            first_name: {
                required: true,
                minlength: 2
            },
            last_name: {
                required: true,
                minlength: 2
            },
            phone: {
                required: true,
                phoneUS: true
            },
            address: {
                required: true
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
            zip: {
                required: true,
                digits: true,
                minlength: 5,
                maxlength: 5
            },
            terms: {
                required: true
            },
            agreements: {
                required: true
            }
        },
        submitHandler: function(form){
            $.LoadingOverlay("show", {
                image       : "img/loading.gif"
            });

            form.submit();
        }
    });

    // Modal Handling
    $( ".terms-modal .modal-body" ).on('scroll', function(){
        var mBody = $(this);

        if(mBody.scrollTop() >= (mBody[0].scrollHeight - mBody.outerHeight())) {
            mBody.parent().find( "button.btn-primary" ).prop('disabled', false);
        }
    });

    $( ".terms-modal button.btn-primary" ).click(function(){
        var target = $(this).data('enable-target');
        $(target).prop('checked', true);
    });

    $( "#accept-terms" ).click(function(){
        $( "#termsModal" ).modal();
        return false;
    });

    $( "#accept-agreements" ).click(function(){
        $( "#agreementsModal" ).modal();
        return false;
    });

    // Fileupload
    $( '#fileupload' ).click(function(){
        $( '#progress .progress-bar' ).css('width', '0%');
    });

    $( '#fileupload' ).fileupload({
        url: custom_config.baseUrl + "/store",
        dataType: 'json',
        done: function (e, data) {

            var section = $( '#fileupload' ).closest('section')
            if (data.result && data.result.message == 'success') {
                section.html('<div class="alert alert-success text-center" role="alert">Your video review has been submitted, thank you.</div>');
            } else {
                section.html('<div class="alert alert-danger text-center" role="alert">Unable to upload please try again.</div>');
                //section.html('<div class="alert alert-danger text-center" role="alert">'+data.result.message+'</div>');
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    })
    .prop('disabled', !$.support.fileInput)
    .parent()
    .addClass($.support.fileInput ? undefined : 'disabled');


    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        data.formData = {'token': $( '#token' ).val(), 'rating': $( '#rating' ).val()};
        if ( ! data.formData.token) {
            data.context.find('button').prop('disabled', false);
            input.focus();
            return false;
        }
    });
});
