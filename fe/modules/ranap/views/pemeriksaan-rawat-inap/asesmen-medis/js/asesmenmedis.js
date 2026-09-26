/*
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-11 15:05:48
 */

$(".input-tags").tagsinput();
$(" .select2 ").select2();

$(document).ready(function () {
  $('.allo_or_auto-radio').change(function() {
      var dependentFieldId = $(this).data('dependent').id;
      var dependentField = $('#' + dependentFieldId);

      if ($(this).val() == '1') {
        dependentField.attr("readonly", false);
      } else {
        $(".sumber-hubungan").val("");
        dependentField.attr("readonly", true);
      }
  });

  $('.allo_or_auto-radio:checked').trigger('change');
  if (data_sumber_info == 1) {
    $("#allo_or_auto-0-asmed").prop("checked", true);
    $("#allo_or_auto-1-asmed").prop("checked", false);
    $('#allo_or_auto-0-asmed').trigger('change');
    $(".sumber-hubungan").val("");
    $(".sumber-hubungan").attr("readonly", true);
  }

  if (data_sumber_info_lainnya == 1) {
    $("#allo_or_auto-1-asmed").prop("checked", true);
    $("#allo_or_auto-0-asmed").prop("checked", false);
    $('#allo_or_auto-1-asmed').trigger('change');
    $(".sumber-hubungan").attr("readonly", false);
  }
  if($(`input[name="AsesmenMedisForm[mata]"]:checked`).val() == 0){
    $(".mata-lainnya").attr("readonly", false);
  }else{
    $(".mata-lainnya").attr("readonly", true);
  }

  if (is_disabled) {
    $("#asesmenmedis-form :input").prop("disabled", true);
  }
  /*----------  Populate riwayat penyakit terdahulu start  ----------*/
  if (!jQuery.isEmptyObject(dataRiwayatPenyakitDahulu)) {
    $.each(dataRiwayatPenyakitDahulu, function (key, value) {
      var _clone = $(".clone-div")
        .clone()
        .removeClass("clone-div hidden");

      _clone.find("input, select").each(function (kx, vx) {
        if ($(this).hasClass("select-tahun")) {
          $(this).val(value.tahun).select2().trigger("change");
        } else if ($(this).attr("data-name") == "penyakit") {
          $(this).val(value.penyakit);
        } else if ($(this).attr("data-name") == "terapi") {
          $(this).val(value.terapi);
        }

        $(this).attr("disabled", "disabled");
      });

      _clone.find("button").each(function (ky, vy) {
        $(this).remove();
      });

      $(".isi-loop").find(".row-default").before(_clone);
    });
  }
  /*----------  Populate riwayat penyakit terdahulu start  ----------*/

  /*----------  Populate penyakit terdahulu start  ----------*/
  if (!jQuery.isEmptyObject(dataPenyakitDahulu)) {
    $.each(dataPenyakitDahulu, function (k, v) {
      if (k == 0) {
        $(".row-default")
          .find("input, select")
          .each(function (kx, vx) {
            if ($(this).hasClass("select-tahun")) {
              $(this).val(v.tahun).trigger("change");
            } else if ($(this).hasClass("penyakit-default")) {
              $(this).val(v.penyakit);
            } else if ($(this).hasClass("terapi-default")) {
              $(this).val(v.terapi);
            }
          });
      } else {
        count++;
        var _clone = $(".template-riwayat-penyakit-dahulu")
          .clone()
          .addClass("row-data row-" + count)
          .removeClass("clone-div template-riwayat-penyakit-dahulu hidden");
          _clone.find('.btn-append').remove();
        _clone.find("input, select").each(function (kx, vx) {
          if ($(this).hasClass("select-tahun")) {
            $(this)
              .addClass("select-tahun" + count)
              .removeClass("select-tahun");
            $(this).val(v.tahun).select2();
          } else if ($(this).attr("data-name") == "penyakit") {
            $(this).val(v.penyakit);
          } else if ($(this).attr("data-name") == "terapi") {
            $(this).val(v.terapi);
          }
          $(this).attr(
            "name",
            "riwayat_penyakit[" + count + "][" + $(this).attr("data-name") + "]"
          );
        });

        $(".wrapper-riwayat-penyakit-dahulu").append(_clone);
      }
    });
  }
  /*----------  Populate penyakit terdahulu start  ----------*/

  addRow();
  var opsi = detailBagianTubuh[$(".bagian-tubuh").val()];
  $(".bagian-tubuh-detail").append(populateOpsi(opsi));
  var opsi_luka_bakar = detailBagianTubuh[$(".bagian-tubuh-luka-bakar").val()];
  $(".bagian-tubuh-detail-luka-bakar").append(populateOpsi(opsi_luka_bakar));
  var opsi_anak = detailBagianTubuh[$(".bagian-tubuh-anak").val()];
  $(".bagian-tubuh-detail-anak").append(populateOpsi(opsi_anak));
  var opsi_anak_luka_bakar =  detailBagianTubuh[$(".bagian-tubuh-luka-bakar-anak").val()];
  $(".bagian-tubuh-detail-luka-bakar-anak").append(populateOpsi(opsi_anak_luka_bakar));

  if ($('input[name="AsesmenMedisForm[is_merokok]"]:checked').val() == 0) {
    $(".jml-rokok").val("");
    $(".jml-rokok").attr("readonly", true);
  }else{
    $(".jml-rokok").attr("readonly", false);
  }

  if ($('input[name="AsesmenMedisForm[kategori_asmed]"]:checked').val() == 1) {
    $('.riwayat-persalinan').addClass('hide')
    $('.riwayat-tumbuh-kembang').addClass('hide')
    $('.status-lokalis-anak').addClass('hide')
    $('.status-lokalis-dewasa').removeClass('hide')

    $('.field-asesmenmedisform-r_peskk').removeClass('hide')
    $('.field-asesmenmedisform-is_merokok').removeClass('hide')
    $('.jumlah-batang-rokok').removeClass('hide')
    $('.field-asesmenmedisform-is_merokok_pasif').removeClass('hide')
    $('.field-asesmenmedisform-r_kelahiran').removeClass('hide')
  }else{
    $('.riwayat-persalinan').removeClass('hide')
    $('.riwayat-tumbuh-kembang').removeClass('hide')
    $('.status-lokalis-anak').removeClass('hide')
    $('.status-lokalis-dewasa').addClass('hide')

    $('.field-asesmenmedisform-r_peskk').addClass('hide')
    $('.field-asesmenmedisform-is_merokok').addClass('hide')
    $('.jumlah-batang-rokok').addClass('hide')
    $('.field-asesmenmedisform-is_merokok_pasif').addClass('hide')
    $('.field-asesmenmedisform-r_kelahiran').addClass('hide')
  }

  hitungGcs();
  KategoriTd();
  kategoriNadi();
  pageFormId = $("#asesmenmedis-form :not([readonly]):not('.disable-get-change')"); // get form id page | declare di pemeriksaan-rawat-inap/js/index js
  pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksaan-rawat-inap/js/_index js

  $(".stepy-navigator").prepend("<span class='draft mr-3' style='color:red;display:none'>Draft : anda harus menyimpan terlebih dulu</span>")

  if (is_draft) {
    $(".draft").show()
  } else {
    $(".draft").hide()
  }

  $("#asesmenmedis-form").on("change", () => {
    saveChanges()
  })
});

