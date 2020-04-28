/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function ()
{
    // Prevent Bootstrap dialog from blocking focusin
    $(document).on('focusin', function(e) {
        if ($(e.target).closest('.mce-window').length !== 0) {
            e.stopImmediatePropagation();
        }
    });

    $(document).on('focusin', '.tiny_mce', function () {
        var id = $(this).attr('id');
        tinymce.get(id).focus()
    });

    $.nette.ext('mce', {
        load: function () {
            tinymce.remove();
            tinymce.init({
                selector: '.tiny-mce',
                readonly: true,
                skin: false,
                content_css: false,
                language: 'cs',
                mode: 'textareas',
                paste_as_text: true,
                entity_encoding: 'raw',
                height: 500,
                relative_urls : false,
                remove_script_host : false,
                menubar: false,
                nonbreaking_force_tab: true,
                allow_unsafe_link_target: true,
                //block_formats: 'H1=h1;H2=h2;H3=h3;Paragraph=p;',
                block_formats: 'Paragraph=p;',
                content_style: '*{font-family:Ubuntu,sans-serif} table{width:100%} p,td,th,li{color:#666} table,td{border: 1px solid #ccc}}',
                plugins: [
                    ['textcolor link lists code table paste']
                ],
                toolbar: [
                    'bold italic underline forecolor | link table | bullist numlist | code'
                ],
                invalid_styles: {
                    'table': 'width height border-collapse',
                    'tr': 'width height',
                    'th': 'width height',
                    'td': 'width height'
                },
                setup: function (editor) {
                    var disabled = $('#' + editor.id).prop('disabled');
                    editor.settings.readonly = disabled;

                    editor.on('keyup change blur', function () {
                        editor.save();
                        //var $form = $(editor.getElement()).parents('form');
                        var textarea = document.getElementById(editor.id);

                        Nette.validateControl(textarea);
                    });
                }
            })
        }
    })
});