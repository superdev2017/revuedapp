$(function() {

    /************* Facebook login **************/
    if($('#fb_login_button').length > 0) {
        window.fbAsyncInit = function() {
            FB.init({
                appId: custom_config.fbAppId,
                cookie: true, // This is important, it's not enabled by default
                version: 'v2.2'
            });

            FB.Event.subscribe('auth.authResponseChange', function(response) {
                if (response.authResponse) {
                    var accessToken = response.authResponse.accessToken;
                    FB.api('/me', 'get', { access_token: accessToken, fields: 'name,email' }, function(response) {
                        location.href=custom_config.fbLoginUrl + '?email=' + response.email;
                    });
                } else {
                    alert('User cancelled login or did not fully authorize.');
                }
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

    }

    /************* Link Facebook Account **************/
    if( $('#fb_link_facebook').length > 0 ) {
        // Page list
        var pages = [];
        // Initialize facebook javascript sdk
        window.fbAsyncInit = function() {
            FB.init({
                appId: custom_config.fbAppId,
                cookie: true, // This is important, it's not enabled by default
                version: 'v2.2'
            });

            FB.Event.subscribe('auth.authResponseChange', function(response) {
                if (response.authResponse) {
                    var accessToken = response.authResponse.accessToken;
                    FB.api('/me', 'get', { access_token: accessToken, fields: 'name,email' }, function(response) {
                        $('#fb-email').val(response.email);
                    });
                    requestFacebookPages();
                } else {
                    alert('You cancelled login or did not fully authorize.');
                }
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function requestFacebookPages() {
            var ajaxUrl = custom_config.fbFetchPageUrl;
            $('#pages_select').attr('disabled','disabled');
            $('#fb_link_facebook').addClass('disabledbutton');
            $.post(ajaxUrl, function(data) {
                if(data.error) {
                    alert(error.message);
                } else {
                    pages = data.data.pages;
                    $('#pages_select').html('');
                    $('#user-setting-fb-page-id').val('');
                    $('#user-setting-fb-api-access-token').val('');
                    if(pages.length == 0) {
                        alert('We can not find any page');
                    }
                    for(i = 0; i < pages.length; i++) {
                        var option = '<option value=' + pages[i].id + '>' + pages[i].name + '</option>';
                        $('#pages_select').append(option);
                    }

                    $('#pages_select').removeAttr('disabled');
                    $('#fb_link_facebook').removeClass('disabledbutton');
                    //$('#pages_select').val('<?php echo $user->user_setting->fb_page_id ?>');
                }
            }).fail(function() {
                $('#fb_link_facebook').removeClass('disabledbutton');
                $('#pages_select').removeAttr('disabled');
                alert("Network error! Please try again.");
            });
        }
        $(function() {
            // Changing the page
            $('#pages_select').change(function() {
                var index = $("#pages_select")[0].selectedIndex;
                $('#user-setting-fb-page-title').val(pages[index].name);
                $('#user-setting-fb-page-id').val(pages[index].id);
                $('#user-setting-fb-api-access-token').val(pages[index].access_token);
                //$(this).attr('disabled','disabled');
                //getPageRating();
            });
        })

        // Getting a long lived access token for user
        function getPageRating() {
            var ajaxUrl = custom_config.fbFetchRatingUrl; //'<?= $this->Url->build(["controller" => "Dashboard", "action" => "requestPage"]); ?>';
            $.post(ajaxUrl, {page_id: $('#user-setting-fb-page-id').val(), access_tocken: $('#user-setting-fb-api-access-token').val()}, function(data) {
                var message = "Page reviewer\n\n";
                $.each(data.data, function(index, value) {
                    console.log(value);
                    message += 'subscriber:' + value.reviewer.name + "\nrating:" + value.rating + "\nreview:" + value.review_text + "\n#####################";
                });
                $('#pages_select').removeAttr('disabled');
                alert(message);
            }).fail(function() {
                $('#pages_select').removeAttr('disabled');
                alert("Network error! Please try again.");
            });
        }
    }
});