var populateOpsi = function (opsi) {
  var txtopsi = "";
  $.each(opsi, function (k, v) {
    txtopsi += '<option value="' + k + '">' + v + "</option>";
  });
  return txtopsi;
};

// Show hide discharge planning
$('input[type=radio][name="AsesmenMedisForm\\[discharge_plan\\]"]').on(
  "change",
  function () {
    // Cek value
    if ($(this).val() == "0") {
      // Show
      $(".tabbable .nav-tabs #tab-dischargeplan").hide();
    } else {
      // hide
      $(".tabbable .nav-tabs #tab-dischargeplan").show();
    }
  }
);

/*----------  Submit form start  ----------*/
$("#asesmenmedis-form").on("submit", function (e) {
  e.preventDefault();
  var class_image = $('.image-frame img');
  for (i in tmpData) {
    if(tmpData[i]['jenis_gambar_id'] == 2){
      class_image = $('.image-frame-luka-bakar img');
    }else if(tmpData[i]['jenis_gambar_id'] == 3){
      class_image = $('.image-frame-anak img');
    }else if(tmpData[i]['jenis_gambar_id'] == 4){
      class_image = $('.image-frame-luka-bakar-anak img');
    }else {
      class_image = $('.image-frame img');
    }
    tmpData[i].height = class_image.height();
    tmpData[i].width = class_image.width();
  }

  var luka_bakar_form = {};
  if ($('input[name="AsesmenMedisForm[kategori_asmed]"]:checked').val() == 1) {
    luka_bakar_form = $('.luka_bakar').serializeArray()[0];
  }else{
    luka_bakar_form = $('.luka_bakar').serializeArray()[1];
  }
  var asesmen = $("#asesmenmedis-form").serializeArray().concat(luka_bakar_form);

  var arr = [];
  arr[0] = { name: "anatomi", value: JSON.stringify(tmpData) };
  var alldata = $.merge(asesmen, arr);
  
  $(this).docoForm("submit", {
    data: alldata,
    before: function () {
      return false;
    },
    success: function (response) {
      let { data } = response;
      //   location.reload();
      // $("#patient-history-tab")
      //   .find(".riwayat-penyakit-keluarga-text")
      //   .text(data.riwayat_penyakit_keluarga);
      // $("#patient-history-tab")
      //   .find(".status-merokok-text")
      //   .text(data.status_merokok ? "Ya" : "-");
      pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksaan-rawat-inap/js/_index js
      
      $("#patient-history-tab").trigger("click");
      $("#tab-asesmenmedis a").trigger("click");
      removeDisable();
    },
    complete: () => {
      hideLoader();
    },
  });
});

// Event Delete
$('.btn-hapus').on("click", function (event) {
  // Prevent default
  event.preventDefault();

  // Asesmen id
  var asesmenmedis_id = $("#asesmenmedisform-asesmenmedis_id").val();

  // Delete
  $(this).docoForm("delete", {
    additional: "data-rm",
    success: function (data) {
      location.reload();
    },
  });
});

// Active tab
function activaTab(tab) {
  $('.nav-tabs a[href="#' + tab + '"]').tab("show");
}

/*----------  Submit form end  ----------*/

var loading_spinner =
  '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';

$('input[name="AsesmenMedisForm[is_merokok]"]').on("change", function () {
  if ($('input[name="AsesmenMedisForm[is_merokok]"]:checked').val() == 0) {
    $("#asesmenmedisform-jumlah_rokok").val("");
    $(".jml-rokok").attr("readonly", true);
  }else{
    $(".jml-rokok").attr("readonly", false);
  }
});

