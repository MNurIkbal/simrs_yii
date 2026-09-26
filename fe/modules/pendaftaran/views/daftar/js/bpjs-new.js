$(document).ready(function(){
    $(".select2AsalRujukan").select2();
    $(".select2KasusKecelakaan").select2();
    $(".select2PpkRujukan").select2({
        placeholder: "PPK Rujukan",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-faskes-new",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });

    $(".select2Diagnosa").select2({
        placeholder: "Pilih Diagnosa Awal",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-diagnosa-new",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });

    $(".select2Dpjp").select2({
        placeholder: "Pilih Dokter DPJP",
        ajax: {
            url: "/api/bpjs/referensi-dpjp",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });

    $(".select2KelasRawat").select2({
        placeholder: "Pilih Kelas Rawat",
        ajax: {
            url: "/api/bpjs/referensi-kelas-rawat",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });

    $(".select2Provinsi").select2({
        placeholder: "Pilih Provinsi",
        ajax: {
            url: "/api/bpjs/referensi-provinsi",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });

    $(".select2Poli").select2({
        placeholder: "Pilih Poli Tujuan",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-poli-new",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }

                return query;
            },
        },
    });
})

$('#tanggal_sep_1, #tanggal_sep, #tanggal_rujukan, #tanggal_kejadian').pickadate({
    format: 'dd mmm yyyy',
    formatSubmit: 'yyyy-mm-dd',
    onStart: function () {
        var date = new Date();
        this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    }
});

$('#jenis_rujukan').change(function () {
    var base = $("input:radio[name='jenis_rujukan']:checked").val();
    if (base == 1) {
        $('#base-rujukan').show();
        $('#base-rujukan-manual').hide();
    } else {
        $('#base-rujukan').hide();
        $('#base-rujukan-manual').show();
    }
});

$('#modal-rujukan').on('click', function(e){
    
})


$('#kasus_kecelakaan').change(function () {
    var base = $(this).val();


    if (base == 0) {
        $('.kasus_kecelakaan_form').hide();
        $('#status_suplesi').val('0');
    } else if ( base == 3 ){
        $('.kasus_kecelakaan_form').show();
        $('.suplesi_form').hide();
        $('#status_suplesi').val('0');
    } 
    else {

        swal({
            title:"Perhatian!", 
            text:"Apakah ini merupakan kasus kecelakaan lalu lintas baru?", 
            type:"info",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonText: "Ya",
        }, function(i) {
            if (i) {
                $('.kasus_kecelakaan_form').show();
                $('.suplesi_form').hide();
                $('#status_suplesi').val('0');
            } else {
                $('.suplesi_form').show();
                $('.kasus_kecelakaan_form').hide();
                $('#status_suplesi').val('1');

                $('#button-list-sep').click();
            }
        });
    }
});

$('.cari_rujukan').click(function() {
    var no_rujukan = $('#no_rujukan').val();
    var asal_rujukan = $('#asal_rujukan_1').val();
    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/rujukan',
        data: {
            nomor: no_rujukan,
            asal_rujukan: asal_rujukan
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan').html(_html).attr('disabled', true);
            $('.err-no-rujukan').html('');
        },
        success: function (res) {
            var resbpjs = res.response.metaData;
            if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-rujukan').html(message);
            } else {
                // disini set sebagian data sep 
                var response = res.response.response;
                var rujukan = response.rujukan
                var peserta = response.rujukan.peserta;
                console.log(response);
                // disini set sebagian data sep 

                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                if(data_pasien != '' && data_pasien.nama_pasien){
                    var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                }
                var namePasien = $('#inf-pasien-namapasien').text();
                if( (cek_pasien && cek_pasien != cek_bpjs) || (pendaftaranol_id != '' && namePasien.toLowerCase() != cek_bpjs) ){
                    // docoNotification('warning', "Peringatan BPJS", "Nama Pasien yang diinputkan berbeda dengan Nama BPJS");

                    // Pnotify
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + data.nama_pasien
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
                    })).get().on('pnotify.confirm', function () {
                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();

                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_nik').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            // $(".skdp-form").show();
                        } else {
                            statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                            $('.skdp-form').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                        if (peserta.statusPeserta.kode == 0) {
                            // poli tujuan
                            var poliRujukan = rujukan.poliRujukan;
                            var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                            $("#poli_tujuan").append(option);
                            $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                            // diagnosaawal
                            var diagnosa = rujukan.diagnosa;
                            var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                            $("#diagnosa_awal").append(option);
                            $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                            // asal rujukan
                            var provPerujuk = rujukan.provPerujuk;
                            var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                            $("#ppk_rujukan").append(option);
                            $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');
                            
                            $('#no_kartu').val(rujukan.peserta.noKartu);
                            $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                            $('#asal_rujukan').val(response.asalFaskes).trigger('change');
                            $('#no_rujukan_1').val(rujukan.noKunjungan);
                            $('#kelas_rawat').val(rujukan.peserta.hakKelas.kode);
                            $('#nomr').val(rujukan.peserta.mr.noMR);
                            $('#no_telp').val(rujukan.peserta.mr.noTelepon);

                            $('#hide-pelayanan').val(rujukan.pelayanan.kode);

                            $('#kode_dpjp').val(null).trigger('change'); 
                            if (response.lastPoli) {
                                new PNotify({
                                    title: '',
                                    text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                                    addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                                    type: 'info'
                                });
                                $(".skdp-form").show();
                                $("#is_skdp").val('1');


                                // var newOption = new Option('--Pilih Dokter DPJP--', null, false, false);
                                // $('#kode_dpjp').append(newOption);
                                $("#kode_dpjp").attr("data-placeholder","--Pilih Dokter DPJP--");
                                // foreach 
                                $.each(response.dokterDpjp.response.list, function( index, value ) {
                                    var newOption = new Option(value.nama, value.kode, false, false);
                                    $('#kode_dpjp').append(newOption);
                                });
                                $('#kode_dpjp').trigger('change');
                            } else {
                                $(".skdp-form").hide();
                                $("#is_skdp").val('0');
                            }

                            // form skdp
                            // $('.skdp-form').show();
                        }
                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ rujukan.peserta.noKartu + '&tgl_sep='+  tglSep );

                    }).on('pnotify.cancel', function () {

                    });
                } else {
                    
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                        $('.skdp-form').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    if (peserta.statusPeserta.kode == 0) {
                        // poli tujuan
                        var poliRujukan = rujukan.poliRujukan;
                        var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                        $("#poli_tujuan").append(option);
                        $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                        // diagnosaawal
                        var diagnosa = rujukan.diagnosa;
                        var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                        $("#diagnosa_awal").append(option);
                        $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                        // asal rujukan
                        var provPerujuk = rujukan.provPerujuk;
                        var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                        $("#ppk_rujukan").append(option);
                        $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');
                        
                        $('#no_kartu').val(rujukan.peserta.noKartu);
                        $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                        $('#asal_rujukan').val(response.asalFaskes).trigger('change');
                        $('#no_rujukan_1').val(rujukan.noKunjungan);
                        $('#kelas_rawat').val(rujukan.peserta.hakKelas.kode);
                        $('#nomr').val(rujukan.peserta.mr.noMR);
                        $('#no_telp').val(rujukan.peserta.mr.noTelepon);

                        $('#hide-pelayanan').val(rujukan.pelayanan.kode);

                        $('#kode_dpjp').val(null).trigger('change'); 
                        if (response.lastPoli) {
                            new PNotify({
                                title: '',
                                text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                                addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                                type: 'info'
                            });
                            $(".skdp-form").show();
                            $("#is_skdp").val('1');


                            // var newOption = new Option('--Pilih Dokter DPJP--', null, false, false);
                            // $('#kode_dpjp').append(newOption);
                            $("#kode_dpjp").attr("data-placeholder","--Pilih Dokter DPJP--");
                            // foreach 
                            $.each(response.dokterDpjp.response.list, function( index, value ) {
                                var newOption = new Option(value.nama, value.kode, false, false);
                                $('#kode_dpjp').append(newOption);
                            });
                            $('#kode_dpjp').trigger('change');
                        } else {
                            $(".skdp-form").hide();
                            $("#is_skdp").val('0');
                        }

                        // form skdp
                        // $('.skdp-form').show();
                    }
                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ rujukan.peserta.noKartu + '&tgl_sep='+  tglSep );
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan').html(_html).attr('disabled', false);
        }
    });
});

