$(document).ready(() => {
    let _modalElement = $('#modal-batal-instruksi');
    _modalElement.find('#batal-terapi-form').unbind();
    _modalElement.find('#batal-terapi-form').bind('submit', function(e) {
        e.preventDefault();
        let _formData = $(this).serializeArray();
        _modalElement.modal('toggle');
        $().docoForm('click', {
            url: $(this).attr('action'),
            method: "POST",
            type: "json",
            data: _formData,
            success: function (res) {
                tbLaporanTindakan.draw();
            }
        })
    });
})
