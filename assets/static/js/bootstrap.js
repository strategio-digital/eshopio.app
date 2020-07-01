/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $('.filter').collapse('show');

    $.nette.ext('bootstrap-tooltip', {
        load: function () {
            $('.tooltip').remove();
            $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();
        }
    });
});