$('input[name="AsesmenMedisForm[kategori_asmed]"]').on("change", function () {
  if ($('input[name="AsesmenMedisForm[kategori_asmed]"]:checked').val() == 1) {
    $('.riwayat-persalinan').addClass('hide')
    $('.riwayat-tumbuh-kembang').addClass('hide')
    $('.status-lokalis-anak').addClass('hide')
    $('.status-lokalis-dewasa').removeClass('hide')
    $('.field-asesmenmedisform-r_peskk').removeClass('hide')
    $('.field-asesmenmedisform-is_merokok').removeClass('hide')
    $('.jumlah-batang-rokok').removeClass('hide')
    $('.field-asesmenmedisform-is_merokok_pasif').removeClass('hide')
    $('.field-asesmenmedisform-r_kelahiran').removeClass('hide')
  }else{
    $('.riwayat-persalinan').removeClass('hide')
    $('.riwayat-tumbuh-kembang').removeClass('hide')
    $('.status-lokalis-anak').removeClass('hide')
    $('.status-lokalis-dewasa').addClass('hide')
    $('.field-asesmenmedisform-r_peskk').addClass('hide')
    $('.field-asesmenmedisform-is_merokok').addClass('hide')
    $('.jumlah-batang-rokok').addClass('hide')
    $('.field-asesmenmedisform-is_merokok_pasif').addClass('hide')
    $('.field-asesmenmedisform-r_kelahiran').addClass('hide')
  }
});


// $('input[name="AsesmenMedisForm[discharge_plan]"]').on('change', function(){
//     if($(this).val() == 1){

//     }else{
//         $('.care-plan').val('')
//     }
// })

$('input[name="AsesmenMedisForm[sumber_info_lainnya]"]').on(
  "change",
  function () {
    if ($(this).is(":checked") == false) {
      $(".sumber-hubungan").val("");
      $(".sumber-hubungan").attr("readonly", true);
    } else {
      $(".sumber-hubungan").attr("readonly", false);
    }
  }
);
/*----------  Append new riwayat penyakit form on click + start  ----------*/
var count = 0;
// $(document).on('click','.btn-tambah-riwayat-penyakit-dahulu', function(){

// });

function tambahRiwayatDahulu() {
  count++;

  var _clone = $(".clone-asesmen-medis-div")
    .clone()
    .addClass("row-" + count)
    .removeClass("clone-asesmen-medis-div hidden");
  _clone.find("input, select").each(function (k, v) {
    if ($(this).hasClass("select-tahun")) {
      $(this).addClass("select-tahun" + count);
      setTimeout(function () {
        $(".select-tahun" + count).select2();
      }, 1);
    }
    $(this).attr(
      "name",
      "riwayat_penyakit[" + count + "][" + $(this).attr("data-name") + "]"
    );
  });

  $(".isi-loop").append(_clone);
}

$(document).on("click", ".btn-remove-riwayat-penyakit-dahulu", function () {
  $(this).closest(".row").remove();
  saveChanges();
});
/*----------  Append new riwayat penyakit form on click + end  ----------*/

