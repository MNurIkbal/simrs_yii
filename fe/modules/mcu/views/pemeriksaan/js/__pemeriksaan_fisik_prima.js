
var tmpHasil = {};
$(document).ready(function () {
  $('.pemeriksaan-fisik').trigger('click');
  $('.select2').select2();
  $('.bagian-tubuh-gigi').select2();
  $('.bagian-tubuh-abdomen').select2();
  $('.bagian-tubuh-kulit').select2();
});

var saveAnatomi = function (data) {
    let res = data;
    pemeriksaanfisikmcu_id = res.response['pemeriksaanfisikmcu_id'] ? res.response['pemeriksaanfisikmcu_id'] : null;
    let url = "/mcu/pemeriksaan/save-anatomi";
    var saveHasil = [];

    $.each(tmpHasil, function(index,value){
      $.each(value, function(index, data){
        saveHasil.push(data)
      });
    });
    
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : url,
        data : {
            data_anatomi : saveHasil,
            pendaftaran_id : pendaftaran_decrypt,
            pasien_id : pasien_id,
            pemeriksaanfisikmcu_id : pemeriksaanfisikmcu_id,
        },
        error : function (data) {
            console.log(data);
        }
    });
};
// /*----------  Anatomi tubuh end  ----------*/

$("#form-pemeriksaan-fisik-prima").on("submit", function(e){
    $(this).docoForm("submit",{
        success : function(data) {
            saveAnatomi(data);
            $('.print').attr('disabled', false)
        },
        error : function (data) {
            console.log(data);
        }
    });
})


$(document).on("click", ".pemeriksaan-fisik", function () {
  var persepsi_mata = document.getElementById("opt_persepsi_mata2");
  var konjungtiva_kanan = document.getElementById("opt_konjungtiva_kanan3");
  var konjungtiva_kiri = document.getElementById("opt_konjungtiva_kiri3");
  var bentuk_torax = document.getElementById("opt_bentuk_torax2");
  var bunyi_jantung = document.getElementById("opt_bunyi_jantung3");
  var batas_kiri_jantung = document.getElementById("opt_batas_kiri_jantung1");
  var cervical = document.getElementById("opt_cervical1");
  var extremitas_atas = document.getElementById("extremitas_atas1");
  var lumbar = document.getElementById("opt_lumbar1");
  var extremitas_bawah = document.getElementById("opt_extremitas_bawah1");
  var extremitas_atas_tes = document.getElementById("opt_extremitas_atas_tes1");
  var extremitas_bawah_tes = document.getElementById("opt_extremitas_bawah_tes1");

  if(persepsi_mata.checked == true){
    $('#pemeriksaanfisiknewform-persepsi_mata_note').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-persepsi_mata_note').prop('disabled',true);
    $('#pemeriksaanfisiknewform-persepsi_mata_note').val(null).trigger("change")
  }

  if(konjungtiva_kanan.checked == true){
    $('#pemeriksaanfisiknewform-note_konjungtiva_kanan').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_konjungtiva_kanan').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_konjungtiva_kanan').val(null).trigger("change")
  }

  if(konjungtiva_kiri.checked == true){
    $('#pemeriksaanfisiknewform-note_konjungtiva_kiri').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_konjungtiva_kiri').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_konjungtiva_kiri').val(null).trigger("change")
  }

  if(bentuk_torax.checked == true){
    $('#pemeriksaanfisiknewform-note_bentuk_torax').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_bentuk_torax').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_bentuk_torax').val(null).trigger("change");
  }

  if(bunyi_jantung.checked == true){
    $('#pemeriksaanfisiknewform-note_bunyi_jantung').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_bunyi_jantung').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_bunyi_jantung').val(null).trigger("change");
  }

  if(batas_kiri_jantung.checked == true){
    $('#pemeriksaanfisiknewform-note_batas_kiri_jantung').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_batas_kiri_jantung').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_batas_kiri_jantung').val(null).trigger("change");
  }

  if(cervical.checked == true){
    $('#pemeriksaanfisiknewform-note_cervical').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_cervical').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_cervical').val(null).trigger("change");
  }

  if(extremitas_atas.checked == true){
    $('#pemeriksaanfisiknewform-note_extremitas_atas').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_extremitas_atas').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_extremitas_atas').val(null).trigger("change");
  }

  if(lumbar.checked == true){
    $('#pemeriksaanfisiknewform-note_lumbar').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_lumbar').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_lumbar').val(null).trigger("change");
  }

  if(extremitas_bawah.checked == true){
    $('#pemeriksaanfisiknewform-note_extremitas_bawah').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_extremitas_bawah').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_extremitas_bawah').val(null).trigger("change");
  }

  if(extremitas_atas_tes.checked == true){
    $('#pemeriksaanfisiknewform-note_extremitas_atas_tes').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_extremitas_atas_tes').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_extremitas_atas_tes').val(null).trigger("change");
  }

  if(extremitas_bawah_tes.checked == true){
    $('#pemeriksaanfisiknewform-note_extremitas_bawah_tes').prop('disabled',false);
  }else{
    $('#pemeriksaanfisiknewform-note_extremitas_bawah_tes').prop('disabled',true);
    $('#pemeriksaanfisiknewform-note_extremitas_bawah_tes').val(null).trigger("change");
  }
});