/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $(document).on('click', '.input-quantity span', function () {
        var $input = $(this).parents('.input-quantity').find('input');

        var direction = $(this).hasClass('decrease') === true ? -1 : 1;
        var value = parseInt($input.val());
        var result = value + direction;

        if (result >= parseInt($input.attr('min')) && result <= parseInt($input.attr('max'))) {
            $input.val(value + direction);
        }
    })
});