/*----------  IMT start  ----------*/
$(".imt_field").keyup(function (e) {
  // Allow: backspace, delete, tab, escape, enter and .
  if (
    $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
    // Allow: Ctrl+A, Command+A
    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: home, end, left, right, down, up
    (e.keyCode >= 35 && e.keyCode <= 40)
  ) {
    // return;
  }
  // Ensure that it is a number and stop the keypress
  if (
    (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
    (e.keyCode < 96 || e.keyCode > 105)
  ) {
    e.preventDefault();
  }

  let tb = $("#asesmenmedisform-tinggi_badan").val()
    ? parseFloat($("#asesmenmedisform-tinggi_badan").val())
    : null;
  let bb = $("#asesmenmedisform-berat_badan").val()
    ? parseFloat($("#asesmenmedisform-berat_badan").val())
    : null;
  let kategori_imt = $("#asesmenmedisform-ket_imt");
  let field_imt = $("#asesmenmedisform-imt");
  let field_bbideal = $("#asesmenmedisform-bb_ideal");
  let bbideal = 0.0;
  let imt = 0.0;
  let imt_kategori = "";

  // hitung bmi / imt
  if (bb && tb) {
    imt = (bb / ((tb / 100) * (tb / 100))).toFixed(2);
    if(getBulan >= 216){
      $.each(dataBmi, function (index, value) {
        if (
          parseFloat(imt) >= parseFloat(value.bmi_minimum) &&
          parseFloat(imt) <= parseFloat(value.bmi_maksimum)
        ) {
          imt_kategori = value.bmi_defenisi;
          return false;
        }
      });
    }else{
      $.each(dataBmiAnak, function (index, value) {
        if (
          parseFloat(imt) >= parseFloat(value.bmi_minimum) &&
          parseFloat(imt) <= parseFloat(value.bmi_maksimum) &&
          value.jenis_kelamin == jeniskelamin_id &&
          getBulan >= value.umur_awal &&
          getBulan <= value.umur_akhir
        ) {
          imt_kategori = value.bmi_defenisi;
          return false;
        }
      });

    }


    // hitung berat badan ideal
    if (jeniskelamin == "Laki-laki") {
      bbideal = parseFloat(tb - 100 - 0.1 * (tb - 100)).toFixed(2);
    } else {
      bbideal = parseFloat(tb - 100 - 0.15 * (tb - 100)).toFixed(2);
    }
  } else if (!bb && !tb) {
    imt = ''
    imt_kategori = ''
    bbideal = ''
  }

  field_imt.val(imt.toString().replace('.', ','));
  kategori_imt.val(imt_kategori);
  field_bbideal.val(bbideal.toString().replace('.', ','));
});
/*----------  IMT end  ----------*/

/*----------  Tekanan Darah start  ----------*/
var nilaiTd = "";

$("#asesmenmedisform-td_diastolic").on("change", function () {
  if ($("#asesmenmedisform-td_systolic").val() != "") {
  let diastolic = $("#asesmenmedisform-td_diastolic").val() != "" ? $("#asesmenmedisform-td_diastolic").val() : 0;
  let systolic = $("#asesmenmedisform-td_systolic").val() != "" ? $("#asesmenmedisform-td_systolic").val() : 0;
  $(".tekanan-darah")
    .val(systolic + "/" + diastolic)
    .trigger("change");
  nilaiTd = systolic + "/" + diastolic;
  }
});
$("#asesmenmedisform-td_systolic").on("change", function () {
  if ($("#asesmenmedisform-td_diastolic").val() != "") {
    let diastolic = $("#asesmenmedisform-td_diastolic").val() != "" ? $("#asesmenmedisform-td_diastolic").val() : 0;
    let systolic = $("#asesmenmedisform-td_systolic").val() != "" ? $("#asesmenmedisform-td_systolic").val() : 0;
    $(".tekanan-darah")
      .val(systolic + "/" + diastolic)
      .trigger("change");
    nilaiTd = systolic + "/" + diastolic;
  }
});

if($("#asesmenmedisform-td_diastolic").val() != "" && $("#asesmenmedisform-td_systolic").val() != ""){
    let diastolic = $("#asesmenmedisform-td_diastolic").val() != "" ? $("#asesmenmedisform-td_diastolic").val() : 0;
    let systolic = $("#asesmenmedisform-td_systolic").val() != "" ? $("#asesmenmedisform-td_systolic").val() : 0;
  $(".tekanan-darah")
    .val($("#asesmenmedisform-td_systolic").val() + "/" + $("#asesmenmedisform-td_diastolic").val())
    .trigger("change");
  nilaiTd = systolic + "/" + diastolic;
  KategoriTd();
}
function KategoriTd(){
  var hasil;
  let diastolic = $("#asesmenmedisform-td_diastolic").val() != "" && !isNaN($("#asesmenmedisform-td_diastolic").val()) ? $("#asesmenmedisform-td_diastolic").val() : 0;
  let systolic = $("#asesmenmedisform-td_systolic").val() != "" && !isNaN($("#asesmenmedisform-td_systolic").val()) ? $("#asesmenmedisform-td_systolic").val() : 0;
  nilaiTd = systolic + "/" + diastolic;
  if(diastolic == "" && systolic == ""){
    $(".tekanan-darah").val("")
  } else {
    $.ajax({
      url:
        "/ranap/pemeriksaan-rawat-inap/get-hasil-td?pendaftaran_id=" +
        $(".pendaftaran_id").val() +
        "&nilai=" + 
        $(".tekanan-darah").val() +
        "&golongan_umur=" +
        $(".golongan_umur").val(),
      type: "get",
      dataType: "JSON",
      beforeSend: function () {
        $(".hasil-td").after(loading_spinner);
      },
      success: function (data, text, xhr) {
        if (xhr.status == 200) {
          hasil = data.hasil;
        }
      },
    }).done(function () {
      if(diastolic == "" || systolic == ""){
        $(".hasil-td").val("")
      }else{
        $(".hasil-td").val(hasil);
      }
      $(".spinner-text").remove();
    });
  }
  
};
$(".tekanan-darah").on("change", function () {
  KategoriTd();
});
/*----------  Tekanan Darah end  ----------*/

/*----------  Tekanan Darah start  ----------*/
$("#asesmenmedisform-detak_nadi").keyup(function(e){
  // Allow: backspace, delete, tab, escape, enter and .
  if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
       // Allow: Ctrl+A, Command+A
      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
       // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40)) {
           // return;
  }
  // Ensure that it is a number and stop the keypress
  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
  }

  kategoriNadi();
});
function kategoriNadi(){
  let kategori = 'Inreguler';
  let val = $("#asesmenmedisform-detak_nadi").val() ? parseInt($("#asesmenmedisform-detak_nadi").val()) : 0;
  let field_denyut = $('#asesmenmedisform-denyut_jantung');

  if ((umur.tahun < 1) && ((val >= 100) && (val <= 160))) {
      kategori = 'Reguler';
  }else if (((umur.tahun >= 1) && (umur.tahun <= 10)) && ((val >= 70) && (val <= 120))) {
      kategori = 'Reguler';
  }else if (((umur.tahun >= 11) && (umur.tahun <= 17)) && ((val >= 60) && (val <= 100))) {
      kategori = 'Reguler';
  }else if ((umur.tahun > 17) && ((val >= 60) && (val <= 100))) {
      kategori = 'Reguler';
  }

  field_denyut.val(kategori).change();
}
/*----------  Tekanan Darah end  ----------*/

