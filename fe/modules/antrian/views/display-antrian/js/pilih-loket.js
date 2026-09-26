function pilihanLoket() {

}
console.log(redirect);
$(document).on('click', '.btn-loket', function (e) {
    e.preventDefault();
    $(this).docoForm('click', {
        data: {
            'loket_id': $(this).attr('data-loket-id'),
        },
        success: function (data) {
            if (redirect == "" ){
                window.location.href = '/antrian/display-antrian/panggil-antrian?param=' + $('#param').val();
            }else{
                window.location.href = '/'+redirect ;
            }
        }
    });
    return false;
});