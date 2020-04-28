/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $('#kontaktni-formular').on('show.bs.modal', function (event) {
        var $btn = $(event.relatedTarget);
        var ctaText = $btn.data('cta-text');

        if (typeof ctaText === 'undefined') {
            ctaText = 'Domluvit se na oslovení';
        }

        $(this).find('#cta-input').val(ctaText);
        $(this).find('#cta-button, #cta-title').text(ctaText);
    })
});