/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0;
var nilaiGcsVerbal = 0;
var nilaiGcsMotorik = 0;
var nilaiGcs = 0;
var hitungGcs = function () {
  nilaiGcsEye = isNaN(parseInt($(".gcs_eye").find(":selected").attr("data-nilai"))) ? 0 : parseInt($(".gcs_eye").find(":selected").attr("data-nilai"));
  nilaiGcsVerbal = isNaN(parseInt($(".gcs_verbal").find(":selected").attr("data-nilai"))) ? 0 : parseInt($(".gcs_verbal").find(":selected").attr("data-nilai"));
  nilaiGcsMotorik = isNaN(parseInt($(".gcs_motorik").find(":selected").attr("data-nilai"))) ? 0 : parseInt($(".gcs_motorik").find(":selected").attr("data-nilai"));
  nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;

  $("#asesmenmedisform-jumlah_gcs").val(isNaN(nilaiGcs) ? "" : nilaiGcs);
  let hasil;

  var is_kapitis = $("#asesmenmedisform-is_kapitis").is(":checked");
  if (isNaN(nilaiGcs)) {
    $(".hasil_gcs").val("");
  } else {
    $.each(dataGcs, function (key, value) {
      if (
        nilaiGcs >= value["gcs_nilaimin"] &&
        nilaiGcs <= value["gcs_nilaimax"] &&
        value["is_kapitis"] == is_kapitis
      ) {
        $(".hasil_gcs").val(value.gcs_nama);
        return false;
      }
    });
  }
};

$("#asesmenmedisform-is_kapitis").on("change", function (e) {
  hitungGcs();
});

$(".gcs_eye").on("change", function (e) {
  hitungGcs();
});
$(".gcs_verbal").on("change", function (e) {
  hitungGcs();
});
$(".gcs_motorik").on("change", function (e) {
  hitungGcs();
});

/*----------  Gcs end  ----------*/

/*----------  Anatomi tubuh start  ----------*/
var sumbuX = 0;
var sumbuY = 0;

$(window).keydown(function (e) {
  if (e.keyCode == 27) {

    $(".add-caption, .add-caption-anak, .add-caption-luka-bakar, .add-caption-luka-bakar-anak").val("");
    $(".bagian-tubuh, .bagian-tubuh-anak, .bagian-tubuh-luka-bakar, .bagian-tubuh-luka-bakar-anak").val("");
    $(".bagian-tubuh-detail, .bagian-tubuh-detail-anak, .bagian-tubuh-detail-luka-bakar, .bagian-tubuh-detail-luka-bakar-anak").find("option").remove();
    $(".tag:not(span), .tag-anak, .tag-luka-bakar, .tag-luka-bakar-anak").attr({
      style: "display:none;",
    });
    $(".tag:not(span), .tag-anak, .tag-luka-bakar, .tag-luka-bakar-anak").data("show", 1);

    // $(".add-caption").val("");
    // $(".bagian-tubuh").val("");
    // $(".bagian-tubuh-detail").find("option").remove();
    // $(".tag").attr({
    //   style: "display:none;",
    // });
    // $(".tag").data("show", 1);



    // $(".add-caption-anak").val("");
    // $(".bagian-tubuh-anak").val("");
    // $(".bagian-tubuh-detail-anak").find("option").remove();
    // $(".tag-anak").attr({
    //   style: "display:none;",
    // });
    // $(".tag-anak").data("show", 1);

    // $(".add-caption-luka-bakar").val("");
    // $(".bagian-tubuh-luka-bakar").val("");
    // $(".bagian-tubuh-detail-luka-bakar").find("option").remove();
    // $(".tag-luka-bakar").attr({
    //   style: "display:none;",
    // });
    // $(".tag-luka-bakar").data("show", 1);

    // $(".add-caption-luka-bakar-anak").val("");
    // $(".bagian-tubuh-luka-bakar-anak").val("");
    // $(".bagian-tubuh-detail-luka-bakar-anak").find("option").remove();
    // $(".tag-luka-bakar-anak").attr({
    //   style: "display:none;",
    // });
    // $(".tag-luka-bakar-anak").data("show", 1);
    return false;
  }
});

$(".tag:not(span),.tag-luka-bakar,.tag-anak,.tag-luka-bakar-anak").keydown(function (e) {
  if (e.which == 13 || e.keyCode == 13) {
    e.preventDefault();
  }
});


$('#asesmenmedis-form').on("click", ".image-frame, .image-frame-luka-bakar, .image-frame-anak, .image-frame-luka-bakar-anak", function (event) {
  event.preventDefault();
  event.stopPropagation();
  var split_class = $(this)[0].className.split("image-frame")
  tag = $(`.tag${split_class[1]}`).not('span');
  var posX = event.pageX - $(this).offset().left,
      posY = event.pageY - $(this).offset().top;
  sumbuX = posX;
  sumbuY = posY
  if (tag.data("show") != 1) {
    tag.attr({
      style: "display:none;",
    });
    tag.data("show", 1);
  } else {
    tag.attr({
      style:
        "top: " +
        (posY + 70) +
        "px; left: " +
        posX +
        "px;width:500px;z-index:3;position:absolute;",
    });
    tag.data("show", 2);
  }
});

