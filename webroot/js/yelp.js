$(function() {
    var lat = 34.052235;
    var lng = -118.243683;

    function getLocation() {
        if (!navigator.geolocation){
            alert("<p>Geolocation is not supported by your browser</p>");
            return;
        }
        function success(position) {
            lat  = position.coords.latitude;
            lng = position.coords.longitude;
        }

        function error(e) {
            console.log(e);
            alert("Unable to retrieve your location");
        }
        navigator.geolocation.getCurrentPosition(success, error);
    }

    var selectedItem = null;
    //getLocation();
    $( "#user-setting-yp-business-name" ).autocomplete({
        delay: 1000,
        source: function( request, response ) {

            $this = $( "#user-setting-yp-business-name" );
            $this.autocomplete('widget').hide();
            $this.attr('readonly', true);
            //parameters = {'term': request.term, 'location': '', 'latitude': lat, 'longitude': lng};

            if($('#user-setting-yp-state').val() == '' || $('#user-setting-yp-city').val() == '') {
                return;
            }
            var near = $('#user-setting-yp-city').val() + ', ' + $('#user-setting-yp-state').val() + ', United States';
            var limit = $('#user-setting-yp-parser-limit').val();
            parameters = {'term': request.term, 'location': near, 'limit' : limit};

            var ajaxUrl = custom_config.yelpSearchUrl;

            $.ajax({
                url: ajaxUrl,
                method: "POST",
                data: parameters,
            }).done(function(data){
                $this.removeAttr('readonly');
                response($.map(data.businesses, function (item) {
                    var address = '';
                    var sep = '';
                    for(i = 0; i < item.location.display_address.length; i++) {
                        address += sep + item.location.display_address[i];
                        sep = ', ';
                    }
                    var label = '<div><img src="' + item.image_url + '" style="width: 60px; height: 60px; float: left;">'
                        + '<div style="margin-left: 65px;"><div>' + item.name + '(' + item.id + ')' + '</div><div>' + address + '</div></div>';
                    // var label = '<div><div>' + item.name + '(' + item.id + ')' + '</div><div>' + address + '</div></div>';
                    return {
                        id: item.id,
                        label: label,
                        value: item.name + '(' + item.id + ')'
                    };
                }));
                $this.autocomplete('widget').show();
            }).fail(function(error){
                $this.removeAttr('readonly');
                console.dir(error);
                console.log("Error occured while searching");
            });
        },
        minLength: 1,
        select: function( event, ui ) {
            if(ui.item) {
                selectedItem = ui.item;
            }
        },
        open: function() {
            $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
            $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        },
        response: function () {
            $( this ).autocomplete().data("ui-autocomplete").cancelSearch = false;
        },
        create: function (event,ui) {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                return $( "<li class='test'></li>" )
                    .data( "item.autocomplete", item )
                    .append( item.label)
                    .appendTo( ul );
            };
        }
    }).click(function () {
        $(this).autocomplete("widget").show();
    });
    $('#user-setting-yp-business-name').autocomplete("widget").css('z-index', 2000);

    $('#yelpModal').on('hide.bs.modal', function (event) {
        $('#user-setting-yp-business-name').autocomplete("widget").hide();
    });

    $('.yelp-modal-confirm').click(function() {
        if(selectedItem != null)
            $('#user-setting-yp-business-id').val(selectedItem.id);
    })

    // General Order
    $('.draggable-element').arrangeable();

    $('#tab-order-container').on('drag.end.arrangeable', function() {
        var orders = [];
        $('#tab-order-container .draggable-element').each(function(idx, el) {
            orders.push($(el).data('key'));
            //orders.push(el.data)
        });
        $('#tab_orders').val(orders.join(','));
    });
});

