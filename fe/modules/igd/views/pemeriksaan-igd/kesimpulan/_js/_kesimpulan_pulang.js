$('#tgl_lanjut_rawat').change(function() {
    // console.log($(this).val());
    $.ajax({
        url: '/igd/end-point/get-jadwal-poli',
        type: 'POST',
        data: {tanggal:$(this).val()},
        success: function(response){
            $('#poliklinik_id').empty();
            var options = new Option('--Pilih--', null, false, false);
            $('#poliklinik_id').append(options);

            if(response){
                var data = [];
                $.each(response, function(i, val) {
                    data = {id:val.ruangan_id, text:val.ruangan_nama};
                    var options = new Option(data.text, data.id, false, false);
                    $('#poliklinik_id').append(options);
                });
                $("#poliklinik_id").trigger('change');
            }else{
                new PNotify({
                    title: 'Terjadi Kesalahan',
                    text: response,
                    addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                    type: 'error'
                });
            }
        }
    });
});
