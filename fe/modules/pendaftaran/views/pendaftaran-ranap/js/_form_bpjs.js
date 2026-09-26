$('#pickadateSep, #pickadateRujukan').pickadate({
    format: 'dd mmm yyyy',
    formatSubmit: 'yyyy-mm-dd',
    onStart: function () {
        var date = new Date();
        this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    }
});


$('#source_peserta').change(function () {
    var sourcePeserta = $("input:radio[name='source_peserta']:checked").val();
    if (sourcePeserta == 1) {
        $('.asal_rujukan_form').show();
    } else {
        $('.asal_rujukan_form').hide();
    }
});

$('.cariPeserta').click(function () {
    var sourcePeserta = $("input:radio[name='source_peserta']:checked").val();
    if (sourcePeserta == 1) { // berdasarkan no rujukan
        getRujukan();
    } else if (sourcePeserta == 2) { // berdasarkan no bpjs / KTP
        getPeserta();
    } else { // berdasarkan no SEP | under construction
        alert('under contstruction');
    }

});

$('.bpjs-back-step').click(function () {
    $('.bpjs-step-2').hide();
    $('.detail_peserta').hide();
    $('.bpjs-step-1').show();
});

function getPeserta() {
    var is_ktp = 0;
    if ($('#nomor_cari').val().length > 15) {
        is_ktp = 1;
    }
    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: $('#nomor_cari').val(),
            tglSEP: $('#pickadateSep').val(),
            isktp: is_ktp
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cariPeserta').html(_html).attr('disabled', true);
            $('.err_nomor_cari').html('');
            $('#nokartuasuransi').val('');
            $('#poliTujuan').val('').trigger('change');
            $('#ppkrujukan').val('').trigger('change');
            $('#norujukan').val('');
            $('#norekammedik').val('');
            $('#diagnosaAwal').val('').trigger('change');
        },
        success: function (res) {
            console.log(res);
            if (res.metadata.status == 200) {
                var result = res.response.metaData;
                if (result.code == 200) {
                    var response = res.response.response;
                    var peserta = response.peserta;

                    $('.bpjs-step-1').hide();
                    $('.detail_peserta').show();

                    $('#bpjs_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjs_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjs_detail_no_kartu').html("<i class='fa fa-address-card'></i> " + peserta.noKartu);
                    $('#bpjs_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjs_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjs_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    var tat = peserta.tglTAT;
                    var tmt = peserta.tglTMT;
                    $('#bpjs_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjs_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + ' - ' + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode != 0) {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                    } else {
                        $('.bpjs-step-2').show();
                        $('#nokartuasuransi').val(peserta.noKartu);
                    }
                    $('#bpjs_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    var newOption = new Option(nmProv, kdProv, true, true);
                    $("#ppkrujukan").append(newOption);
                    $('#ppkrujukan').val(kdProv).trigger('change');

                    $('#hidden-klsrawat').val(peserta.hakKelas.kode);

                } else {
                    $('.err_nomor_cari').html(result.message);
                }
            }
        },
        complete: function () {
            $('.cariPeserta').html('<i class="fa fa-search"> ' + i18next.t('Cari')).removeAttr('disabled');
            $('.select2-container--default').css('width', '100');
        }
    });
}

function getRujukan() {
    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/rujukan',
        data: {
            nomor: $('#nomor_cari').val(),
            asal_rujukan: $('#asal_rujukan_dummy').val()
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cariPeserta').html(_html).attr('disabled', true);
            $('.err_nomor_cari').html('');
            $('#nokartuasuransi').val('');
            $('#poliTujuan').val('').trigger('change');
            $('#ppkrujukan').val('').trigger('change');
            $('#norujukan').val('');
            $('#norekammedik').val('');
            $('#diagnosaAwal').val('').trigger('change');
        },
        success: function (res) {
            console.log(res)
            if (res.metadata.status == 200) {
                var result = res.response.metaData;
                if (result.code == 200) {
                    var response = res.response.response;
                    var rujukan = response.rujukan;
                    var peserta = rujukan.peserta;
                    $('.bpjs-step-1').hide();
                    $('.detail_peserta').show();
                    $('#bpjs_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjs_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjs_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjs_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjs_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjs_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjs_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + ' - ' + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode != 0) {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                    } else {
                        $('.bpjs-step-2').show();
                        $('#nokartuasuransi').val(peserta.noKartu);
                        $('#asal_rujukan').val($('#asal_rujukan_dummy').val());
                    }
                    $('#bpjs_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    $('#hidden-klsrawat').val(peserta.hakKelas.kode);

                    // poli tujuan
                    var poliRujukan = rujukan.poliRujukan;
                    var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                    $("#poliTujuan").append(option);
                    $('#poliTujuan').val(poliRujukan.kode).trigger('change');

                    // diagnosaawal
                    var diagnosa = rujukan.diagnosa;
                    var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                    $("#diagnosaAwal").append(option);
                    $('#diagnosaAwal').val(diagnosa.kode).trigger('change');

                    // no rujukan
                    var noRujukan = rujukan.noKunjungan;
                    $('#norujukan').val(noRujukan);

                    // jnspelayanan
                    var jnsPelayanan = rujukan.pelayanan;
                    $('#jnspelayanan').val(jnsPelayanan.kode).trigger('change');

                    // ppk rujukan
                    var newOption = new Option(nmProv, kdProv, true, true);
                    $("#ppkrujukan").append(newOption);
                    $('#ppkrujukan').val(kdProv).trigger('change');

                } else {
                    $('.err_nomor_cari').html(result.message);
                }
            }
        },
        complete: function () {
            $('.cariPeserta').html('<i class="fa fa-search"> ' + i18next.t('Cari')).removeAttr('disabled');
            $('.select2-container--default').css('width', '100');
        }
    });
}


