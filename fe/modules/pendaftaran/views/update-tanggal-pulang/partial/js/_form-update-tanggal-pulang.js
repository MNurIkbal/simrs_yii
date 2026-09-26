$('.pickadate-w-month').pickadate({
    format: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: 99,
    formatSubmit: 'dd-mm-yyyy',
});

$(document).ready(function () {
    setInformasiPasien();
});

function setInformasiPasien() {
    var data_bpjs = data.data_bpjs;
    var data_update_tanggal_pulang = data.data_updat_tanggal_pulang;
    var data_pasien_pulang = data.data_pasien_pulang;
    var data_vclaim = data.data_vclaim.response;
    // var data_pasien = data.data_pasien;
    var additional_bpjs = JSON.parse(data_bpjs.additional_data);

    // SEP
    $('.nama-pasien').html(data_vclaim.peserta.nama);
    $('.rm-pasien').html(data_vclaim.peserta.noMr);
    $('.info-no_sep').html(": " + data_vclaim.noSep);
    var tgl_sep = convertDate(data_vclaim.tglSep);
    $('.info-tgl_sep').html(": " + tgl_sep);
    $('.info-jenis_pelayanan').html(": " + data_vclaim.jnsPelayanan);
    $('.info-diagnosa').html(": " + data_vclaim.diagnosa);

    // Peserta
    $('.info-nama_peserta').html(": " + data_vclaim.peserta.nama);
    // var sex = convertSex(data_vclaim.peserta.kelamin);
    $('.info-jenis_kelamin').html(": " + data_vclaim.peserta.kelamin);
    $('#bpjsnew_detail_nokartu').html(": " + data_vclaim.peserta.noKartu);
    $('#bpjsnew_detail_tgl_lahir').html(": " + data_vclaim.peserta.tglLahir);
    $('#bpjsnew_detail_hak_kelas').html(": " + data_vclaim.peserta.hakKelas);
    // var tmt = peserta.tglTMT;
    // var tat = peserta.tglTAT;
    // $('#info-ppk_peserta').html(": " + tmt + ' - ' + tat);
    if($("#selectStatusPulang").val() !== undefined && $("#selectStatusPulang").val() == 4){
        $("#no_surat_kematian").prop("readonly",false);
    }
}

$("#form").on("submit", function(event) {
    event.preventDefault();
    var UpdateTanggalPulangForm = $("#form").serializeArray();

    $(this).docoForm("submit", {
        data: UpdateTanggalPulangForm,
        error: function(data) {
            if (data.status == 422) {
                if(data.responseJSON.response.message != ''){
                    docoNotification('error', 'Proses Gagal!', data.responseJSON.response.message);
                }else{
                    docoNotification('error', 'Proses Gagal!', 'Terjadi kesalahan pada inputan',);
                }
            }else {
                docoNotification('error', 'Proses Gagal!', data.responseJSON.response.message);
            }
        },
        success: function (response) {
            if(typeof response.result != 'undefined'){
                if (response.result.metaData.code !='200' && (response.result.metaData.code)) {
                    docoNotification('error', 'Proses Gagal!', response.result.metaData.message);
                }
            } else {
                if ((typeof response.message !== 'undefined') && (response.message)) {
                    docoNotification('error', 'Proses Gagal!', response.message);
                } else {
                    docoNotification("success", i18next.t("Proses Berhasil"), i18next.t("data Berhasil disimpan."));
                    window.location.href = '/pendaftaran/update-tanggal-pulang/';
                } 
            }
        }
    }); 
});

$(".btn-hapus").on("click", function () {
    // formReset();
    window.location.href = '/pendaftaran/update-tanggal-pulang/';
});

$("#selectStatusPulang").on("change",function(){
    if(this.value == 4){
        $("#no_surat_kematian").prop("readonly",false);
    }else{
        $("#no_surat_kematian").prop("readonly",true);
    }
});


function formReset() {  
    $("#nosep").val(model.nosep);
    $("#tglpasienpulang").val(model.tglpasienpulang);
    $("#selectStatusPulang").val(model.status_pulang_id).trigger('change');
    $("#no_surat_kematian").val(model.no_surat_kematian);
    $("#tgl_meninggal").val(model.tgl_meninggal);
    $("#no_up_manual").val(model.no_up_manual);
}

function convertDate(input) {
    var date = new Date(input);
    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = date.getFullYear();
    date = dd + '-' + mm + '-' + yyyy;
    return date;
}

function convertSex(input) {
    var sex;
    if (input == 16) {
        sex = 'Laki-laki';
    } else {
        sex = 'Perempuan';
    }
    return sex;
}