$(function () {
    $(document).on({
        ajaxStart: function () {
            $(".sg-loading").show();
        },
        ajaxStop: function () {
            $(".sg-loading").hide();
        }

    });
});