$('.cari_rujukan_bayi').click(function() {
    var no_rujukan = $('#no_rujukan').val();
    var asal_rujukan = $('#asal_rujukan_1').val();
    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/rujukan',
        data: {
            nomor: no_rujukan,
            asal_rujukan: asal_rujukan
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan').html(_html).attr('disabled', true);
            $('.err-no-rujukan').html('');
        },
        success: function (res) {
            var resbpjs = res.response.metaData;
            if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-rujukan').html(message);
            } else {
                // disini set sebagian data sep 
                var response = res.response.response;
                var rujukan = response.rujukan
                var peserta = response.rujukan.peserta;
                console.log(response);
                // disini set sebagian data sep 

                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                if(data_pasien != '' && data_pasien.nama_pasien){
                    var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                }
                var namePasien = $('#inf-pasien-namapasien').text();
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    // docoNotification('warning', "Peringatan BPJS", "Nama Pasien yang diinputkan berbeda dengan Nama BPJS");

                    // Pnotify
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + data.nama_pasien
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
                    })).get().on('pnotify.confirm', function () {
                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();

                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                        $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                        $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            // $(".skdp-form").show();
                        } else {
                            statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                            $('.skdp-form').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                        if (peserta.statusPeserta.kode == 0) {
                            // poli tujuan
                            var poliRujukan = rujukan.poliRujukan;
                            var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                            $("#poli_tujuan").append(option);
                            $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                            // diagnosaawal
                            var diagnosa = rujukan.diagnosa;
                            var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                            $("#diagnosa_awal").append(option);
                            $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                            // asal rujukan
                            var provPerujuk = rujukan.provPerujuk;
                            var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                            $("#ppk_rujukan").append(option);
                            $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');
                            
                            $('#no_kartu').val(rujukan.peserta.noKartu);
                            $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                            $('#asal_rujukan').val(response.asalFaskes).trigger('change');
                            $('#no_rujukan_1').val(rujukan.noKunjungan);
                            $('#kelas_rawat').val(rujukan.peserta.hakKelas.kode);
                            $('#nomr').val(rujukan.peserta.mr.noMR);
                            $('#no_telp').val(rujukan.peserta.mr.noTelepon);

                            $('#hide-pelayanan').val(rujukan.pelayanan.kode);

                            $('#kode_dpjp').val(null).trigger('change'); 
                            if (response.lastPoli) {
                                new PNotify({
                                    title: '',
                                    text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                                    addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                                    type: 'info'
                                });
                                $(".skdp-form").show();
                                $("#is_skdp").val('1');


                                // var newOption = new Option('--Pilih Dokter DPJP--', null, false, false);
                                // $('#kode_dpjp').append(newOption);
                                $("#kode_dpjp").attr("data-placeholder","--Pilih Dokter DPJP--");
                                // foreach 
                                $.each(response.dokterDpjp.response.list, function( index, value ) {
                                    var newOption = new Option(value.nama, value.kode, false, false);
                                    $('#kode_dpjp').append(newOption);
                                });
                                $('#kode_dpjp').trigger('change');
                            } else {
                                $(".skdp-form").hide();
                                $("#is_skdp").val('0');
                            }

                            // form skdp
                            // $('.skdp-form').show();
                        }
                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ rujukan.peserta.noKartu + '&tgl_sep='+  tglSep );

                    }).on('pnotify.cancel', function () {

                    });
                } else {
                    
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                        $('.skdp-form').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    if (peserta.statusPeserta.kode == 0) {
                        // poli tujuan
                        var poliRujukan = rujukan.poliRujukan;
                        var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                        $("#poli_tujuan").append(option);
                        $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                        // diagnosaawal
                        var diagnosa = rujukan.diagnosa;
                        var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                        $("#diagnosa_awal").append(option);
                        $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                        // asal rujukan
                        var provPerujuk = rujukan.provPerujuk;
                        var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                        $("#ppk_rujukan").append(option);
                        $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');
                        
                        $('#no_kartu').val(rujukan.peserta.noKartu);
                        $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                        $('#asal_rujukan').val(response.asalFaskes).trigger('change');
                        $('#no_rujukan_1').val(rujukan.noKunjungan);
                        $('#kelas_rawat').val(rujukan.peserta.hakKelas.kode);
                        $('#nomr').val(rujukan.peserta.mr.noMR);
                        $('#no_telp').val(rujukan.peserta.mr.noTelepon);

                        $('#hide-pelayanan').val(rujukan.pelayanan.kode);

                        $('#kode_dpjp').val(null).trigger('change'); 
                        if (response.lastPoli) {
                            new PNotify({
                                title: '',
                                text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                                addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                                type: 'info'
                            });
                            $(".skdp-form").show();
                            $("#is_skdp").val('1');


                            // var newOption = new Option('--Pilih Dokter DPJP--', null, false, false);
                            // $('#kode_dpjp').append(newOption);
                            $("#kode_dpjp").attr("data-placeholder","--Pilih Dokter DPJP--");
                            // foreach 
                            $.each(response.dokterDpjp.response.list, function( index, value ) {
                                var newOption = new Option(value.nama, value.kode, false, false);
                                $('#kode_dpjp').append(newOption);
                            });
                            $('#kode_dpjp').trigger('change');
                        } else {
                            $(".skdp-form").hide();
                            $("#is_skdp").val('0');
                        }

                        // form skdp
                        // $('.skdp-form').show();
                    }
                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ rujukan.peserta.noKartu + '&tgl_sep='+  tglSep );
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan').html(_html).attr('disabled', false);
        }
    });
});

