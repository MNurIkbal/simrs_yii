function pilihanLoket() {

}
// console.log(redirect);
$(document).on('click', '.btn-loket', function (e) {
    e.preventDefault();
    /*$(this).docoForm('click', {
        data: {
            'loket_id': $(this).attr('data-loket-id'),
        },
        success: function (data) {
            window.location.href = '/antrian/panggil-antrian/index?param=' + $('#param').val();
        }
    });*/
    var post_url = $(this).attr('action');
    var loket_id = $(this).attr('data-loket-id');
    $.post(post_url, {
            loket_id: loket_id
        },
        function (data, status) {
            // alert("Data: " + data + "\nStatus: " + status);
            var url = '/antrian/panggil-antrian/index?param=' + $(this).attr('data-loket-id');
            window.location.href = url;
        });

    // var url = '/antrian/panggil-antrian/index?param=' + $(this).attr('data-loket-id');
    // window.location.href = url;
    // console.log(url);
    // return false;
});