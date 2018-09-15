/**
 * Google Place API
 */




var autocomplete = null;

function geocodePlaceId(geocoder, map, infowindow, infowindowContent, marker) {
    var placeId = $('#user-setting-gp-place-id').val();
    var request = {
        location: map.getCenter(),
        radius: '0',
        query: placeId
    };



    geocoder.geocode({'placeId': placeId}, function(results, status) {
        if (status === 'OK') {
            if (results[0]) {
                var place = results[0];
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.

                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });

                marker.setPlace({
                    placeId: place.place_id,
                    location: place.geometry.location
                });
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                infowindowContent.children['place-address'].textContent = place.formatted_address;
                //infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);
            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    });
}


function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -33.8688, lng: 151.2195},
        zoom: 13
    });

    var input = document.getElementById('user-setting-gp-url');

    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var autocomplete = new google.maps.places.Autocomplete(input);

    // Bind the map's bounds (viewport) property to the autocomplete object,
    // so that the autocomplete requests use the current map bounds for the
    // bounds option in the request.
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var infowindowContent = document.getElementById('infowindow-content');
    infowindow.setContent(infowindowContent);
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    autocomplete.setTypes([]);

    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindowContent.children['place-icon'].src = place.icon;
        infowindowContent.children['place-name'].textContent = place.name;
        infowindowContent.children['place-address'].textContent = address;
        infowindow.open(map, marker);


        console.log(place);
        $('#user-setting-gp-place-id').val(place.place_id);
        $('#user-setting-gp-reference').val(place.reference);
        //$('#user-setting-gp-url').val(place.name);

    });



    if($('#user-setting-gp-place-id').val() != '') {
        // Initialize map with google place ID
        var placeId = $('#user-setting-gp-place-id').val();
        var geocoder = new google.maps.Geocoder;
        geocoder.geocode({'placeId': placeId}, function(results, status) {
            if (status === 'OK') {
                console.log(results);
                if (results[0]) {
                    for(i = 0; i < results.length; i++){
                        var place = results[i];
                        console.log(place);

                        /*marker.setPlace({
                            placeId: place.place_id,
                            location: place.geometry.location
                        });*/

                        marker.setPosition(place.geometry.location);

                        // If the place has a geometry, then present it on a map.
                        if (place.geometry.viewport) {
                            map.fitBounds(place.geometry.viewport);
                        } else {
                            map.setCenter(place.geometry.location);
                            map.setZoom(17);  // Why 17? Because it looks good.
                        }
                        marker.setPosition(place.geometry.location);
                        marker.setVisible(true);
                        google.maps.event.addListener(marker, 'click', function() {
                            infowindow.open(map,marker);
                        });
                        infowindowContent.children['place-address'].textContent = place.formatted_address;
                        infowindow.open(map,marker);
                    }

                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }


        });
    }
}

$(function(){


    $('a[href="#tabs-3"]').click(function() {
        $('.map-area').appendTo('#map-wrapper');
        $('.map-area').css('position','relative');
        $('.map-area').css('left','0');
        $('.map-area').css('width','100%');
        $('#map').css('width','100%');
        google.maps.event.trigger(map,'resize');

    });

    $('#user-setting-gp-url').keypress(function (e) {
        if(e.which == 13) {
            e.preventDefault();
            return false;
        }
    });
});

