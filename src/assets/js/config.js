(function ($) {
    $(function () {
        $('.config-option_update').on('click', function (e) {
            e.preventDefault();
            var that = $(this);
            var span = $('span', this);
            var parentRow = that.parents('.config-option_row');
            var inputs = parentRow.find('.config-option_hidden-input');
            var names = parentRow.find('.config-option_name');

            if (that.hasClass('is-edit')) {
                if (span.hasClass('glyphicon-ok')) {
                    span.removeClass('glyphicon-ok').addClass('glyphicon-pencil');
                }
                inputs.hide(10);
                names.show(10);
                that.removeClass('is-edit');
            } else {
                if (span.hasClass('glyphicon-pencil')) {
                    span.removeClass('glyphicon-pencil').addClass('glyphicon-ok');
                }
                names.hide(10);
                inputs.show(10);
                that.addClass('is-edit');
            }
        });

        var ctrlIsPressed = false;

        $(window).keydown(function(event){
            if(event.keyCode == 17) {
                ctrlIsPressed = true;
                event.preventDefault();
            }
        });

        $(window).keyup(function(event){
            if(event.keyCode == 17) {
                ctrlIsPressed = false;
                event.preventDefault();
            }
        });

        $(window).on('keydown', function(e) {
            if (ctrlIsPressed && event.keyCode === 83) {
                event.preventDefault();
                $('#config-form').submit();
                return;
            }
        });
    });
})(jQuery)