var addCaption = function (e) {
  var valBagian = "";
  var valBagianDetail = "";
  e.preventDefault();
  var date = new Date();
  var m = date.getMonth() + 1;
  var d = date.getDate();
  var now =
    (("" + d).length < 2 ? "0" : "") +
    d +
    "/" +
    (("" + m).length < 2 ? "0" : "") +
    m +
    "/" +
    date.getFullYear() +
    " " +
    date.getHours() +
    ":" +
    date.getMinutes() +
    ":" +
    date.getSeconds();
  var tabel = $(".tabel-anggotatubuh");
  var _contentParent = $(this).closest(".well-sm");

  var valBagian = $(".bagian-tubuh").val();
  var valBagianDetail = $(".bagian-tubuh-detail").val();
  var jenisGambar = "Dewasa";
  var GambarId = 1;
  var Berat_LB = "";
  if($(this)[0].classList[1] == "add-caption-luka-bakar"){
    valBagian = $(".bagian-tubuh-luka-bakar").val();
    valBagianDetail = $(".bagian-tubuh-detail-luka-bakar").val();
    jenisGambar = "Dewasa_LB";
    GambarId = 2;
    Berat_LB = $('input[name="AsesmenMedisForm[berat_luka_bakar]"]:checked').val()
  }else if($(this)[0].classList[1] == "add-caption-anak"){
    valBagian = $(".bagian-tubuh-anak").val();
    valBagianDetail = $(".bagian-tubuh-detail-anak").val();
    jenisGambar = "Anak";
    GambarId = 3;
  }else if($(this)[0].classList[1] == "add-caption-luka-bakar-anak"){
    valBagian = $(".bagian-tubuh-luka-bakar-anak").val();
    valBagianDetail = $(".bagian-tubuh-detail-luka-bakar-anak").val();
    jenisGambar = "Anak_LB";
    GambarId = 4;
    Berat_LB = $('input[name="AsesmenMedisForm[berat_luka_bakar]"]:checked').val()
  }
  if (e.keyCode == 13 || e.keyCode == 27) {

    if (e.keyCode == 13 && /[\w\d]+/.test($(this).val()) && valBagian != "") {
      var bagian =
        typeof bagianTubuh[valBagian] != "undefined"
          ? bagianTubuh[valBagian]
          : "-";
      var bagianDetail =
        typeof detailBagianTubuh[valBagian][valBagianDetail] != "undefined"
          ? detailBagianTubuh[valBagian][valBagianDetail]
          : "-";
      tmpData[counter] = {
        counters: counter,
        bagian: bagian,
        bagianDetail: bagianDetail,
        bagiantubuh_id: valBagian,
        bagiantubuhdetail_id: valBagianDetail,
        koordinat_y: sumbuY,
        koordinat_x: sumbuX,
        created_date: now,
        catatan_tubuh: $(this).val(),
        jenis_gambar_nama : jenisGambar,
        jenis_gambar_id : GambarId,
        berat_luka_bakar : Berat_LB,
      };

      tmpData[counter] = {...tmpData[counter], ...setImageSize(jenisGambar)};

      addRow();
      counter++;
      saveChanges();
    }
    $(this).val("");
    $(".bagian-tubuh, .bagian-tubuh-anak, .bagian-tubuh-luka-bakar, .bagian-tubuh-luka-bakar-anak").val("");
    $(".bagian-tubuh-detail, .bagian-tubuh-detail-anak, .bagian-tubuh-detail-luka-bakar, .bagian-tubuh-detail-luka-bakar-anak").val("");
    $(".tag:not(span), .tag-anak, .tag-luka-bakar, .tag-luka-bakar-anak").attr({
      style: "display:none;",
    });
    $(".tag:not(span), .tag-anak, .tag-luka-bakar, .tag-luka-bakar-anak").data("show", 1);
    return false;
  }
};