$('.cari_rujukan_manual').click(function() {
    var tglSEP = $('#tanggal_sep_1').val();
    var nokartu = $('#no_kartu').val();
    var isktp = $("input:radio[name='jenis_kartu']:checked").val();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: nokartu,
            tglSEP: tglSEP,
            isktp: isktp == 2 ? 1 : 0,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan_manual').html(_html).attr('disabled', true);
            $('.err-no-kartu').html('');
        },
        success: function (res) {

            var resbpjs = res.response.metaData;
            if(resbpjs.code == null) {
                var message = "Maaf Server BPJS sedang mengalami gangguan";
                $('.err-no-kartu').html(message);
            }
            else if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-kartu').html(message);
            } else {
                var response = res.response.response;
                var peserta = response.peserta;
                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                var namePasien = $('#bpjs-nama-pasien').val();
                var cek_pasien = namePasien.toLowerCase();

                // if(data_pasien != '' && data_pasien.nama_pasien){
                //     var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                // }
                
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + namePasien
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
                    })).get().on('pnotify.confirm', function () {

                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();
                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_no_kartu').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html("<strong> NIK : </strong> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<strong>  Tanggal Lahir : </strong> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<strong>  Jenis Peserta : </strong> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<strong>  Hak Kelas : </strong> " + peserta.hakKelas.keterangan);
                        if(is_ranap == 1) {
                            $(".form-poli_tujuan").hide();
                            // $(".dpjp_form").hide();
                            $("#jenis_pelayanan").prop("disabled", true);
                        }
                        else {
                            $(".form-poli_tujuan").show();
                            $(".dpjp_form").hide();
                            $("#jenis_pelayanan").prop("disabled", false);
                        }

                        $("#asal_rujukan").val(2).trigger('change');
                        $("#no_asuransi").val(peserta.noKartu);
                        $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<strong>  TMT/TAT : </strong> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<strong>  Kode/Provinsi : </strong> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            $('#kelas_rawat').val(peserta.hakKelas.kode);
                        } else {
                            statusPeserta = '<strong>  Status Peserta : </strong> ' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<strong> Status Peserta : </strong> " + statusPeserta);

                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );
                        $("#no_telp").val(peserta.mr.noTelepon);
                        // form skdp
                        // $('.skdp-form').hide();
                        $('.skdp-form').show();

                    }).on('pnotify.cancel', function () {

                    });
                } else {
                    
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    $("#no_asuransi").val(peserta.noKartu);
                    $("#no_telp").val(peserta.mr.noTelepon);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                        $('.skdp-form').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    if (peserta.statusPeserta.kode == 0) {
                        // poli tujuan
                        var poliRujukan = rujukan.poliRujukan;
                        var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                        $("#poli_tujuan").append(option);
                        $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                        // diagnosaawal
                        var diagnosa = rujukan.diagnosa;
                        var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                        $("#diagnosa_awal").append(option);
                        $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                        // asal rujukan
                        var provPerujuk = rujukan.provPerujuk;
                        var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                        $("#ppk_rujukan").append(option);
                        $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');
                        
                        $('#no_kartu').val(rujukan.peserta.noKartu);
                        $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                        $('#asal_rujukan').val(response.asalFaskes).trigger('change');
                        $('#no_rujukan_1').val(rujukan.noKunjungan);
                        $('#kelas_rawat').val(rujukan.peserta.hakKelas.kode);
                        $('#nomr').val(rujukan.peserta.mr.noMR);
                        $('#no_telp').val(rujukan.peserta.mr.noTelepon);

                    // form skdp
                    $('.skdp-form').show();
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan_manual').html(_html).attr('disabled', false);
        }
    });
});

