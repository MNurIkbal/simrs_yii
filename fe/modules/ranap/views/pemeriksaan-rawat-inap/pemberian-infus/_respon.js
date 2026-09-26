$(document).ready(function(){
    $('#save-respon-infus').on('click', function(e) {
        e.preventDefault()
        let payload = $('#respon-infus-form').serializeArray()

        $().docoForm("click", {
            skipScrollUp: false,
            url: $('#respon-infus-form').attr('action'),
            data: payload,
            success: function (data) {
                $('.close-modal-respon').trigger('click')
                reloadForm()
            },
        });
    })
})