setIndicatorPosition();

$('#slide-kamar').carousel({
    pause: 'false'
});
$(window).on('scroll',function(){
    var scrollBottom = $(document).height() - $(window).height() - $(window).scrollTop();
    if(scrollBottom < 15) {
        show = true;
        $('.carousel-indicators').css({'z-index': '9999'});
    } else{
        $('.carousel-indicators').css({'z-index': '1'});
    }
    
}).trigger('scroll');

$(window).on('resize', function(){
    if((window.fullScreen) || (window.innerWidth == screen.width && window.innerHeight == screen.height)){
      $('html').addClass('overflow');
      $('.carousel-indicators').css({'z-index': '9999'});
      setIndicatorPosition();
    } else {
        setIndicatorPosition();
        $('html').removeClass('overflow');
    }
});

function setIndicatorPosition(){
    var footer = $('.navbar-fixed-bottom').outerHeight();
    var position = Math.ceil(parseInt((footer) / 2)) + 'px';
    $('.carousel-indicators').css({'bottom': position});
}

$.getJSON("./../../json/setup.json", function (config) {
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(config.ip+':'+config.port);
    }
    socket.on('display-dashboard-kamar-' + config.name, function (data) {
        var return_data = JSON.parse(data);
            
        $.each(return_data.data, function(key,value){
            if (key == 'min') {
                $.each(value, function(idx, val) {
                    var total = parseInt($('#kamar-'+val).text());
                    total = total - 1;
                    if (total == 0) {
                        $('#kamar-' + val).addClass('empty');            
                    } else {
                        $('#kamar-' + val).removeClass('empty');            
                    }
                    $('#kamar-'+val).html(total);
                });
            } 

            if (key == 'add') {
                $.each(value, function(idx, val) {
                    var total = parseInt($('#kamar-'+val).text());
                    total = total + 1;
                    if (total == 0) {
                        $('#kamar-' + val).addClass('empty');            
                    } else {
                        $('#kamar-' + val).removeClass('empty');            
                    }
                    $('#kamar-'+val).html(total);
                });
            } 

            if (key == 'reload') {
                location.reload();
            }
        });
    });
});

// $.getJSON("./../../json/setup.json", function (config) {
//     if (config.origin == "true") {
//         var socket = io.connect(window.location.origin);
//     } else {
//         var socket = io.connect(config.ip+':'+config.port);
//     }
//     console.log('clte');
//     socket.on('display-dashboard-kamar-' + config.name, function (data) {
//         console.log('clicked');
//         var return_data = JSON.parse(data);
//         $.each(return_data.data, function(key,value){
//             if (typeof value.min && min !== 'undefined') {
//                 $.each(value.min, function(idx, val){
//                     var total = parseInt($('#kamar-'+val).text());
//                     total = total - 1;
//                     if (total == 0) {
//                         $('#kamar-' + val).addClass('empty');            
//                     } else {
//                         $('#kamar-' + val).removeClass('empty');            
//                     }
//                     $('#kamar-'+val).html(total);
//                 });   
//             }
//         });
//     });
// });