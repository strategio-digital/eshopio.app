/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
$(function ()
{
    var BUTTON_SELECTOR = '[data-ajax-button]';

    $.nette.ext('progress-button', {
        load: function () {
            $(BUTTON_SELECTOR).unbind('click');

            $(BUTTON_SELECTOR).on('click', function (event) {
                event.preventDefault();

                var $button = $(this);
                var buttonHtml = $button.html();
                var showPercentage = $button.data('wakers-progress-button') === 'show-percentage';

                var request = {
                    type: null,
                    url: null,
                    data: null,
                };

                switch ($(this).prop('nodeName')) {
                    case 'A':
                        request.type = 'GET';
                        request.url = $($button).attr('href');

                        break;

                    default:
                        var $form = $button.parents('form');
                        var formData = new FormData($form[0]);

                        request.data = formData;
                        request.url = $form.attr('action');
                        request.data = formData;

                        break;
                }

                $.nette.ajax({
                    //cache: false,
                    //contentType: false,
                    processData: false,

                    start: function () {
                        $button.addClass('disabled');
                        $button.attr('disabled', true);
                        $button.html(buttonHtml);
                    },

                    success: function () {
                        $button.removeClass('disabled');
                        $button.attr('disabled', false);
                        $button.html(buttonHtml);
                    },

                    error: function () {
                        $button.removeClass('disabled');
                        $button.attr('disabled', false);
                        $button.html(buttonHtml);
                    },

                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();

                        if(myXhr.upload) {
                            myXhr.upload.addEventListener('progress', function (event) {
                                if(event.lengthComputable && showPercentage) {
                                    var percentage = (event.loaded * 100) / event.total;

                                    $button.text(Math.round((percentage * 100) / 100) + '%');
                                }

                            }, false);
                        }

                        return myXhr;
                    }

                }, this, event);
            });
        }
    });
});
