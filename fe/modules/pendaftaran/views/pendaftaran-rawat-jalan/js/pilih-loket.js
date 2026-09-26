$(document).on('click', '.btn-loket', function (e) {
    e.preventDefault();

    $(this).docoForm('click', {
        data: {
            'loket_id': $(this).attr('data-loket-id'),
        },
        success: function (data) {
            window.location.href = '/pendaftaran/pendaftaran-rawat-jalan/index';
        }
    });
    
    return false;
});