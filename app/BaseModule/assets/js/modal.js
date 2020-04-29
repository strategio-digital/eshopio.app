/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$.nette.ext('modals', {
    success: function (payload, status, jqXHR, settings) {
        var toggleModal = payload.toggleModal;

        if (typeof toggleModal !== 'undefined') {
            $('#' + toggleModal.element).modal(toggleModal.toggle)
        }
    }
});
