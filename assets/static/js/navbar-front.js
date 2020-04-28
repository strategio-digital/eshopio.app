/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $(document).on('click', '#navbar-toggler', function () {
        if ($(this).hasClass('active') === false) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }

        $('#navbar').toggle();
    })
});