var addRow = function () {

  var _tabel = null;
  var _tabel_dewasa = $(".tabel-anggotatubuh");
  if (_tabel_dewasa.find("tbody > tr").length == 1) {
    $(".default-row").hide();
  }
  var clone = $(".default-row").clone();
  _tabel_dewasa
    .find("tbody")
    .html(
      '<tr class="default-row" style="display:none">' + clone.html() + "</tr>"
    );

  var _tabel_dewasa_luka_bakar = $(".tabel-luka-bakar");
  var clone_luka_bakar = $(".default-row").clone();

  _tabel_dewasa_luka_bakar
    .find("tbody")
    .html(
      '<tr class="default-row" style="display:none">' + clone_luka_bakar.html() + "</tr>"
    );

    var _tabel_anak = $(".tabel-anggotatubuh-anak");
    var clone_anak = $(".default-row").clone();

    _tabel_anak
      .find("tbody")
      .html(
        '<tr class="default-row" style="display:none">' + clone_anak.html() + "</tr>"
      );

    var _tabel_anak_luka_bakar = $(".tabel-luka-bakar-anak");
    var clone_anak_luka_bakar = $(".default-row").clone();

    _tabel_anak_luka_bakar
      .find("tbody")
      .html(
        '<tr class="default-row" style="display:none">' + clone_anak_luka_bakar.html() + "</tr>"
      );

  var i = 0, j = 0, k = 0, l = 0, no = 0;
  $.each(tmpData, function (key, items) {
    // $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ items.counters +'</span></div>');
    if(items.jenis_gambar_id == null || items.jenis_gambar_id == 1){
      i = i +1;
      no = i;
      $(".image-frame").append(
        '<div style="top: ' +
        items.koordinat_y +
        "px; left: " +
        items.koordinat_x +
        'px;z-index:3;position:absolute;" class="tag-image counter-' +
        items.counters +
        '"><span class="badge bg-warning-400">' +
        i +
        "</span></div>"
      );
      _tabel = _tabel_dewasa
    }
    else if( items.jenis_gambar_id == 2) {
      j = j +1;
      no = j;
      $(".image-frame-luka-bakar").append(
        '<div style="top: ' +
        items.koordinat_y +
        "px; left: " +
        items.koordinat_x +
        'px;z-index:3;position:absolute;" class="tag-image counter-' +
        items.counters +
        '"><span class="badge bg-warning-400">' +
        j +
        "</span></div>"
      );
      _tabel = _tabel_dewasa_luka_bakar
    }
    else if( items.jenis_gambar_id == 3) {
      k = k +1;
      no = k;
      $(".image-frame-anak").append(
        '<div style="top: ' +
        items.koordinat_y +
        "px; left: " +
        items.koordinat_x +
        'px;z-index:3;position:absolute;" class="tag-image counter-' +
        items.counters +
        '"><span class="badge bg-warning-400">' +
        k +
        "</span></div>"
      );
      _tabel = _tabel_anak
    }
    else {
      l = l +1;
      no = l;
      $(".image-frame-luka-bakar-anak").append(
        '<div style="top: ' +
        items.koordinat_y +
        "px; left: " +
        items.koordinat_x +
        'px;z-index:3;position:absolute;" class="tag-image counter-' +
        items.counters +
        '"><span class="badge bg-warning-400">' +
        l +
        "</span></div>"
      );
      _tabel = _tabel_anak_luka_bakar
    }

    var html = "";
    html += "<tr>";
    // html += '<td>'+ items.counters +'</td>';
    html += "<td>" + no + "</td>";
    html += "<td>" + items.created_date + "</td>";
    html += "<td>" + items.bagian + "</td>";
    html +=
      "<td>" +
      (items.bagianDetail != null ? items.bagianDetail : "-") +
      "</td>";
    html += "<td>" + items.catatan_tubuh + "</td>";
    if(items.jenis_gambar_id == 2 || items.jenis_gambar_id == 4){
      html += "<td>" + items.berat_luka_bakar + "</td>";
    }
    html +=
      '<td><button style="padding-left: 9px !important;" class="btn btn-danger btn-xs hapus-item" data-counter="' +
      items.counters +
      '">' +
      '<i class="fa fa-trash"></i></button></td>';
    html += "</tr>";
    _tabel.find("tbody").append(html);
  });
};

var delRow = function (event) {
  event.preventDefault();
  var _this = $(this);
  // var _counter = _this.data('counter') + 1;
  var _counter = _this.data("counter");
  var _trParent = _this.closest("tr");
  var _tabel = $(".tabel-anggotatubuh");
  var index = -1;

  $.each(tmpData, function (key, item) {
    if (item.counters == _counter) {
      index = key;
    }
  });

  delete tmpData[index];
  _trParent.remove();
  // delete tmpData[_counter];

  // $.each(tmpData, function (key, items) {
  //     if (_counter < key) {
  //         // delete tmpData[key];
  //         tmpData[(key - 1)] = items;
  //         tmpData[(key - 1)]['counters'] = items.counters - 1;
  //     }
  // });

  // $('.counter-' + _counter).remove();
  $(".tag-image").remove();
  addRow();
  if (_tabel.find("tbody > tr").length == 1) {
    $(".default-row").show();
  }
  saveChanges()
  // counter--;
};

function setImageSize(type){
    switch (type) {
        case 'Dewasa':
            return  {height : $('.image-frame img').height(), width : $('.image-frame img').width()}
            break;
        case 'Dewasa_LB':
            return  {height : $('.image-frame-luka-bakar img').height(), width : $('.image-frame-luka-bakar img').width()}
            break;
        case 'Anak':
            return  {height : $('.image-frame-anak img').height(), width : $('.image-frame-anak img').width()}
            break;
        case 'Anak_LB':
            return  {height : $('.image-frame-luka-bakar-anak img').height(), width : $('.image-frame-luka-bakar-anak img').width()}
            break;
        default:

    }
}

$(document).off('click', '.hapus-item')
$(document).on("click", ".hapus-item", delRow);

$(".add-caption, .add-caption-anak, .add-caption-luka-bakar, .add-caption-luka-bakar-anak").on("keyup", addCaption);
var saveAnatomi = function (data) {
  let res = data;
  let pemeriksaanfisik_id = res.response["pemeriksaanfisik_id"]
    ? res.response["pemeriksaanfisik_id"]
    : null;
  let url = "/ranap/pemeriksaan-rawat-inap/save-anatomi";

  if (Object.keys(tmpData).length) {
    $.ajax({
      type: "POST",
      dataType: "json",
      url: url,
      data: {
        data: tmpData,
        pendaftaran_id: $(".pendaftaran_id").val(),
        pasien_id: $(".pasien_id").val(),
        pemeriksaanfisik_id: pemeriksaanfisik_id,
      },
      error: function (data) {
        console.log(data);
      },
    });
  } else {
    alert("Anatomi Harus Di isi");
  }
};

$(".bagian-tubuh").on("change", function () {
  var opsi = detailBagianTubuh[$(".bagian-tubuh").val()];
  $(".bagian-tubuh-detail")
    .find("option")
    .remove()
    .end()
    .append(populateOpsi(opsi));
});

$(".bagian-tubuh-anak").on("change", function () {
  var opsi = detailBagianTubuh[$(".bagian-tubuh-anak").val()];
  $(".bagian-tubuh-detail-anak")
    .find("option")
    .remove()
    .end()
    .append(populateOpsi(opsi));
});

