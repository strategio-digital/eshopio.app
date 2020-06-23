/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    $(document).on('click', '.input-radio', function (event) {
        event.preventDefault();
        $(this).find('input[type="radio"]').prop('checked', true);
    })
});