


// $('#form-resumemedis').on('submit',function(e){
//     e.preventDefault();
// 	let dataResume = $('#form-resumemedis').serialize();
// 	$(this).docoForm('submit',{
//         url: "/ranap/pemeriksaan-rawat-inap/resumemedis-simpan",
//         success: function(){
//             var btn_cetak = $('#cetak-resume-medis');
//             if(btn_cetak.hasClass('hidden')){
//                 btn_cetak.removeClass('hidden');
//             }
//         }
//     });
// })
$('#resumemedisform-kondisi_lain').hide();
$('#carakeluar_id').on('change', function(){
    var valuedata = $(this).val();
    //  console.log(valuedata);
    if (valuedata) {
        $.ajax({
            type: 'GET',
            url: '/ranap/end-point/get-data-kondisi-keluar?carakeluar_id='+valuedata,
            success: function(response){
                var select = $('#kondisipulang_id');
                select.children().remove();
                $('#kondisipulang_id').append($('<option>', { value : '' }).text('-- Pilih --'));
                $.each(response.result, function(index, item) {
                    $('#kondisipulang_id').append($('<option>', { value : item.id }).text(item.text).attr('data-carakeluar_id',item.parent));
                });
                $("#kondisipulang_id").prop("disabled", false);
            }
        });

        if(valuedata == 2 || valuedata == 6){
            $('#resumemedisform-kondisi_lain').show();
        }else{
            $('#resumemedisform-kondisi_lain').hide();
            $('#resumemedisform-kondisi_lain').val('');
        }
    }
});
$(document).ready(function(){
    if($('#kondisipulang_id option:selected').val() != ''){
        $("#kondisipulang_id").prop("disabled", false);
        var carakeluar_id = $('#kondisipulang_id option:selected').data('carakeluar_id');
        $('#carakeluar_id').val(carakeluar_id);

        if(carakeluar_id == 2 || carakeluar_id == 6){
            $('#resumemedisform-kondisi_lain').show();
        }else{
            $('#resumemedisform-kondisi_lain').hide();
            $('#resumemedisform-kondisi_lain').val('');
        }
    }

    var selectDiagMasuk = $('#diag_masuk').data('select2');
    selectDiagMasuk.on('results:message', function(params){
      this.dropdown._resizeDropdown();
      this.dropdown._positionDropdown();
    });

    var selectDiagUtama = $('#resumemedisform-diag_utama').data('select2');
    selectDiagUtama.on('results:message', function(params){
      this.dropdown._resizeDropdown();
      this.dropdown._positionDropdown();
    });

    var selectDiagPenyerta = $('#resumemedisform-diag_penyerta').data('select2');
    selectDiagPenyerta.on('results:message', function(params){
      this.dropdown._resizeDropdown();
      this.dropdown._positionDropdown();
    });

    var selectProsedurDiag = $('#resumemedisform-prosedur_diag').data('select2');
    selectProsedurDiag.on('results:message', function(params){
      this.dropdown._resizeDropdown();
      this.dropdown._positionDropdown();
    });

    $('#save-resume-medis').on('click',function(){
        // console.log('cek');
        let dataResume = $('#form-resumemedis').serializeArray();
        $(this).docoForm('click',{
            data: dataResume,
            url: "/ranap/pemeriksaan-rawat-inap/resumemedis-simpan",
            success: function(){
                location.reload();
                var btn_cetak = $('#cetak-resume-medis');
                if(btn_cetak.hasClass('hidden')){
                    btn_cetak.removeClass('hidden');
                }
            }
        });
    });


    $('#cetak-resume-medis').on('click',function(e){
        e.preventDefault();
        var btn_save = $('#save-resume-medis');
        btn_save.addClass('hidden');
        var url="/ranap/pemeriksaan-rawat-inap/cetak-pdf-resume-medis?id="+pendaftaran_id+"&pasien_id="+pasien_id;
        window.open(url, '_blank');
    });

});
