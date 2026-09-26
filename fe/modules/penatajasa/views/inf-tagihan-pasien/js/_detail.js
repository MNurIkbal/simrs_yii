/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


// $(document).on('click','.btn-delete-tindakan',function (e) {
//     e.preventDefault();
//     $.ajax({
//         url: $(this).attr('data-url')+'&pendaftaran_id='+pendaftaran_id,
//         success : function (res) {
//             table.draw();
//             $('#modal_backdrop').modal('toggle');
//         }
//     });
// });

$(document).on('click','.btn-delete-penjamin', function(e){
    e.preventDefault();
    var _obj = $(this);
    $(this).docoForm('click', {
        url: _obj.attr('data-url') + '&pendaftaran_id='+pendaftaran_id,
        skipConfirm: true,
        beforeSend: function(){
            $(this).prop('disabled', true).html('<b><i class=\'fa fa-gear fa-spin\'></i></b>');
        },
        success: function(response){
            data_audit(pendaftaranID);
            tblPenjamin.ajax.url('get-list-penjamin?pendaftaran_id='+ pendaftaran_id +'&get_session=true').load();
            $(this).prop('disabled', false).html('<b><i class=\'fa fa-trash fa-xs\'></i></b>');
        },
        error: function(err){
            $(this).prop('disabled', false).html('<b><i class=\'fa fa-trash fa-xs\'></i></b>');
        }

    })
});

$(document).on('click', '#close-hapus-tindakan', function(event) {
    event.preventDefault();
    var form = $("#remove-tindakan-form");
    form[0].reset();
    $("#modal-remove").modal('toggle');
});

var _sumTotal = function(){
    var _totalTagihan = docoHelper.convertToAngka($('#total_tagihan').val());
        _totalDijamin = docoHelper.convertToAngka($('#subsidi_asuransi').val());
        _total = _totalTagihan - _totalDijamin;
    $('#tagihan_pasien').val( docoHelper.convertToRupiah(_total < 0 ? 0 : _total) );
    // $('#nominal_dijamin').val(docoHelper.convertToRupiah(_totalTagihan)).trigger('change');
    $('#nominal_dijamin').val(docoHelper.convertToRupiah(_total < 0 ? 0 : _total)).trigger('change');
}

// data_audit(pendaftaranID);

function data_audit(pendaftaran_id){
    $.getJSON(baseUrl+"penatajasa/inf-tagihan-pasien/data-audit?id="+pendaftaran_id, function(res){
        if(res == true){
            window.onbeforeunload = confirmExit;
            function confirmExit()
            {
                return "Do you want to leave this page without saving ?";
            }
        }
    });
}
