/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function() {
    $('[data-scroll-to]').on('click', function () {
        var target = $(this).data('scroll-to');
        var $target = $(target);

        if ($target.length === 1) {
            //$('#core-navbar').collapse('hide');
            $([document.documentElement, document.body]).animate({
                scrollTop: $target.offset().top //- 40 // 64 is navbar
            }, 500);
        }
    });
});