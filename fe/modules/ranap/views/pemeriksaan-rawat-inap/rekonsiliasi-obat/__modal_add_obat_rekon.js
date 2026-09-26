$('#add-obat-form').submit(function(e) {
    e.preventDefault();

    data = $(this).serializeArray();

    $(this).docoForm("submit", {
        data: data,
        success: function (response) {
            var response = response.response;
            var option = new Option(response.obatalkes_nama, response.obatalkes_id, true, true);
            $("#obatalkes_id").append(option);
            $('#obatalkes_id').val(response.obatalkes_id).trigger('change');
            $('#modal_backdrop').modal('hide');
        }
    });
});


$('.btn-kode').on('click', function(){
    $.ajax({
        url: '/ranap/pemeriksaan-rawat-inap/get-kode',
        beforeSend: function(){
            $('.kode-oa').val('Harap tunggu....')
            $('.kode-oa').attr('readonly', true)
            $(this).attr('disabled', true)
        },
        success: function(data){
            $('.kode-oa').val(data)
            $('.kode-oa').attr('readonly', false)
            $(this).attr('disabled', false)
        }
    })
})

$(document).ready(function(){
    $('.btn-kode').click();
});