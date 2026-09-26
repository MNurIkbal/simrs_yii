$('#list-'+type).on('change', function(){
    if ($(this).val() == "") {
        $('#pilih-'+type).attr('disabled', true)
        $('#hapus-template-'+type).attr('disabled', true)
    } else {
        $('#pilih-'+type).attr('disabled', false)
        $('#hapus-template-'+type).attr('disabled', false)
    }
});
$('#list-'+type).trigger('change')

$('#hapus-template-'+type).click(function(e){
    e.preventDefault();
    var temp =  $('#list-'+type).val();
    $(this).docoForm('click',{
        url: '/mcu/pemeriksaan/hapus-template?id='+temp,
        confirmTitle : 'Konfirmasi',
        confirmMessage : 'Apakah anda yakin ingin menghapus data ini ?',
        success : function (data) {
            docoNotification('success', 'Proses Berhasil', data.message);
            $('#'+tab).trigger('click');
        }
    });
})

$('#pilih-'+type).click(function(e){
    e.preventDefault();
    var temp =  $('#list-'+type).val();
    $(this).docoForm('click',{
        url: '/mcu/pemeriksaan/pilih-template?id='+temp+'&pendaftaran_id='+id,
        confirmTitle : 'Konfirmasi',
        confirmMessage : 'Apakah anda yakin ingin menggunakan data ini ?',
        success : function (data) {
            $('#'+tab).trigger('click');
            if(type != 'phr' && type != 'status-kesehatan'){
                $('.print').attr('disabled', false)
            }
        }
    });
})

$(document).ready(function () {
    var data = $("#"+id_form).serialize();
    $("#"+id_form).on('change', function(event){
        event.preventDefault();
        var currState = $("#"+id_form).serialize();
        
        if (data == currState) {
            $('#save-template-'+type).attr('disabled', true)
        } else {
            $('#save-template-'+type).attr('disabled', false)
        }
    });

    $('#list-'+type).select2({
        ajax: {
            url: '/mcu/pemeriksaan/list-template?type='+detail_type,
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
        },
    });
});