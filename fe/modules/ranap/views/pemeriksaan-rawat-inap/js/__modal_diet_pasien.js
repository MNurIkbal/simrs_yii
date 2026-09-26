$(() => {

    $('#btn-save-diet-pasien').bind('click', () => {
        let _form = $('#diet-pasien-form').serializeArray()
        
        $().docoForm('click', {
            data: _form,
            skipScrollUp: false,
            url: '/ranap/pemeriksaan-rawat-inap/save-diet-pasien?id='+pendaftaran_id,
            success: function(data) {
                tabel.draw()
                $('#modal-lab').find('.close').click();
            }
        })
    })
})