$('.cari_rujukan_manual_bayi').click(function() {
    var tglSEP = $('#tanggal_sep_1').val();
    var nokartu = $('#no_kartu').val();
    var isktp = $("input:radio[name='jenis_kartu']:checked").val();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: nokartu,
            tglSEP: tglSEP,
            isktp: isktp == 2 ? 1 : 0,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan_manual').html(_html).attr('disabled', true);
            $('.err-no-kartu').html('');
        },
        success: function (res) {
            var resbpjs = res.response.metaData;
            if(resbpjs.code == null) {
                var message = "Maaf Server BPJS sedang mengalami gangguan";
                $('.err-no-kartu').html(message);
            }
            else if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-kartu').html(message);
            } else {
                var response = res.response.response;
                var peserta = response.peserta;
                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                if(data_pasien != '' && data_pasien.nama_pasien){
                    var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                }
                var namePasien = $('#bpjs-nama-pasien').val();
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + namePasien
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
                    })).get().on('pnotify.confirm', function () {

                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();
                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_no_kartu').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html("<strong> NIK : </strong> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<strong>  Tanggal Lahir : </strong> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<strong>  Jenis Peserta : </strong> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<strong>  Hak Kelas : </strong> " + peserta.hakKelas.keterangan);

                        $("#asal_rujukan").val(2).trigger('change');
                        $("#no_asuransi").val(peserta.noKartu);
                        $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<strong>  TMT/TAT : </strong> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<strong>  Kode/Provinsi : </strong> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            $('#kelas_rawat').val(peserta.hakKelas.kode);
                        } else {
                            statusPeserta = '<strong>  Status Peserta : </strong> ' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<strong> Status Peserta : </strong> " + statusPeserta);

                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );
                        $("#no_telp").val(peserta.mr.noTelepon);
                        // form skdp
                        // $('.skdp-form').hide();
                        $('.skdp-form').show();

                    }).on('pnotify.cancel', function () {

                    });

                } else {
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    $("#no_asuransi").val(peserta.noKartu);
                    $("#no_telp").val(peserta.mr.noTelepon);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                        $('#kelas_rawat').val(peserta.hakKelas.kode);
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );

                    // form skdp
                    $('.skdp-form').show();
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan').html(_html).attr('disabled', false);
        }
    });
});

