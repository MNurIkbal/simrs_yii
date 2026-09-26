$('#btn-history-resep').unbind();
$('#btn-history-resep').bind('click', () => {
    const { href, width } = $('#btn-history-resep').data()
    showLoader('Memuat Halaman...')
    $('#modal_riwayat').find('.modal-dialog').css('width', width)
    $('#modal_riwayat .modal-content').docoLoad({
        url: href,
        dataType: 'html',
        success: function (data) {
            hideLoader()
            $('#modal_riwayat .modal-content').parents('.modal').modal('show')
        },
        error: function () {
            hideLoader()
        }
    })
});