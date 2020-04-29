/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    var gallery = {
        enabled: true,
        tPrev: 'Předchozí', // title for left button
        tNext: 'Další', // title for right button
        tCounter: '<span class="mfp-counter">%curr% / %total%</span>'
    };

    /*$('[data-mfp-image]').magnificPopup({
        type:'image'
    });

    $('[data-mfp-gallery]').each(function () {
        $(this).magnificPopup({
            type: 'image',
            delegate: 'a',
            gallery: gallery
        });
    });*/

    $('[data-mfp-owl]').each(function () {
        var $mfp = $(this);

        $mfp.on('click', '.owl-item.cloned a', function (event) {
            event.preventDefault();
            var self = $(this);

            $mfp.find('.owl-item:not(.cloned) a').each(function (index) {
                if ($(this).attr('href') === self.attr('href')) {
                    $mfp.magnificPopup('open', index);
                }
            });
        });

        $mfp.magnificPopup({
            type: 'image',
            delegate: '.owl-item:not(.cloned) a',
            gallery: gallery
        });
    });
});