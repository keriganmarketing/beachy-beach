/**
 * Created by Bryan on 5/23/2017.
 */
$(document).ready(function() {
    var $lightbox = $('#lightbox');

    $('#myCarousel').carousel({
        interval: false,
        ride: false
    });

    $('[data-target="#lightbox"]').on('click', function(event) {
        var $img = $(this).find('img'),
            //src = $img.attr('src'),
            target = $img.attr('data-slide-to'),
            css = {
                //'width': '100%',
                //'maxWidth': $(window).width() - 0,
                //'maxHeight': $(window).height() - 0

                'width': '1200px',
                'maxWidth': '100%',
                //'maxHeight': $(window).height() - 0
            };

        $lightbox.find('.close').addClass('hidden');
        //$lightbox.find('img').attr('src', src);
        //$lightbox.find('img').attr('alt', alt);
        $lightbox.find('img').css(css);

        console.log(target);
        $('#myCarousel').carousel(Number(target));

    });

});