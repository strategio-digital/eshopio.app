/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $.nette.ext('bootstrap-tooltip', {
        load: function () {
            $('.tooltip').remove();
            $('[data-tooltip="tooltip"]').tooltip('dispose').tooltip();
        }
    });

    $('.modal').on('show.bs.modal', function (event) {
        var $button = $(event.relatedTarget);
        var title = $button.data('modal-title');

        if (typeof title !== 'undefined') {
            $(this).find('.modal-title').text(title);
        }
    });

    $(document).on('change', '.custom-file-input', function (event) {
        var files = $(this).prop('files');
        var names = $.map(files, function(val) { return val.name; });

        var $label = $(this).parent().find('.custom-file-label');
        if (names.length > 1) {
            $label.text('Vybrali jste ' + names.length + ' soubory');
        } else if (names.length === 1) {
            if (names[0].length > 43) {
                names[0] = names[0].substring(0, 20) + "..." + names[0].substring(names[0].length - 20, names[0].length)
            }
            $label.text(names[0]);
        } else {
            $label.text('Vybrat soubor...')
        }
    });
});
