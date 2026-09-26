var arrData = [];
var stat = 0;

$.ajaxSetup({
  cache:false
});

$(document).ready(function() {
    setInterval(clockUpdate, 1000);
});

function clockUpdate() {
    var date = new Date();
    function addZero(x) {
        if (x < 10) {
            return x = '0' + x;
        } else {
            return x;
        }
    }

    function twelveHour(x) {
        if (x > 12) {
            return x = x - 12;
        } else if (x == 0) {
            return x = 12;
        } else {
            return x;
        }
    }

    var h = addZero(twelveHour(date.getHours()));
    var m = addZero(date.getMinutes());
    var s = addZero(date.getSeconds());

    $('#time').text(h + ':' + m)
}

$(document).ready(function () {
    function anim() {
        var part = 1;
        var i = setInterval(() => {
            part++;
            var className = document.getElementsByClassName('part-'+part)
            if(className.length == 0) {
                clearInterval(i);
                location.reload();
            } else {
                $(`.part-${part}`).fadeIn(1000)
               $(`.part-${part}`).removeClass("hide")
               $(`.part-${part - 1}`).fadeOut()
               $(`.part-${part - 1}`).addClass("hide")
            }
        }, 15000)
    }
    anim();
});