function getListPoli() {
    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/list-poli',
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
        },
        success: function (res) {
            if (res.metadata.status == 200) {
                var result = res.response.metadata;
                if (result.code == 200) {
                    var response = res.response.response;
                    if (response) {
                        var list = response.list;
                        $('#poliTujuan option').remove();
                        data = [];
                        $.each(list, function (i, item) {
                            data.push({ id: item.kdPoli, text: item.nmPoli });
                        });
                        $('#poliTujuan').select2({
                            // placeholder: i18next.t('--pilih poli--'),
                            data: data,
                            // type: 'GET',
                            // quietMillis: 50,
                            // minimumInputLength: 1,
                        })
                    }
                } else {
                }
            } else {
            }

        },
        complete: function () {
        }
    });
}

$('#bpjsform-lakalantas').click(function () {
    var lakalantas = $(this).prop('checked');
    if (lakalantas == true) {
        $('.laka').slideDown().addClass('form-group');
    } else {
        $('.laka').slideUp().removeClass('form-group');
    }
});

$('.createSep').click(function () {
    var noKartu = $('#nokartuasuransi').val();
    var tglSep = $('#pickadateSep').val();
    // var ppkPelayanan = $('#ppkpelayanan').val(); // get from db
    var jnsPelayanan = $('#jnspelayanan').val();
    var klsRawat = $('#hidden-klsrawat').val();
    var noMR = $('#no_rekam_medik').val();
    var asalRujukan = $('#asal_rujukan').val();
    var tglRujukan = $('#pickadateRujukan').val();
    var noRujukan = $('#norujukan').val();
    var ppkRujukan = $('#ppkrujukan').val();
    var catatan = $('#catatansep').val();
    var diagAwal = $('#diagnosaAwal').val();
    var tujuan = $('#poliTujuan').val();
    var eksekutif = $('#bpjsform-eksekutif').is(':checked') ? 1 : 0;
    var cob = $('#bpjsform-cob').is(':checked') ? 1 : 0;
    var lakaLantas = $('#bpjsform-lakalantas').is(':checked') ? 1 : 0;
    var arr_penjamin = [];
    $('#bpjsform-penjamin input:checked').each(function () {
        arr_penjamin.push(this.value);
    });
    var penjamin = arr_penjamin.join();
    var lokasiLaka = $('#lokasilaka').val();
    var noTelp = $('#notelp').val();
    // var user = $('#pendaftaranform-no_rekam_medik').val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/create-sep',
        data: {
            noKartu: noKartu,
            tglSep: tglSep,
            //  ppkPelayanan:ppkPelayanan,
            jnsPelayanan: jnsPelayanan,
            klsRawat: klsRawat,
            noMR: noMR,
            asalRujukan: asalRujukan,
            tglRujukan: tglRujukan,
            noRujukan: noRujukan,
            ppkRujukan: ppkRujukan,
            catatan: catatan,
            diagAwal: diagAwal,
            tujuan: tujuan,
            eksekutif: eksekutif,
            cob: cob,
            lakaLantas: lakaLantas,
            penjamin: penjamin,
            lokasiLaka: lokasiLaka,
            noTelp: noTelp,
            // user:,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.createSep').html(_html).attr('disabled', true);
            $('#nosep').val('');
            $('.err-nosep').html('');
        },
        success: function (res) {
            console.log(res);
            if (res.metadata.status == 200) {
                var result = res.response.metaData;
                var response = res.response.response;
                if (result.code == 200) {
                    $('#nosep').val(response.sep.noSep);
                    $('.createSep').hide();
                    $('.printSep').show();
                } else {
                    $('.err-nosep').html(result.message);
                    $('.createSep').show();
                    $('.printSep').hide();
                }

                tableDaftarTerakhir.draw();
            }
        },
        complete: function () {
            $('.createSep').html(i18next.t('Buat SEP')).removeAttr('disabled');
        }
    });
});