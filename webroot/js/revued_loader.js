rJQuery(document).ready(function(rJQuery){
    var holder = rJQuery("#revued-placeholder");

    if (holder.data("base") !== undefined && holder.data("id") !== undefined) {
        __revued_base = holder.data("base");
        __revued_id = holder.data("id");

        var url = "//revued.com/revued-setting/cors.php?userId=" + __revued_id;
        //var url = "http://localhost/revued/cors.php?userId=" + __revued_id;

        var success = function(data){
            data = rJQuery.parseJSON(data);

            if (data[0].status == 'active' || data[0].status == 'trial') {
                runRevued(holder, data[0]);
            }

            if(data[0].custom_settings != null && data[0].custom_settings.close_color != undefined) {
                rJQuery('#revued-close').css('color', data[0].custom_settings.close_color);

                if(data[0].custom_settings.btn_size != undefined) {
                    rJQuery( "#revued-btn-container" ).find('button').addClass('revued-btn-' + data[0].custom_settings.btn_size);
                }
            }
        };

        rJQuery.ajax({
            type: 'GET',
            url: url,
            dataType: "jsonp",
            crossDomain: true,
            cache:false,
            success: success,
            error:function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
            }
        });
    }

    function runRevued(holder, settings) {
        if (holder.data("formheight") !== undefined) {
            __revued_height = holder.data("formheight");
        } else {
            __revued_height = 510;
        }

        if (holder.data("formurl") !== undefined) {
            __revued_url = holder.data("formurl");
        }

        var revued_iframe = rJQuery('<iframe id="revued_iframe" height="' + __revued_height + '" allowTransparency="true" frameborder="0" scrolling="no" src="'+ __revued_url +'"><a href="'+ __revued_url +'">View Data</a></iframe>');
        holder.append(revued_iframe);

        if (settings.display_mode == 'float') {
            rJQuery('head').append('<link rel="stylesheet" type="text/css" href="' + __revued_base + 'css/revued-float.css">');

            /* Create Button for Floater */
            var open = rJQuery('<div>');
            open.attr('id', 'revued-btn-container').append('<button>');
            rJQuery('body').append(open);

            var close = rJQuery('<a href="#" id="revued-close" aria-label="Close Revued Box">&times;</a>');
            rJQuery("#revued-placeholder").prepend(close);

            rJQuery( "#revued-btn-container button" ).click(function() {
                if ( ! holder.is(':visible')) {
                    holder.fadeIn();
                    rJQuery( "#revued-close" ).fadeIn();
                    rJQuery( "#revued-btn-container" ).fadeOut();
                }
            });

            rJQuery( "#revued-close" ).click(function() {
                if (holder.is(':visible')) {
                    holder.fadeOut();
                    rJQuery( "#revued-close" ).fadeOut();
                    rJQuery( "#revued-btn-container" ).fadeIn();
                }

                return false;
            });
        }
    }
});
