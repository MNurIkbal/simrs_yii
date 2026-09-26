
/**
 * [javascript form sep manual]
 * @author Wahyu
 */

var text_memuat = i18next.t('Memuat...');
var text_cari_sep = '<i class="fa fa-search"></i> ' + i18next.t('Cari No SEP');

$('.sep-manual').attr('disabled', true);

$('#cari-nosep').click(function() {
    var nosep = $('#nosep').val();

    if(nosep == "") {
        showErrorSep('No. SEP harus diisi.');
        return false;
    }

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/cari-sep',
        data: {
            nosep: nosep,
        },
        dataType: 'JSON',
        beforeSend: function () {
            disableButtonCari(text_memuat);
            showErrorSep('');
            showLoader();
        },
        success: function (res) {
            if(res.message !== 'undefined') {
                showErrorSep(res.message);
                enableButtonCari(text_cari_sep);
            }

            if(typeof res.data !== 'undefined') {
                if(res.data.response == "") {
                    var message = "Maaf Server BPJS sedang mengalami gangguan";
                    showErrorSep(message);
                    enableButtonCari(text_cari_sep);
                }

                var resbpjs = res.data.metadata;
                if(resbpjs.status == 'undefined') {
                    var message = "Maaf Server BPJS sedang mengalami gangguan";
                    showErrorSep(message);
                    enableButtonCari(text_cari_sep);
                }
                else if (resbpjs.status != "200") {
                    var message = resbpjs.message;
                    showErrorSep(message);
                    enableButtonCari(text_cari_sep);
                } 
                else {
                    var responseBpjs = res.data.response.metaData;
                    if(responseBpjs.code == 201) {
                        showErrorSep(responseBpjs.message);
                        enableButtonCari(text_cari_sep);
                    }
                    else if(responseBpjs.message == 'OK') {
                        showErrorSep(responseBpjs.code);
                        enableButtonCari(text_cari_sep);
                    }
                    else {
                        if(responseBpjs.code == 200) {
                            if(res.data.response !== "") {
                                var nama_pasien = $("#bpjs-nama-pasien").val();
                                var resData = res.data.response.response;
                                var namaPeserta = resData.peserta.nama;
                                var noKartu = resData.peserta.noKartu;
                                var tglLahir = resData.peserta.tglLahir;
                                var jnsPeserta = resData.peserta.jnsPeserta;
                                var hakKelas = resData.peserta.hakKelas;
                                var jnsPelayanan = resData.jnsPelayanan;
                                var poli = resData.poli;
                                var tglSep = resData.tglSep;
                                var noSep = resData.noSep;
                                var noRujukan = resData.noRujukan;
                                var catatan = resData.catatan;
                                var diagnosa = resData.diagnosa;
                                var kelasRawat = resData.kelasRawat;
                                var penjamin = resData.penjamin;
                                var asuransi = resData.peserta.asuransi;
                                var kelamin = resData.peserta.kelamin;
                                var noMr = resData.peserta.noMr;

                                $(".detail_peserta").show();
                                $("#bpjsnew_detail_nama").html(namaPeserta);
                                $("#bpjsnew_detail_no_kartu").html(noKartu);
                                $("#bpjsnew_detail_jenis_pelayanan").html(jnsPelayanan);
                                $("#bpjsnew_detail_poli").html(poli);
                                $("#bpjsnew_detail_tglLahir").html(tglLahir);
                                $("#bpjsnew_detail_jnsPeserta").html(jnsPeserta);
                                $("#bpjsnew_detail_hakKelas").html(hakKelas);

                                $("#no_asuransi").val(noKartu);
                                $("#tglsep").val(tglSep);
                                $("#nosep").val(noSep);
                                $("#norujukan").val(noRujukan);
                                $("#catatansep").val(catatan);
                                $("#diagnosaawal").val(diagnosa);
                                $("#klsrawat").val(kelasRawat);
                                $("#jenis_pelayanan").val(jnsPelayanan);
                                $("#poli_tujuan").val(poli);
                                $("#jenis_peserta").val(jnsPeserta);
                                $("#penjamin").val(penjamin);
                                $("#asuransi").val(asuransi);
                                $("#hak_kelas").val(hakKelas);
                                $("#jenis_kelamin").val(kelamin);
                                $("#tanggal_lahir").val(tglLahir);
                                $("#noMr").val(noMr);


                                
                                if(nama_pasien.toLowerCase() !== namaPeserta.toLowerCase()) {
                                    (new PNotify({
                                        title: "Peringatan",
                                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                                            +" Nama Pasien BPJS : <strong>" + namaPeserta
                                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + nama_pasien
                                            +" </strong><br> Apakah Anda yakin akan melanjutkan proses? ",
                                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                                        type: "success",
                                        buttons: {
                                            closer: false,
                                            sticker: false
                                        },
                                        hide: false,
                                        confirm: {
                                            confirm: true,
                                            buttons: [{
                                                    text: 'Ya',
                                                    addClass: 'btn btn-xs btn-success',
                                                },
                                                {
                                                    text: 'Tidak',
                                                    addClass: 'btn btn-xs btn-danger',
                                                }
                                            ]
                                        },
                                        history: {
                                            history: false
                                        }
                                    })).get().on('pnotify.confirm', function(){
                                        $('.bpjs-step-1').hide();
                                        setTimeout(function(){
                                            $('.sep-manual').attr('disabled', false);
                                        }, 1000);
                                        
                                    }).on('pnotify.cancel', function() {
                                        $('.bpjs-step-1').show();
                                        enableButtonCari(text_cari_sep);
                                        $(".detail_peserta").hide();
                                    });
                                }
                                else {
                                    $('.bpjs-step-1').hide();
                                    setTimeout(function(){
                                        $('.sep-manual').attr('disabled', false);
                                    }, 1000);
                                }

                                enableButtonCari(text_cari_sep);
                            }
                        }
                    }
                }
            }
        },
        complete: function() {
            enableButtonCari(text_cari_sep);
            hideLoader();
        }
    });
});

$(".sep-manual").click(function(){
    var data = $('#sep-manual-form').serializeArray();
    var pendaftaran_id = $("#bpjsnewform-pendaftaran_id").val();

    $(this).docoForm('click', {
        skipConfirm: true,
        skipSuccessNotif: true,
        method: 'POST',
        data: data,
        url: window.location.origin + '/pendaftaran/end-point/create-sep-manual',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.sep-manual').html(_html).attr('disabled', true);
        },
        success: function(res) {
            var _html = '<i class="fa fa-save"></i> ' + i18next.t('Simpan');
            if( typeof res.response.data !== 'undefined') {
                setTimeout(function(){
                    $('.sep-manual').html(_html).attr('disabled', true);
                }, 1000);
                table.draw();
                window.open(window.location.origin + "/pendaftaran/end-point/print-sep?pendaftaran_id=" + pendaftaran_id, '_blank');
            }
        }
    });
});

var disableButtonCari = function(html) {
    $('#cari-nosep').html(html).prop('disabled', true);
}

var enableButtonCari = function(html) {
    $('#cari-nosep').html(html).prop('disabled', false);
}

var showErrorSep = function(html) {
    $('.err-nosep').html(html);
}