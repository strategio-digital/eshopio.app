/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$.nette.ext('notifications', {
    success: function (payload, status, jqXHR, settings) {
        var notifications = payload.notifications;

        if (typeof notifications !== 'undefined') {
            $('#notifications').prepend(notifications);
        }
    }
});
