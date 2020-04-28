/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () { $.nette.ext({ load: function () {

    var $showcase = $('#owl-showcase').owlCarousel({
        items: 1,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000
    });

    $showcase.on('changed.owl.carousel', function () {
        $showcase.trigger('stop.owl.autoplay');
        $showcase.trigger('play.owl.autoplay');
    });

    var $testimonial =  $('#owl-testimonial').owlCarousel({
        items: 3,
        loop: true,
        autoplay: true,
        autoplayTimeout: 5500,
        //dotsContainer: '#owl-hp-gallery-dots',
        dots: false,
        margin: 30,
        responsive:{
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            }
        }
    });

    $testimonial.on('changed.owl.carousel', function () {
        $testimonial.trigger('stop.owl.autoplay');
        $testimonial.trigger('play.owl.autoplay');
    });

    // Handle onclick next, prew
    $(document).on('click', '.next, .prev', function () {
        var $carousel = $(this).parent().find('.owl-carousel');
        var direction = $(this).hasClass('prev') ? 'prev' : 'next';
        $carousel.trigger(direction + '.owl.carousel');
    });
}})});