$(".bagian-tubuh-luka-bakar").on("change", function () {
  var opsi = detailBagianTubuh[$(".bagian-tubuh-luka-bakar").val()];
  $(".bagian-tubuh-detail-luka-bakar")
    .find("option")
    .remove()
    .end()
    .append(populateOpsi(opsi));
});

$(".bagian-tubuh-luka-bakar-anak").on("change", function () {
  var opsi = detailBagianTubuh[$(".bagian-tubuh-luka-bakar-anak").val()];
  $(".bagian-tubuh-detail-luka-bakar-anak")
    .find("option")
    .remove()
    .end()
    .append(populateOpsi(opsi));
});
/*----------  Anatomi tubuh end  ----------*/

// --------- print lihat hasil

// $('.btn-hasil-lab').click(function(){
//     $.ajax();
// });


$(".fisik").each(function(){
  const name = $(this)[0].id
  const split = name.split("-");
  if($(this).find("input:radio:checked").val() == 0){
    $(`.${split[1]}-lainnya`).attr("readonly", false);
  }else{
    $(`.${split[1]}-lainnya`).val("");
    $(`.${split[1]}-lainnya`).attr("readonly", true);
  }
});


$(".fisik").bind(
  "change",
  ({ currentTarget }) => {
    const element = $(currentTarget);
    const name = element[0].id
    const split = name.split("-");
    if(element.find("input:radio:checked").val() == 0){
      $(`.${split[1]}-lainnya`).attr("readonly", false);
    }else{
      $(`.${split[1]}-lainnya`).val("");
      $(`.${split[1]}-lainnya`).attr("readonly", true);
    }
  }
);

$(".checkbox-disabled").each(function(){
  const name = $(this)[0].id
  const split = name.split("-");
  if($(this).is(":checked") == true){
    $(`.${split[0]}-lainnya`).attr("readonly", false);
  }else{
    $(`.${split[0]}-lainnya`).val("");
    $(`.${split[0]}-lainnya`).attr("readonly", true);

  }
});

$(".checkbox-disabled").bind(
  "change",
  ({ currentTarget }) => {
    const element = $(currentTarget);
    const name = element[0].id
    const split = name.split("-");
    if(element.is(":checked") == true){
      $(`.${split[0]}-lainnya`).attr("readonly", false);
    }else{
      $(`.${split[0]}-lainnya`).val("");
      $(`.${split[0]}-lainnya`).attr("readonly", true);

    }
  }
);

$('#adm_persen,#adm_persen_anak').change(function () {
  if ($(this).val() > 100) {
    $(this).val('100');
  }
});

$(".btn-append-riwayat-penyakit-dahulu").on("click", function(){
  rowDataRiwayatPenyakitDahulu++;
  rowDataRiwayatPenyakitDahuluTotal++;

  const clone = $('.template-riwayat-penyakit-dahulu').clone()
              .addClass("row-data row-"+rowDataRiwayatPenyakitDahulu)
              .removeClass("clone-div template-riwayat-penyakit-dahulu hidden");
  clone.find('.btn-append').remove();
  clone.find("input, select").each(function(k,v){
    $(this).attr("name", "riwayat_penyakit["+rowDataRiwayatPenyakitDahulu+"]["+$(this).attr("data-name")+"]").addClass("row-"+rowDataRiwayatPenyakitDahulu+"-"+$(this).attr("data-name"))
  });

  $('.wrapper-riwayat-penyakit-dahulu').append(clone)
  $(".row-"+rowDataRiwayatPenyakitDahulu+"-tahun").select2();
});

$(document).on("click", ".btn-remove-riwayat-penyakit-dahulu", function(e){
  rowDataRiwayatPenyakitDahuluTotal--;
  $(this).parent().parent().remove()
  saveChanges()
});

function saveChanges() {
  $("[type='hidden']").prop('disabled', true)
  let updatedData = $("#asesmenmedis-form").serializeArray();
  $("[type='hidden']").prop('disabled', false)
  // handling default value for unchecked checkbox
  let uncheked_checkboxes = []
  $("[type='checkbox']:not(:checked)").each(function() {
    updatedData.push({
      name: $(this).attr("name"),
      value: 0
    })
  });

  let luka_bakar_form = {};
  if ($('input[name="AsesmenMedisForm[kategori_asmed]"]:checked').val() == 1) {
    luka_bakar_form = $('.luka_bakar').serializeArray()[0];
  }else{
    luka_bakar_form = $('.luka_bakar').serializeArray()[1];
  }

  let sumber_info_form = {};
  if ($('input[name="AsesmenMedisForm[sumber_hubungan]"]').val() != '') {
    sumber_info_form = {
      name: "AsesmenMedisForm[sumber_hubungan]",
      value: $('input[name="AsesmenMedisForm[sumber_hubungan]"]').val()
    }
  }

  updatedData.push(luka_bakar_form,sumber_info_form, {
    name : "pendaftaran_id",
    value: $(".pendaftaran_id").val()
  })

  if(Object.keys(tmpData).length == 0) {
    tmpData = {}
  }
   
  updatedData.push({
    name : "AsesmenMedisForm[anatomi_tubuh]",
    value: JSON.stringify(tmpData)
  })

  $.ajax({
    url: `/ranap/pemeriksaan-rawat-inap/set-cache-asmed`,
    method: 'GET',
    data: updatedData,
    success: (data) => {
      if (data.is_draft) {
        $(".draft").show()
      } else {
        $(".draft").hide()
      }
    },
  })
}