$('.bpjs-back-step').click(function () {
    $('.bpjs-step-2').hide();
    $('.detail_peserta').hide();
    $('.bpjs-step-1').show();
});

$('.create-sep').click(function() {
    var data = $('#bpjs-new-form').serializeArray();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: nokartu,
            tglSEP: tglSEP,
            isktp: isktp == 2 ? 1 : 0,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.create-sep').html(_html).attr('disabled', true);
            $('#nosep').val('');
            $('.err-nosep').html('');
        },
        success: function(res){
            if (res.metadata.status == 200) {
                var result = res.response.result.metaData;
                var response = res.response.result.response;
                if (result.code == 200) {
                    $('#nosep').val(response.sep.noSep);
                    $('#no_sep').html(response.sep.noSep);
                    $('.create-sep').attr('disabled', true);
                    // $('.printSep').show();
                    // set value to form kunjungan
                    $('#igd-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    $('#bpjs-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    $('#ranap-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    // $('.bpjs-step-1').hide();
                    $('.bpjs-step-2').hide();
                    $('.bpjs-step-3').show();
                    window.open("/pendaftaran/end-point/print-sep?pendaftaran_id=" + res.response.model.pendaftaran_id, '_blank');
                } else {
                    new PNotify({
                        title: 'Error',
                        text: result.message,
                        addclass: 'alert alert-error alert-arrow-right alert-styled-right',
                        type: 'error'
                    });
                    $('#no_sep').html();
                    $('.err-nosep').html(result.message);
                    $('.create-sep').attr('disabled', false);
                    // $('.printSep').hide();
                    $('#igd-form').find('.bpjs-id').val('');
                    $('.bpjs-step-2').show();
                    $('.bpjs-step-3').hide();
                }
            }
        },
        success: function (res) {

            var resbpjs = res.response.metaData;
            if(resbpjs.code == null) {
                var message = "Maaf Server BPJS sedang mengalami gangguan";
                $('.err-no-kartu').html(message);
            }
            else if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-kartu').html(message);
            } else {
                var response = res.response.response;
                var peserta = response.peserta;
                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                var namePasien = $('#bpjs-nama-pasien').val();
                var cek_pasien = namePasien.toLowerCase();

                // if(data_pasien != '' && data_pasien.nama_pasien){
                //     var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                // }
                
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + namePasien
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
                    })).get().on('pnotify.confirm', function () {

                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();
                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_no_kartu').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html("<strong> NIK : </strong> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<strong>  Tanggal Lahir : </strong> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<strong>  Jenis Peserta : </strong> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<strong>  Hak Kelas : </strong> " + peserta.hakKelas.keterangan);
                        if(is_ranap == 1) {
                            $(".form-poli_tujuan").hide();
                            // $(".dpjp_form").hide();
                            $("#jenis_pelayanan").prop("disabled", true);
                        }
                        else {
                            $(".form-poli_tujuan").show();
                            $(".dpjp_form").hide();
                            $("#jenis_pelayanan").prop("disabled", false);
                        }

                        $("#asal_rujukan").val(2).trigger('change');
                        $("#no_asuransi").val(peserta.noKartu);
                        $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<strong>  TMT/TAT : </strong> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<strong>  Kode/Provinsi : </strong> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            $('#kelas_rawat').val(peserta.hakKelas.kode);
                        } else {
                            statusPeserta = '<strong>  Status Peserta : </strong> ' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<strong> Status Peserta : </strong> " + statusPeserta);

                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );
                        $("#no_telp").val(peserta.mr.noTelepon);
                        // form skdp
                        // $('.skdp-form').hide();
                        $('.skdp-form').show();

                    }).on('pnotify.cancel', function () {

                    });

                } else {
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    $("#no_asuransi").val(peserta.noKartu);
                    $("#no_telp").val(peserta.mr.noTelepon);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                        $('#kelas_rawat').val(peserta.hakKelas.kode);
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );

                    // form skdp
                    $('.skdp-form').show();
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan_manual').html(_html).attr('disabled', false);
        }
    });
});

