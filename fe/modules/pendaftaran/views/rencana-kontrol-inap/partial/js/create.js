var pendaftaran_id ='';
var bpjs_id ='';

$(document).ready(function () {

    $('.field-jenis_rencana input').on('click',function () {
        var jenis_rencana = $('input[name="jenis_rencana"]:checked').val();
        $('#no_sep').val(null);
        $('#no_kartu').val(null);
        if (jenis_rencana == 1){
            $('#cari_sep').show();
            $('#cari_no_kartu').hide();
        }else {
            $('#cari_sep').hide();
            $('#cari_no_kartu').show();
        }
    });
    
    $('#form').submit(function(event){
        event.preventDefault();
        var _data = $(this).serializeArray();
    
        _data.push({
            name: 'RencanaKontrolForm[no_sep]',
            value:  $('#no_sep').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[bpjs_id]',
            value:  $('#bpjs_id').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[pendaftaran_id]',
            value:  $('#pendaftaran_id').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[tgl_rencana_inap]',
            value:  $('#tgl_rencana_inap').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[no_kartu]',
            value:  $('#no_kartu').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[tgl_rencanakontrol]',
            value:  $('#tgl_rencanakontrol').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[jenis_pelayanan]',
            value:  $('#jenis_pelayanan').val(),
        });
    
        _data.push({
            name: 'RencanaKontrolForm[kode_poli]',
            value:  $('#kode_poli').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[nama_spesialis]',
            value:  $('#nama_spesialis').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[dokterdpjp_kode]',
            value:  $('#dokterdpjp_kode').val(),
        });
        _data.push({
            name: 'RencanaKontrolForm[dokterdpjp_nama]',
            value:  $('#dokterdpjp_nama').val(),
        });
        _data.push({
            name: 'jenis_rencana',
            value:  $('input[name="jenis_rencana"]:checked').val(),
        });
    
        $(this).docoForm("submit", {
            data: _data,
            error: function(data) {
                if (data.status == 422) {
                    docoNotification('error', "Terjadi kesalahan pada inputan", '');
                }else {
                    docoNotification('error', data.responseJSON.response.message, '');
                }
            },
            success: function (response) {
                if ((typeof(response.message!== "undefined")) && (response.message)) {
                    docoNotification('error', response.message, '');
                } else {
                    docoNotification("success", i18next.t("Proses Berhasil"), i18next.t("data Berhasil disimpan."));
                    window.open(window.location.origin + "/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=" + response.id, '_blank');
                    location.reload();
                }
            }
        });
    });
});



$('#reset').on('click', function () {

    location.reload();
});

$('.btn-hapus').on('click', function () {
    let now = new Date();
    let month = now.getMonth() + 1;
    if (month < 10) {
        month = '0'+month;
    }
    let today = now.getDate()  + '-' + (month) + '-' + now.getFullYear();
    $('#tgl_rencanakontrol').val(today);
    $('#jenis_pelayanan').val('Rawat Jalan').trigger('change');
    $('#kode_poli').val(null).trigger('change');
    $('#dokterdpjp_kode').val(null).trigger('change');
});

