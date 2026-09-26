
$(document).ready(function () {
    var $inputMulai = $(".jam_awal").pickatime({
        format: "HH:i",
    
    });
    var pickerMulai = $inputMulai.pickatime('picker');
    
    
    var $inputSelesai = $(".jam_akhir").pickatime({
        format: "HH:i",
        onOpen: function () {
            var selectedTime = pickerMulai.get('select');
            if (selectedTime) {
                this.set('min',
                    [selectedTime.hour, selectedTime.mins]
                );
                if (pickerMulai.get('disable').length > 0) {
                    var firstDis = this.get('disable')[0];
                    var lastDis = this.get('disable')[this.get('disable').length - 1];
                    this.set('max', false);
                    if (selectedTime.hour <= firstDis[0]) {
                        this.set('max', firstDis);
                    } else {
                        var selesai = $('#jadwalbukapoli_id').find(":selected").data('jam_selesai');
                        this.set('max', selesai);
                    }
                }
            }
        }
    });
    var pickerSelesai = $inputSelesai.pickatime('picker');

    $("#btn-save").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#shift-form").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            success: function (data) {
                setTimeout(function () {
                    window.location.href = "/master/shift"
                }, 1000);
            }
        });
    });
});

$('#btn-ulang').on('click', function () {
    location.reload();
});

$('#btn-kembali').on('click', function () {
    window.location.href = "/master/shift"
});

$('.shift_nama').on('keyup', function() {
    $('.shift_namalainnya').val($(this).val());
});