$('.cari_rujukan_manual_bayi').click(function() {
    var tglSEP = $('#tanggal_sep_1').val();
    var nokartu = $('#no_kartu').val();
    var isktp = $("input:radio[name='jenis_kartu']:checked").val();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: nokartu,
            tglSEP: tglSEP,
            isktp: isktp == 2 ? 1 : 0,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan_manual').html(_html).attr('disabled', true);
            $('.err-no-kartu').html('');
        },
        success: function (res) {
            var resbpjs = res.response.metaData;
            if(resbpjs.code == null) {
                var message = "Maaf Server BPJS sedang mengalami gangguan";
                $('.err-no-kartu').html(message);
            }
            else if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-kartu').html(message);
            } else {
                var response = res.response.response;
                var peserta = response.peserta;
                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                if(data_pasien != '' && data_pasien.nama_pasien){
                    var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                }
                var namePasien = $('#bpjs-nama-pasien').val();
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + namePasien
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
                    })).get().on('pnotify.confirm', function () {

                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();
                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_no_kartu').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html("<strong> NIK : </strong> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<strong>  Tanggal Lahir : </strong> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<strong>  Jenis Peserta : </strong> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<strong>  Hak Kelas : </strong> " + peserta.hakKelas.keterangan);

                        $("#asal_rujukan").val(2).trigger('change');
                        $("#no_asuransi").val(peserta.noKartu);
                        $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<strong>  TMT/TAT : </strong> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<strong>  Kode/Provinsi : </strong> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            $('#kelas_rawat').val(peserta.hakKelas.kode);
                        } else {
                            statusPeserta = '<strong>  Status Peserta : </strong> ' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<strong> Status Peserta : </strong> " + statusPeserta);

                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );
                        $("#no_telp").val(peserta.mr.noTelepon);
                        // form skdp
                        // $('.skdp-form').hide();
                        $('.skdp-form').show();

                    }).on('pnotify.cancel', function () {

                    });

                } else {
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    $("#no_asuransi").val(peserta.noKartu);
                    $("#no_telp").val(peserta.mr.noTelepon);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                        $('#kelas_rawat').val(peserta.hakKelas.kode);
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );

                    // form skdp
                    $('.skdp-form').show();
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan_manual').html(_html).attr('disabled', false);
        }
    });
});

