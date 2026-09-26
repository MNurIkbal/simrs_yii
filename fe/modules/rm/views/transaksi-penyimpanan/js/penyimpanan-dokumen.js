/*
    Author : aweutist
*/

	$(function() {
        const tgl_akhir = $('.tgl_akhir').val();
        let yesterday = new Date(tgl_akhir);
        yesterday.setDate(yesterday.getDate() - 1);

        const pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
            format: 'dd mmmm yyyy',
            disable: [{
                from: [0, 0, 0],
                to: yesterday
            }],
            onStart: function () {
                var date = new Date();
                this.set('select', tgl_akhir)
            }
        });
    });

    $("#btn-save").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#penyimpanan-form").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            success: function (data) {
                setTimeout(function () {
                    window.location.href = "/rm/inf-dokumen"
                }, 1000);
            }
        });
    });

    $('#btn-ulang').on('click', function () {
        location.reload();
    });

    const no_rak = $('#no_rak').val();
    const no_sub_rak = $('.no_sub_rak').val();
    $('#no_rak').val(no_rak).trigger('change');
    setTimeout(function () {
        $('#subrak').val(no_sub_rak).trigger('change');
    }, 1000);
