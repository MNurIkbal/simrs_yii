$(document).ready(function() {
    renderPickadate($('#importadjustmentform-tanggal'), {
         dependElementPicker: $('#btnDatePick'),
         minDate: convertDateByFormat(new Date(), 'Y-M-d'),
         defaultValue: new Date(),
     });

    $('#migrasi-adjustment-obat-alkes').submit(function(event) {
        $(this).docoForm("submit", {
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            method: 'post',
            isUpload: true,
            success: function(data){

            },
            error: function(){

            }
        })
    });
});