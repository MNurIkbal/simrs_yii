/*
* @Author: Budi
* @Date:   2020-01-29 12:44:34
*/

$(document).ready(function(){
    let link_penunjang = '/mcu/pemeriksaan/penunjang?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    let link_kesimpulan = '/mcu/pemeriksaan/kesimpulan?id=' + pendaftaran_id + '&pasien_id=' + pasien_id + '&ruangan_id=' + ruangan_id + '&pegawai_id=' + pegawai_id;
    let link_riwayat_penyakit = '/mcu/pemeriksaan/riwayat-penyakit?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    let link_pemeriksaan_fisik = '/mcu/pemeriksaan/pemeriksaan-fisik?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    let link_status_kesehatan = '/mcu/pemeriksaan/status-kesehatan?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    let link_hasil_pemeriksaan_phr = '/mcu/pemeriksaan/hasil-pemeriksaan-phr?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    let link_resume_pemeriksaan = '/mcu/pemeriksaan/resume-hasil-pemeriksaan?id=' + pendaftaran_id + '&pasien_id=' + pasien_id;
    if(config == 'prima'){
        $('.print').removeClass('hidden');
        $('.template').removeClass('hidden');
    }else{
        $('.print').addClass('hidden');
        $('.template').addClass('hidden');
    }

    $('#content-riwayat-penyakit').docoLoad({
        url: link_riwayat_penyakit,
        dataType: 'html',
        success : function(data) {
        }
    });

    $('#tab-penunjang').on("click", function(e){
        $('#content-penunjang').docoLoad({
            url: link_penunjang,
            dataType: 'html',
            success : function(data) {
            }
        });
    });
    
    $('#tab-riwayat-penyakit').on("click", function(e){
        $('#content-riwayat-penyakit').docoLoad({
            url: link_riwayat_penyakit,
            dataType: 'html',
            success : function(data) {
            }
        });
    });

    $('#tab-pemeriksaan-fisik').on("click", function(e){
        $('#content-pemeriksaan-fisik').docoLoad({
            url: link_pemeriksaan_fisik,
            dataType: 'html',
            success : function(data) {
            }
        });
    });

    $('#tab-kesimpulan').on("click", function(e){
        $('#content-kesimpulan').docoLoad({
            url: link_kesimpulan,
            dataType: 'html',
            success : function(data) {
            }
        });
    });

    $('#tab-status-kesehatan').on("click", function(e){
        $('#content-status-kesehatan').docoLoad({
            url: link_status_kesehatan,
            dataType: 'html',
            success : function(data) {
            }
        });
    });

    $('#tab-hasil-pemeriksaan-kesehatan').on("click", function(e){
        $('#content-status-kesehatan').docoLoad({
            url: link_hasil_pemeriksaan_phr,
            dataType: 'html',
            success : function(data) {
            }
        });
    });

    $('#tab-resume-pemeriksaan').on("click", function(e){
        $('#content-resume-pemeriksaan').docoLoad({
            url: link_resume_pemeriksaan,
            dataType: 'html',
            success : function(data) {
            }
        });
    });
});


$(document).on("click", ".print", function() {
    window.open("/mcu/pemeriksaan/export-pdf-periksa-fisik?pendaftaran_id="+pendaftaran_id);
});

// simpan template
$(document).on("click", "#btn-add", function(e) {
    var simpan = true;
    if(type == 'resume'){
        var valueData = CKEDITOR.instances.resume_editor.getData().replace(/<[^>]+>/g, '').trim();
        var tatalaksana = CKEDITOR.instances.tatalaksana_editor.getData().replace(/<[^>]+>/g, '').trim();
        if(valueData.length > 2000 || tatalaksana.length > 2000) {
            var simpan = false;
            $('#danger-resume').html("Text Resume/Tatalaksana Hasil Pemeriksaan lebih dari 2000 karakter !")
        }else{
            $('#danger-resume').html(null)
        }
    }else{
        $('#danger-resume').html(null)
    }
    var hasil = $("#"+id_form).serializeArray();
    hasil.push({
        name : "PemeriksaanFisikNewForm[anatomi]",
        value: JSON.stringify(saveHasil)
    });
    e.preventDefault();
    if(simpan == true){
        $(this).docoForm("click",{
            url: "/mcu/pemeriksaan/simpan-template?pendaftaran_id="+id+"&type="+detail_type+"&judul="+$("#templateform-temp_nama").val(),
            data: hasil,
            skipErrorNotif: true,
            success : function (data) {
                $("#close").trigger("click");
                $("#save-template-"+type).attr("disabled", true)
            }
        });
    }
});
