$(function() {
    $("#header-color").change(function() {
        $('#revued_iframe').contents().find('.reviews-heading').css('background-color', $(this).val());
    });

    $("#footer-color").change(function() {
        $('#revued_iframe').contents().find('.brand').css('background-color', $(this).val());
    });

    $("#background-color").change(function() {
        $('#revued_iframe').contents().find('.reviews-main').css('background-color', $(this).val());
    });

    $("#close-color").change(function() {
        $('#revued-close').css('color', $(this).val());
    });
});
