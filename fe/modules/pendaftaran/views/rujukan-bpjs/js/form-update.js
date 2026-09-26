var metaData = null;
var data = null;
var ppkRujukan = null;

$(document).ready(function() {
    // $("#jenis_pelayanan_bpjs").prepend("<option selected=></option>").select2({
    //     placeholder: "Jenis Pelayanan"
    // });
    // // $("#rujukan").prepend("<option selected=></option>").select2({
    // //     placeholder: "Tipe Rujukan"
    // // });
    if ($('#rujukan').val() == 2) {
        $("#field-spesialis").hide();
        $("#jenis_pelayanan_bpjs").val(2).trigger("change");
        $("#jenis_pelayanan_bpjs").attr("disabled", true);
        $("#btn-pencarian-rujukan").prop('disabled', true).css({
            'cursor': 'not-allowed'
        });
    } else if($('#rujukan').val() == 1){
        $("#field-spesialis").hide();
        $("#btn-pencarian-rujukan").prop('disabled', true).css({
            'cursor': 'not-allowed'
        });
    } else {
        $("#btn-pencarian-rujukan").prop('disabled', false).css({
            'cursor': 'default'
        });
        $("#field-spesialis").show();
    }

    // var jenisPelayanan = $("#jenis_pelayanan_bpjs").val();
    // $("#jenis_pelayanan").val(jenisPelayanan);
});

$(document).on("click", "#btn-muat-ulang", function () {
    window.location.reload();
});

$(document).on("click", "#btn-cari-sep", function(event) {
    event.preventDefault();
    
    $('#btn-cari-sep').prop('disabled', true);
    var nosep = $("#nosep").val();
    $.ajax({
        type: "GET",
        url: "/pendaftaran/rujukan-bpjs/get-pasien-by-sep?nosep=" + nosep,
        dataType: "JSON",
        beforeSend: function (beforeSend) {

        },
        success: function (success) {
            metaData = success.metadata;
            data = success.response;
            
            $('#btn-cari-sep').prop('disabled', false);
            if (metaData.status == 200) {
                // Info pasien
                $(".namapasien").text(data.peserta.nama);
                $(".nomorrekammedik").text(data.peserta.noMr);
                $(".nosep").text(data.noSep);
                $(".tglsep").text(data.tglSep);
                $(".jenispelayanan").text(data.jnsPelayanan);
                $(".diagnosa").text(data.diagnosa);
                $(".nokartubpjs").text(data.peserta.noKartu);
                $(".namapeserta").text(data.peserta.nama);
                $(".tanggallahir").text(data.peserta.tglLahir);
                $(".jeniskelamin").text(data.peserta.kelamin);
                $(".hakkelas").text(data.peserta.hakKelas);

                // Form rujukan
                $("#tanggal_rujukan").val(data.tglSep);
                $("#pendaftaran_id").val(data.pendaftaran_id);
                $("#pasienadmisi_id").val(data.pasienadmisi_id);
                $("#bpjs_id").val(data.bpjs_id);
                $("#poli_rujukan").val(data.poli);

                // Show content rujukan
                $("#content-rujukan").show();

                if ($("#rujukan").val() == 2) {
                    $("#dirujukke").val(data.provUmum.nmProvider);
                    $("#dirujukke_nama").val(data.provUmum.nmProvider);
                    $("#kode_ppkrujukan").val(data.provUmum.kdProvider);
                    $(".faskes").text(data.provUmum.nmProvider+" - "+data.provUmum.kdProvider);
                }
            } else {
                docoNotification("warning", "Perhatian!", success.response.message);
            }
        },
        error: function (error) {
            $('#btn-cari-sep').prop('disabled', false);
            docoNotification("warning", "Perhatian!", "Data SEP tidak ditemukan.");
        },
    });
});

$(document).on("click", "#btn-batal-sep", function () {
    // Pencarian SEP
    $("#nosep").val(null);

    // Info pasien
    $(".namapasien").text("-");
    $(".nomorrekammedik").text("-");
    $(".nosep").text("-");
    $(".tglsep").text("-");
    $(".jenispelayanan").text("-");
    $(".diagnosa").text("-");
    $(".nokartubpjs").text("-");
    $(".namapeserta").text("-");
    $(".tanggallahir").text("-");
    $(".jeniskelamin").text("-");
    $(".hakkelas").text("-");
    $(".faskes").text("-");

    // Form rujukan
    $("#tanggal_rujukan").val(null);
    $("#catatan_rujukan").val(null);
    $("#diagnosa_rujukan").val(null).trigger("change");
    $("#dirujukke").val(null);
    $("#dirujukke_nama").val(null);

    // Hidden form
    $("#kode_ppkrujukan").val(null);
    $("#nama_ppkrujukan").val(null);
    $("#pendaftaran_id").val(null);
    $("#pasienadmisi_id").val(null);
    $("#jenis_pelayanan").val(null);
    $("#poli_rujukan").val(null);

    // Hide content rujukan
    $("#content-rujukan").hide();
});

$(document).on("change", "#rujukan", function () {
    var rujukan = $(this).val();
    if (rujukan == 2) {
        $("#dirujukke").val(nmProvider);
        $("#kode_ppkrujukan").val(kdProvider);
        $("#dirujukke_nama").val(nmProvider);
        $(".faskes").text(kdProvider+" - "+nmProvider)
        
        $("#field-spesialis").hide();
        $("#jenis_pelayanan_bpjs").val(2).trigger("change");
        $("#jenis_pelayanan_bpjs").attr("disabled", true);
        $("#btn-pencarian-rujukan").prop('disabled', true).css({
            'cursor': 'not-allowed'
        });
    } else if(rujukan == 1){
        $("#field-spesialis").hide();
        $("#btn-pencarian-rujukan").prop('disabled', true).css({
            'cursor': 'not-allowed'
        });
    } else {
        $("#btn-pencarian-rujukan").prop('disabled', false).css({
            'cursor': 'default'
        });
        $("#field-spesialis").show();
    }
});

$(document).on("change", "#jenis_pelayanan_bpjs", function () {
    var jenisPelayanan = $(this).val();
    $("#jenis_pelayanan").val(jenisPelayanan);
});

$("#form-rujukan-bpjs").on("submit", function(event) {
    event.preventDefault();
    var formRujukanBpjs = $("#form-rujukan-bpjs").serializeArray();


    $(this).docoForm("submit", {
        data: formRujukanBpjs,
        before: function() {
            return false;
        },  
        success : function(response) {
            window.open(url_print+"?id="+response.response.rujukanbpjs_id+"&bpjs="+response.response.bpjs_id, '_blank');

            window.location.replace(url+"?id="+response.response.id);
        },
        error: function(response) {
            if (response.responseJSON.metadata.status == 400) {
                docoNotification('error', 'Peringatan!', response.responseJSON.response.message, '');
            }
        }
    }); 
});