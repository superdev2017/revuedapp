$(function() {
    var filters = {}
    var sources = [];
    var status = 'all';
    var reviewList = $( "#review-list" );

    getReviews(filters);

    $( '.filter-status' ).click(function(){
        $( '.filter-status' ).removeClass('selected');
        $(this).toggleClass('selected');

        status = $(this).attr('data-filter-value');

        filters.status = status;
        getReviews(filters);
    });

    $( '.filter-icon' ).click(function(){
        $(this).toggleClass('selected');

        var source = $(this).attr('data-filter-value');
        var found = $.inArray(source, sources);

        if (found >= 0) {
            sources.splice(found, 1);
        } else {
            sources.push(source);
        }

        filters.sources = sources;
        getReviews(filters);
    });

    reviewList.on('click', '.paginator a', function() {
        if($(this).attr("href")) {
            reviewList.empty().load($(this).attr("href"), filters);
        }

        return false;
    });

    reviewList.on('change', '.limit', function() {
        filters.limit = $(this).val();
        getReviews(filters);

        return false;
    });

    function getReviews(filters) {
        reviewList.load(custom_config.baseUrl + "dashboard/reviews", filters);
    }

    $( "#approve-dialog" ).dialog({
        modal: true,
        bgiframe: true,
        width: 500,
        height: 200,
        autoOpen: false
    });

    $( "#archive-dialog" ).dialog({
        modal: true,
        bgiframe: true,
        width: 500,
        height: 200,
        autoOpen: false
    });

    $( "#select-btn" ).click(function(){
        reviewList.find( "input[type='checkbox']" ).prop('checked', 'checked');
    });

    $( "#approve-btn" ).click(function(e) {
        var theHREF = $(this).attr("href");

        $( "#approve-dialog" ).dialog('option', 'buttons', {
            "Confirm" : function() {
                bulkOperations({operation: 'update-status', status: 'active'});
                $(this).dialog("close");
            },
            "Cancel" : function() {
                $(this).dialog("close");
            }
        });

        $( "#approve-dialog" ).dialog("open");
    });

    $( "#archive-btn" ).click(function(e) {
        var theHREF = $(this).attr("href");

        $( "#archive-dialog" ).dialog('option', 'buttons', {
            "Confirm" : function() {
                bulkOperations({operation: 'update-status', status: 'archived'});
                $(this).dialog("close");
            },
            "Cancel" : function() {
                $(this).dialog("close");
            }
        });

        $( "#archive-dialog" ).dialog("open");
    });

    function bulkOperations(request) {
        var list = reviewList.find( "input[type='checkbox']:checked:enabled" ).map(function(){return $(this).attr('data-id');}).get();
        request.reviews = list;

        $.ajax({
            url: custom_config.baseUrl + "work/bulkOperations",
            type: "POST",
            dataType: 'json',
            data: request,
            complete: function() {
                location.reload();
            }
        });
    }

    $( "#subscription-form" ).validate({
        errorPlacement: function(){
            return false;
        },
        rules: {
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
            $(this).get(0).submit();
            location.reload();
        }
    });

    // Help Dialogs
    $( "#help-settings-dialog" ).dialog({
        autoOpen: false,
        position: { my: "center center", at: "left top", of: $( ".panel" )},
        minHeight: 500,
        minWidth: 700,
        show: {
            effect: "fade",
            duration: 300
        },
        hide: {
            effect: "fade",
            duration: 300
        },
        classes: {
            ".ui-dialog-content": "help-dialog-content"
        }
    });

    $( "#help-settings" ).on( "click", function() {
        $( "#help-settings-dialog" ).dialog( "open" );
    });

});
