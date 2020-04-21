$(function () {
    $('.js-tabs').on('shown.bs.tab', function () {
       $(this).find('.js-tab-more').show();
    });
    $('.js-tabs').on('hidden.bs.tab', function () {
        $(this).find('.js-tab-more').hide();
    });

    var ctrlIsPressed = false;

    $(window).keydown(function (e) {
        if (e.keyCode == 17) {
            ctrlIsPressed = true;
            e.preventDefault();
        }
    });

    $(window).keyup(function (e) {
        if (e.keyCode == 17) {
            ctrlIsPressed = false;
            e.preventDefault();
        }
    });

    $(window).on('keydown', function (e) {
        if (ctrlIsPressed && e.keyCode === 83) {
            e.preventDefault();
            var form = $('form').get(0);
            $(form).submit();
            return;
        }
    });

    $('.js-config-type').on('change', function () {
        var value = $(this).val();
        if (window.location.href.indexOf('?')) {
            var url = window.location.href + '&type=' + value;
        } else {
            var url = '?type=' + value
        }
        $.pjax.reload('#config-value-input', {
            timeout: false,
            replace: true,
            url: url
        });
    });
});
$(function () {
    $('form.has-ajax').on('click', 'button[type="submit"]', function (e) {
        e.preventDefault();
        var form = $(this).parents('form');
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: new FormData(form.get(0)),
            processData: false,
            contentType: false,
            success: function (r) {
                if (r.success) {
                    $.pjax.reload('#config-options');
                    form.parents('.modal').modal('hide');
                    $('input[type="text"], input[type="password"], textarea', form).val('');
                } else {
                    console.log(r.errors);
                }
            }
        });
    });
});

$('.js-tab-edit').on('click', function (e) {
    e.preventDefault();
    var modal = $('<div>', {
        id: 'file-edit-modal',
        class: 'modal fade',
        tabindex: -1,
        role: 'dialog',
        'aria-hidden': 'true',
        style: 'display: none'
    });
    $.ajax({
        url: $(this).attr('href'),
        success: function (r) {
            modal.append(r);
            $(document.body).append(modal);
            modal.modal();
            modal.on('hide.bs.modal', function () {
                $(this).remove();
            });
        }
    });
});

function sort(e, ui) {
    var item = $(ui.item[0]);
    var ul = item.parents('ul');
    var tabs = $('li', ul);
    var items = [];
    tabs.each(function (l, i) {
        items.push({
            i: $(i).index() + 1,
            id: $(i).find('.nav-link').data('id')
        });
    });
    $.ajax({
        url: ul.data('sort'),
        data: {items: JSON.stringify(items)},
        method: 'post',
        success: function (r) {
            if (!r.success) {
                $('#sortable-tabs').sortable("cancel");
                console.log(r.errors);
            }
        },
        error: function (xhr, status) {
            $('#sortable-tabs').sortable("cancel");
            console.log(status);
        }
    });
}
