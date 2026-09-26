$(document).ready(function () {
    var readmore = $('#c_klinis').html();
    var lessmore = readmore.substr(0, 112);
    if (readmore.length > 150) {

        $('#c_klinis').html(lessmore).append("<a href='' class='read-more-link'>Tampilkan Lebih ...</a>");

    } else {
        $('#c_klinis').html(readmore);
    }

    $("body").on("click", ".read-more-link", function (event) {
        event.preventDefault();
        $(this).parent('#c_klinis').html(readmore).append("<a href='' class='show-less-link' >Sembunyinkan ...</a>");
    });
    $("body").on("click", ".show-less-link", function (event) {
        event.preventDefault();
        $(this).parent('#c_klinis').html(readmore.substr(0, 112)).append("<a href='' class='read-more-link' >Tampilkan Lebih ...</a>");
    });
});