
$('.bpjs-panel').hide();
$('#form-update-ranap').docoForm('submit', {
    success: function (response) {
        this.formInput[0].reset();
        $('#modal_backdrop').modal('hide');
        $('.data-filter').click();
    }
});

$('#carabayar_id').change(function () {
    if ($('#carabayar_id option:selected').text() == 'BPJS') {
        $('.bpjs-panel').slideDown();
    } else {
        $('.bpjs-panel').slideUp();
    }
});


$('.cek').click(function () {
    alert($('#BpjsForm_politujuan').val());
});