$('#cari').on('click', function () {
    var jenis_rencana = $('input[name="jenis_rencana"]:checked').val();
    var no_sep = $('#no_sep').val();
    var tgl_rencana_inap = $('#tgl_rencana_inap').val();
    var no_kartu = $('#no_kartu').val();
    $(".populate-data").css("display", "block")
    $(".populate-data").html(`mempersiapkan data ...`);
    resetForm();

    if (jenis_rencana == 1) {
        if (no_sep == null || no_sep == '') {
            docoNotification('error', "No SEP wajib diisi", "Silahkan inputkan no SEP");
            $(".populate-data").css("display", "none")
        }
        
        $('#form-informasi_create').hide();
        $('#form-rencana_kontrol').hide();
        setTimeout(() => {
            $.ajax({
                url: '/pendaftaran/rencana-kontrol-inap/get-data-kontrol',
                type: 'GET',
                data: {
                    no_sep: no_sep,
                    jenis_rencana: jenis_rencana,
                },
                dataType: 'JSON',
                error: function(response) {
                    $(".populate-data").css("display", "none")
                    docoNotification('error', response.responseJSON.response.title, response.responseJSON.response.message);
                },
                success: function(response) {
                    $(".populate-data").css("display", "none")
                    if (response.sep.metaData.code == 201)
                    {
                        docoNotification('error', "Maaf, Data tersebut tidak ada di BPJS", "Silahkan diulang untuk melakukan pencarian");
                    } else if(response.Sirs.status != 200) {
                        docoNotification('error', "Maaf, Data tersebut tidak ditemukan", "Silahkan diulang untuk melakukan pencarian");
                    }else {
                        $('#form-informasi_create').show();
                        $('#form-rencana_kontrol').show();

                        var peserta = (response.peserta.response != null) ? response.peserta.response.peserta : null;
                        var sep = (response.sep.response != null) ? response.sep.response : null;
                        var rujukan = (response.rujukan.response != null) ? response.rujukan.response.rujukan : null;
                        var sirs = response.Sirs.sirs;
                        $('.nama-pasien').html(peserta.nama);
                        $('.rm-pasien').html(peserta.mr.noMR);

                        // Info Pasien
                        $('.info-nama_peserta').html(": " + peserta.nama);
                        $('.info-jenis_kelamin').html(": " + peserta.sex);
                        $('#bpjsnew_detail_nokartu').html(": " + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html(": " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html(": " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html(": " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html(": " + peserta.hakKelas.keterangan);
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html(": " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html(": " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        $('#bpjsnew_detail_status_peserta').html(": " + statusPeserta);

                        // Rujukan
                        if(rujukan != null) {
                            $('#no_kunjungan').html(": " + rujukan.noKunjungan);
                            $('#tgl_kunjungan').html(": " + rujukan.tglKunjungan);
                            $('#keluhan').html(": " + rujukan.keluhan);
                            $('#diagnosa').html(": " + rujukan.diagnosa.kode+"-"+rujukan.diagnosa.nama);
                            $('#pelayanan').html(": " + rujukan.pelayanan.kode+"-"+rujukan.pelayanan.nama);
                            $('#poli-rujukan').html(": " + rujukan.poliRujukan.kode+"-"+rujukan.poliRujukan.nama);
                            $('#prov-rujukan').html(": " + (sep.provPerujuk.nmProviderPerujuk ==null ? '-' : sep.provPerujuk.nmProviderPerujuk) + ' - ' + sep.provPerujuk.kdProviderPerujuk);
                            $('.info-masa_rujukan').html(": " + rujukan.tglRujukan);
                            $('.info-no_rujukan').html(": " + sep.provPerujuk.noRujukan);
                        }

                        
                        // SEP
                        $('.info-no_sep').html(": " + sep.noSep);
                        $('.info-diagnosa').html(": " + sep.diagnosa);
                        $('.info-dpjp').html(": " + sep.dpjp.kdDPJP+"-"+sep.dpjp.nmDPJP);
                        $('.info-jenis_pelayanan').html(": " + sep.jnsPelayanan);
                        $('.info-poli').html(": " + sep.poli);
                        $('.info-prov_perujuk').html(": " + sep.provPerujuk.asalRujukan+", "+sep.provPerujuk.kdProviderPerujuk+"-"+sep.provPerujuk.nmProviderPerujuk+", "+sep.provPerujuk.noRujukan+", "+sep.provPerujuk.tglRujukan);
                        $('.info-prov_mum').html(": " + sep.provUmum.kdProviderPerujuk+"-"+sep.provUmum.nmProviderPerujuk);
                        $('.info-tgl_sep').html(": " + sep.tglSep);

                        // Form
                        $('#dokterdpjp_kode').val(sep.dpjp.kdDPJP).trigger('change');
                        $('#jenis_pelayanan').val(sep.jnsPelayanan).trigger('change');
                        $('#no_kartu').val(sep.peserta.noKartu);
                        noKartu = sep.peserta.noKartu;
                        $('#no_sep').val(sep.noSep);
                        $('#bpjs_id').val(sirs.bpjs.bpjs_id);
                        $('#pendaftaran_id').val(sirs.bpjs.pendaftaran_id);
                        $('#jenis_pelayanan').val('Rawat Jalan').trigger('change');
                    }
                }
            }), 2500
        });
    } else {
        if ($('#no_kartu').val() == null || $('#no_kartu').val() == '') {
            docoNotification('error', "No Kartu wajib diisi", "Silahkan inputkan no Kartu");
            $(".populate-data").css("display", "none")
        }
        noKartu = $('#no_kartu').val();

        $('#form-informasi_create').hide();
        $('#form-rencana_kontrol').hide();
        setTimeout(() => {
            $.ajax({
                url: '/pendaftaran/rencana-kontrol-inap/get-data-kontrol',
                type: 'GET',
                data: {
                    no_kartu: no_kartu,
                    jenis_rencana: jenis_rencana,
                },
                dataType: 'JSON',
                error: function(response) {
                    $(".populate-data").css("display", "none")
                    docoNotification('error', response.responseJSON.response.title, response.responseJSON.response.message);
                },
                success: function(response) {
                    $(".populate-data").css("display", "none")
                    
                    if(response.Sirs.status != 200) {
                        docoNotification('error', "Maaf, Data tersebut tidak ditemukan", "Silahkan diulang untuk melakukan pencarian");
                    }else {
                        $('#form-informasi_create').show();
                        $('#form-rencana_kontrol').show();
                        var peserta = (response.peserta.response != null) ? response.peserta.response.peserta : null;
                        var sep = (response.sep.response != null) ? response.sep.response : null;
                        var rujukan = (response.rujukan.response != null) ? response.rujukan.response.rujukan : null;
                        var sirs = response.Sirs.sirs;
                        $('.nama-pasien').html(peserta.nama);
                        $('.rm-pasien').html(peserta.mr.noMR);

                        // Info Pasien
                        $('.info-nama_peserta').html(": " + peserta.nama);
                        $('.info-jenis_kelamin').html(": " + peserta.sex);
                        $('#bpjsnew_detail_nokartu').html(": " + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html(peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html(": " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html(peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html(": " + peserta.hakKelas.keterangan);
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html( tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html( kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        $('#bpjsnew_detail_status_peserta').html(statusPeserta);

                        // Rujukan
                        if(rujukan != null) {
                            $('#no_kunjungan').html(rujukan.noKunjungan);
                            $('#tgl_kunjungan').html(rujukan.tglKunjungan);
                            $('#keluhan').html(rujukan.keluhan);
                            $('#diagnosa').html(rujukan.diagnosa.kode+"-"+rujukan.diagnosa.nama);
                            $('#pelayanan').html(rujukan.pelayanan.kode+"-"+rujukan.pelayanan.nama);
                            $('#poli-rujukan').html(rujukan.poliRujukan.kode+"-"+rujukan.poliRujukan.nama);
                            $('#prov-rujukan').html(": " + (rujukan.provPerujuk.nama ==null ? '-' : rujukan.provPerujuk.nama) + ' - ' + rujukan.provPerujuk.kode);
                            $('.info-masa_rujukan').html(": " + rujukan.tglRujukan);
                        }
                        
                        // SEP
                        $('.info-no_sep').html(sirs.bpjs.nosep);
                        $('.info-diagnosa').html(sirs.bpjs.diagnosaawal);
                        $('.info-dpjp').html(sirs.bpjs.kode_dpjp_melayani+"-"+sirs.bpjs.nama_dpjp_melayani);
                        $('.info-jenis_pelayanan').html(sirs.bpjs.jnspelayanan);
                        $('.info-poli').html(sirs.bpjs.politujuan);
                        $('.info-prov_perujuk').html(sirs.bpjs.asal_rujukan+", "+sirs.bpjs.kode_ppk_perujuk+"-"+sirs.bpjs.nama_ppk_perujuk+", "+sirs.bpjs.norujukan+", "+sirs.bpjs.tglrujukan);
                        $('.info-prov_mum').html(sirs.bpjs.ppkpelayanan+"-"+sirs.bpjs.nama_dpjp_melayani);
                        $('.info-tgl_sep').html(sirs.bpjs.tglsep);

                        // Form
                        $('#dokterdpjp_kode').val(sirs.bpjs.kode_dpjp_melayani).trigger('change');
                        $('#jenis_pelayanan').val(sirs.bpjs.jnspelayanan).trigger('change');
                        $('#no_kartu').val(sirs.bpjs.nokartuasuransi);
                        $('#no_sep').val(sirs.bpjs.nosep);
                        $('#bpjs_id').val(sirs.bpjs.bpjs_id);
                        $('#pendaftaran_id').val(sirs.bpjs.pendaftaran_id);
                        $('#jenis_pelayanan').val('Rawat Inap').trigger('change');
                    }
                }
            }), 2500
        });
    }
});

function resetForm() {
    let now = new Date();
    let month = now.getMonth() + 1;
    if (month < 10) {
        month = '0'+month;
    }
    let today = now.getDate()  + '-' + (month) + '-' + now.getFullYear();
    // $('#no_sep').val(null);
    $('#tgl_rencana_inap').val(today);
    // $('#no_kartu').val(null);
    // $('#nosuratkontrol').val(null);
    $('#tgl_rencanakontrol').val(today);
    $('#jenis_pelayanan').val('Rawat Jalan').trigger('change');
    $('#kode_poli').val(null).trigger('change');
    $('#nama_spesialis').val(null).trigger('change');
    $('#dokterdpjp_kode').val(null).trigger('change');
    $('#dokterdpjp_nama').val(null).trigger('change');
}


$('.pickadate-w-month').pickadate({
    format: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: 99,
    formatSubmit: 'dd-mm-yyyy',
});