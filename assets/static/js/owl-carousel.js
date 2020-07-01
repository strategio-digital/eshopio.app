/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () { $.nette.ext({ load: function () {

    var $product = $('#owl-product').owlCarousel({
        items: 1,
        loop: false,
        dots: false
    });

    var $productThumbnail = $('#owl-product-thumbnail').owlCarousel({
        items: 4,
        margin: 16,
        loop: false,
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            992 : {
                items: 3
            },
            1400 : {
                items: 4
            }
        }
    });

    $productThumbnail.on('mouseover', '.owl-item', function () {
        var current = $(this).find('a').data('position');

        $(this).parent().find('.owl-item').each(function (index, element) {
            var $target = $(element).find('a');
            $target.removeClass('current');

            if (current === $target.data('position')) {
                $target.addClass('current')
                $product.trigger('to.owl.carousel', [current, 150])
            }
        })
    });

    // Handle onclick next, prew
    $(document).on('click', '.next, .prev', function () {
        var $carousel = $(this).parent().find('.owl-carousel');
        var direction = $(this).hasClass('prev') ? 'prev' : 'next';
        $carousel.trigger(direction + '.owl.carousel');
    });
}})});