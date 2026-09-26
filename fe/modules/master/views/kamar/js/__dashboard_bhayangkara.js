(function($) {
    "use strict"; // Start of use strict


    $(document).ready(function() {
        // $("#loader").hide();
        $("#afterLoad").fadeIn('slow');
        // jQuery for page scrolling feature - requires jQuery Easing plugin
        $('a.page-scroll').bind('click', function(event) {
            event.preventDefault();
            var $anchor = $(this);
           /* $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top - 50)
            }, 100, 'easeInOutExpo');*/

            /*$('html, body').stop().animate({
                'scrollTop': $anchor.offset().top-100
              }, 500, function () {
                window.location.hash = $anchor;
            });*/
            var el = $( this.getAttribute('href') );
            var offs = el.offset();
            $('html, body').stop().animate({ scrollTop: offs.top-100 },500); 

        });

        // Highlight the top nav as scrolling occurs
        $('body').scrollspy({
            target: '.navbar-fixed-top',
            offset: 51
        });

        // Closes the Responsive Menu on Menu Item Click
        $('.navbar-collapse ul li a').click(function(){ 
                $('.navbar-toggle:visible').click();
        });

        // Offset for Main Navigation
        $('#mainNav').affix({
            offset: {
                top: 100
            }
        })

        $("html, body").animate({ scrollTop: $('#groupKelas-1').offset().top - 50 }, 2000);
        
        var part = 2;
        var i = setInterval(function() {
            console.log(part);
            if(document.getElementById('groupKelas-'+part) == null) {
                $("#loader").fadeIn('slow');
                $("#afterLoad").fadeOut('slow');
                
                clearInterval(i);
                // setVisible('.page', true);
                // setVisible('#loading', false);
                location.reload();
            } else {
                part++;
                $("html, body").animate({ scrollTop: $('#groupKelas-'+part).offset().top - 50 }, 2000);
            }

        }, 10000);
        
        function showDetail(id_kamar) {
            alert(id_kamar);
        }
    });

})(jQuery); // End of use strict
/*function setVisible(selector, visible) {
  document.querySelector(selector).style.display = visible ? 'block' : 'none';
}*/