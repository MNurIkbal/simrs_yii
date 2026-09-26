;(function ($) {
    function extractMessage(payload, fallback) {
        if (!payload) {
            return fallback;
        }

        if (typeof payload === 'string') {
            return payload || fallback;
        }

        return payload.message ||
            payload.text ||
            (payload.metadata && payload.metadata.message) ||
            (payload.response && (
                payload.response.message ||
                payload.response.text ||
                (payload.response.metadata && payload.response.metadata.message)
            )) ||
            fallback;
    }

    // Auto-fill nama dokumen based on file name
    $(document).on('change', '#dokumen_file', function () {
        var file = this.files[0];
        var namaInput = $('#nama_dokumen');
        if (file && namaInput.length && !namaInput.val()) {
            var baseName = file.name.replace(/\.[^/.]+$/, '');
            namaInput.val(baseName);
        }
    });

    $(document).on('click', '#btn-submit-dokumen', function (e) {
        e.preventDefault();
        console.log('check');

        var form = $('#form-upload-dokumen');
        if (!form.length) {
            docoNotification('error', 'Gagal', 'Form upload tidak ditemukan.');
            return;
        }

        var uploadUrl = form.data('url');
        if (!uploadUrl) {
            docoNotification('error', 'Gagal', 'URL upload tidak tersedia.');
            return;
        }

        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        var submitBtn = $(this);
        var formData = new FormData(form[0]);

        submitBtn.prop('disabled', true).html('<b><i class="fa fa-spinner fa-spin"></i></b> Mengirim...');

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var message = extractMessage(response, 'Dokumen berhasil dikirim.');
                docoNotification('success', 'Berhasil', message);
                setTimeout(function () {
                    $('#modal_backdrop').modal('hide');
                    window.location.reload();
                }, 1200);
            },
            error: function (xhr) {
                var resp = xhr.responseJSON || {};
                var message = extractMessage(resp, 'Dokumen gagal dikirim.');
                docoNotification('error', 'Gagal', message);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<b><i class="fa fa-upload"></i></b> Kirim Dokumen');
            }
        });
    });
})(jQuery);