$('.cari_rujukan_manual_bayi').click(function() {
    var tglSEP = $('#tanggal_sep_1').val();
    var nokartu = $('#no_kartu').val();
    var isktp = $("input:radio[name='jenis_kartu']:checked").val();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/peserta',
        data: {
            nokartu: nokartu,
            tglSEP: tglSEP,
            isktp: isktp == 2 ? 1 : 0,
        },
        dataType: 'JSON',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.cari_rujukan_manual').html(_html).attr('disabled', true);
            $('.err-no-kartu').html('');
        },
        success: function (res) {
            var resbpjs = res.response.metaData;
            if(resbpjs.code == null) {
                var message = "Maaf Server BPJS sedang mengalami gangguan";
                $('.err-no-kartu').html(message);
            }
            else if (resbpjs.code != "200") { // case error get rujukan
                var message = resbpjs.message;
                $('.err-no-kartu').html(message);
            } else {
                var response = res.response.response;
                var peserta = response.peserta;
                console.log(peserta);
                var cek_bpjs = peserta.nama.toLowerCase();
                var cek_pasien = false;
                if(data_pasien != '' && data_pasien.nama_pasien){
                    var cek_pasien = data_pasien.nama_pasien.toLowerCase();
                }
                var namePasien = $('#bpjs-nama-pasien').val();
                if( (cek_pasien && cek_pasien != cek_bpjs) || (namePasien.toLowerCase() != cek_bpjs) ){
                    (new PNotify({
                        title: "Peringatan",
                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                            +" Nama Pasien BPJS : <strong>" + peserta.nama
                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + namePasien
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
                    })).get().on('pnotify.confirm', function () {

                        $('.bpjs-step-1').hide();
                        $('.bpjs-step-3').hide();
                        $('.detail_peserta').show();
                        $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                        $('#bpjsnew_detail_no_kartu').html('No Kartu : ' + peserta.noKartu);
                        $('#bpjsnew_detail_nik').html("<strong> NIK : </strong> " + peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html("<strong>  Tanggal Lahir : </strong> " + peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html("<strong>  Jenis Peserta : </strong> " + peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html("<strong>  Hak Kelas : </strong> " + peserta.hakKelas.keterangan);

                        $("#asal_rujukan").val(2).trigger('change');
                        $("#no_asuransi").val(peserta.noKartu);
                        $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                        var tmt = peserta.tglTMT;
                        var tat = peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html("<strong>  TMT/TAT : </strong> " + tmt + ' - ' + tat);
                        var kdProv = peserta.provUmum.kdProvider;
                        var nmProv = peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html("<strong>  Kode/Provinsi : </strong> " + kdProv + " - " + nmProv);
                        var statusPeserta = peserta.statusPeserta.keterangan;
                        if (peserta.statusPeserta.kode == 0) {
                            $('.bpjs-step-2').show();
                            $('.bpjs-step-3').hide();
                            $('#kelas_rawat').val(peserta.hakKelas.kode);
                        } else {
                            statusPeserta = '<strong>  Status Peserta : </strong> ' + statusPeserta + '</span>';
                            $('.bpjs-step-2').hide();
                            $('.bpjs-step-3').hide();
                        }
                        $('#bpjsnew_detail_status_peserta').html("<strong> Status Peserta : </strong> " + statusPeserta);

                        var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                        $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );
                        $("#no_telp").val(peserta.mr.noTelepon);
                        // form skdp
                        // $('.skdp-form').hide();
                        $('.skdp-form').show();

                    }).on('pnotify.cancel', function () {

                    });

                } else {
                    $('.bpjs-step-1').hide();
                    $('.bpjs-step-3').hide();

                    $('.detail_peserta').show();
                    $('#bpjsnew_detail_nama').html('<i class="fa fa-user"></i> ' + peserta.nama);
                    $('#bpjsnew_detail_nik').html('NIK : ' + peserta.nik);
                    $('#bpjsnew_detail_no_kartu').html("<i class='fa fa-user-circle'></i> " + peserta.noKartu);
                    $('#bpjsnew_detail_tgl_lahir').html("<i class='fa fa-calendar'></i> " + peserta.tglLahir);
                    $('#bpjsnew_detail_jenis_peserta').html("<i class='fa fa-user'></i> " + peserta.jenisPeserta.keterangan);
                    $('#bpjsnew_detail_hak_kelas').html("<i class='fa fa-list-ul'></i> " + peserta.hakKelas.keterangan);
                    $("#no_asuransi").val(peserta.noKartu);
                    $("#no_telp").val(peserta.mr.noTelepon);
                    var tmt = peserta.tglTMT;
                    var tat = peserta.tglTAT;
                    $('#bpjsnew_detail_tmt_tat').html("<i class='fa fa-thumbs-up'></i> " + tmt + ' - ' + tat);
                    var kdProv = peserta.provUmum.kdProvider;
                    var nmProv = peserta.provUmum.nmProvider;
                    $('#bpjsnew_detail_ppk_rujukan').html("<i class='fa fa-database'></i> " + kdProv + " - " + nmProv);
                    var statusPeserta = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == 0) {
                        $('.bpjs-step-2').show();
                        $('.bpjs-step-3').hide();
                        $('#kelas_rawat').val(peserta.hakKelas.kode);
                    } else {
                        statusPeserta = '<span class="text-danger">' + statusPeserta + '</span>';
                        $('.bpjs-step-2').hide();
                        $('.bpjs-step-3').hide();
                    }
                    $('#bpjsnew_detail_status_peserta').html("<i class='fa fa-info'></i> " + statusPeserta);

                    var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
                    $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu='+ peserta.noKartu + '&tgl_sep='+  tglSep );

                    // form skdp
                    $('.skdp-form').show();
                }
            }
        },
        complete: function() {
            var _html = i18next.t('<i class="fa fa-search"></i> ' + 'Cari');
            $('.cari_rujukan_manual').html(_html).attr('disabled', false);
        }
    });
});

