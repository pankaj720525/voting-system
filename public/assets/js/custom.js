$(function() {
    "use strict";

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),
        utcOffset: new Date().getTimezoneOffset() },
    });


    $(document).ready(function() {
        $(".alert-block").fadeTo(3000, 500).slideUp(500, function() {
            $(".alert-block").slideUp(500);
        });
    });
});
