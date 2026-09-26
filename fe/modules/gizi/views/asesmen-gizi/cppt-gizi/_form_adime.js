$('.edit-adime').on("click", function(e){
    $(this).parent().find('.simpan-adime').removeClass('hidden')
    $(this).parent().find('.batal-adime').removeClass('hidden')    
    $(this).addClass('hidden')
    $(this).parent().parent().find('.form-adime').removeClass('hidden')
    $(this).parent().parent().find('.hasil-asesmen').addClass('hidden')
});

$('.batal-adime').on("click", function(e){
    $(this).addClass('hidden')
    $(this).parent().find('.simpan-adime').addClass('hidden')
    $(this).parent().find('.edit-adime').removeClass('hidden')    
    $(this).parent().parent().find('.form-adime').addClass('hidden')
    $(this).parent().parent().find('.hasil-asesmen').removeClass('hidden')
});

$('.simpan-adime').on("click", function(e){
    let form = $("#form-adime-" + $(this).data('pagt_id'))
    showLoader('Memproses Data ...')
    $.ajax({
        url: form.prop('action') + '?id=' + pendaftaran_id,
        method: 'POST',
        data: form.serializeArray(),
        success: (response) => {
            docoNotification('success', 'Proses berhasil', 'ADIME berhasil disimpan')
            hideLoader()
            $('#tab-cppt-gizi').click();
        },
        error: (response) => {
            docoNotification('error', 'Proses gagal', 'ADIME gagal disimpan')
            hideLoader()
        }
    });
});