$('.bpjs-back-step').click(function () {
    $('.bpjs-step-2').hide();
    $('.detail_peserta').hide();
    $('.bpjs-step-1').show();
});

// $('.bpjs-back-step').click(function () {
//     clearBpjsForm();
// });

$('.create-sep').click(function() {
    var data = $('#bpjs-new-form').serializeArray();
    var pendaftaran_id = $("#bpjs-pendaftaran_id").val();

    $(this).docoForm('click', {
        skipConfirm: true,
        skipSuccessNotif: true,
        method: 'POST',
        data: data,
        url: window.location.origin + '/pendaftaran/end-point/create-sep-new',
        beforeSend: function () {
            var _html = i18next.t('Memuat...');
            $('.create-sep').html(_html).attr('disabled', true);
            $('#nosep').val('');
            $('.err-nosep').html('');
        },
        success: function(res){
            if (res.metadata.status == 200) {
                var result = res.response.result.metaData;
                var response = res.response.result.response;
                if (result.code == 200) {
                    $('#nosep').val(response.sep.noSep);
                    $('#no_sep').html(response.sep.noSep);
                    $('.create-sep').attr('disabled', true);
                    // $('.printSep').show();
                    // set value to form kunjungan
                    $('#igd-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    $('#bpjs-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    $('#ranap-form').find('.bpjs-id').val(res.response.model.bpjs_id);
                    // $('.bpjs-step-1').hide();
                    $('.bpjs-step-2').hide();
                    $('.bpjs-step-3').show();
                    window.open("/pendaftaran/end-point/print-sep?pendaftaran_id=" + res.response.model.pendaftaran_id, '_blank');
                } else {
                    new PNotify({
                        title: 'Error',
                        text: result.message,
                        addclass: 'alert alert-error alert-arrow-right alert-styled-right',
                        type: 'error'
                    });
                    $('#no_sep').html();
                    $('.err-nosep').html(result.message);
                    $('.create-sep').attr('disabled', false);
                    // $('.printSep').hide();
                    $('#igd-form').find('.bpjs-id').val('');
                    $('.bpjs-step-2').show();
                    $('.bpjs-step-3').hide();
                }
            }
        },
        complete: function () {
            $('.create-sep').html(i18next.t('Buat SEP'));
            $('.create-sep').attr('disabled', false);

        }
    });
});

// $("#asal_rujukan").on('change', function(){
//     var _val = $(this).val();
//     if(_val == 2) {
//         $(".dpjp_form").show();
//     }
//     else {
//         $(".dpjp_form").hide();
//     }
// });
