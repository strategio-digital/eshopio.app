/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 * DŮLEŽITÉ:
 *
 *      Na začátku zdrojových souborů nepoužívat podtržítko, GULP tyto soubory ignoruje.
 *      V názech souborů nepoužávat tečky, GULP tyto soubory ignoruje.
 *
 *      Špatně:     './assets/static/scss/_test.test.ext'
 *      Správně:    './assets/static/scss/test_test.ext'
 *
 */

module.exports = function ()
{
    return {

        js:
        [
            // Node - jQuery
            './node_modules/jquery/dist/jquery.js',

            // Node - Popper
            './node_modules/popper.js/dist/umd/popper.js',

            // Bootstrap 4
            './node_modules/bootstrap/js/dist/index.js',
            './node_modules/bootstrap/js/dist/util.js',
            //'./node_modules/bootstrap/js/dist/carousel.js',
            //'./node_modules/bootstrap/js/dist/alert.js',
            './node_modules/bootstrap/js/dist/button.js',
            './node_modules/bootstrap/js/dist/collapse.js',
            './node_modules/bootstrap/js/dist/modal.js',
            //'./node_modules/bootstrap/js/dist/popover.js',
            //'./node_modules/bootstrap/js/dist/scrollspy.js',
            './node_modules/bootstrap/js/dist/dropdown.js',
            './node_modules/bootstrap/js/dist/toast.js',
            './node_modules/bootstrap/js/dist/tooltip.js',
            //'./node_modules/bootstrap/js/dist/tab.js',

            // Bootstrap
            './app/BaseModule/assets/js/bootstrap.js',

            // Nette Ajax
            './node_modules/nette.ajax.js/nette.ajax.js',

            // Button Ajax
            './app/BaseModule/assets/js/button-ajax.js',

            // Nette Live validation
            './app/BaseModule/assets/js/live-validation.js',
            './node_modules/live-form-validation/live-form-validation.js',

            // Notification
            './app/BaseModule/assets/js/notification.js',

            // Modal
            './app/BaseModule/assets/js/modal.js',

            // Tinymce,
            './node_modules/tinymce/tinymce.js',
            './node_modules/tinymce/themes/silver/theme.js',
            './node_modules/tinymce/plugins/textcolor/plugin.js',
            './node_modules/tinymce/plugins/lists/plugin.js',
            './node_modules/tinymce/plugins/code/plugin.js',
            './node_modules/tinymce/plugins/link/plugin.js',
            './node_modules/tinymce/plugins/table/plugin.js',
            './node_modules/tinymce/plugins/paste/plugin.js',
            './app/BaseModule/assets/js/tinymce/cs.js',
            './app/BaseModule/assets/js/tinymce/tinymce.js',

            // Magnific popup
            './node_modules/magnific-popup/dist/jquery.magnific-popup.js',
            './app/BaseModule/assets/js/magnific-popup.js',

            // Nette init
            './app/BaseModule/assets/js/nette.init.js'
        ],

        file:
        [
            { from: './assets/static/img/**/*', to: '/img' },
            { from: './node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.*', to: '/font' },
            { from: './node_modules/@fortawesome/fontawesome-free/webfonts/fa-regular-400.*', to: '/font' },
            //{ from: './node_modules/@fortawesome/fontawesome-free/webfonts/fa-brands-400.*', to: '/font' }
        ],

        scss:
        [
            // Zde načítat pouze tento zaváděcí soubor!
            './assets/static/scss/backend.scss',
        ]

    };
}();
