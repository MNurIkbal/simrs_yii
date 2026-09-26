const str_ranap = 'ranap';
const str_igd = 'igd';
const faskes_rs = 2;
const rujukan_manual = 2;
const rujukan_rujuk = 1;
const pelayanan_ranap = 1;
var defaultKelasRawat = null;
var tujuanKunjTrue = 1;
var tujuanKunReset = 0;
var tujuanKunNormal = 0;
var tujuanKunProsedur = 1;
var tujuanKunKonsul = 2;
var infoResponseBpjs = [];


$(document).ready(function(){
    bindingRegionBpjs({
        province: $("#kode_provinsi"),
        city: $("#kode_kabupaten"),
        district: $("#kode_kecamatan")
    })

    $(".select2AsalRujukan").select2();
    $(".kode_kabupaten").select2();
    $(".kode_kecamatan").select2();
    $(".select2KasusKecelakaan").select2();
    $(".select2Bpjs").select2();
    $("select").on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })

    $("#asal_rujukan").on("change", function () {
        $("#ppk_rujukan").val(null).trigger("change");
    });

    $("#ppk_rujukan").select2({
        placeholder: "PPK Rujukan",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-faskes-new",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    asal_rujukan: $('#asal_rujukan').val(),
                    type: 'public'
                }

                return query;
            },
        },
    });
    $("#ppk_rujukan").on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })

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
            data: function (params) {
                let _poli_tujuan = $('#poli_tujuan').val();
                let _jenis_pelayanan = $('#jenis_pelayanan').val();
                let _tlg_sep = $('#tanggal_sep').val();
                var query = {
                    search: params.term,
                    type: 'public',
                    pelayanan: _jenis_pelayanan,
                    tgl: _tlg_sep,
                    poli: _poli_tujuan
                }

                return query;
            },
        },
    });

    $(".select2DpjpServe").select2({
        placeholder: "Pilih Dokter DPJP Melayani",
        ajax: {
            url: "/api/bpjs/referensi-dpjp",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    cacheId: 'dpjp-melayani',
                }

                return query;
            },
        },
        templateSelection: function (res) {
            nama_dpjp_melayani = res.text
            return res.text;
        }
    });

    $(".select2KelasRawat").select2({
        placeholder: "Pilih Kelas Rawat",
        ajax: {
            url: "/api/bpjs/referensi-kelas-rawat",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
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
    var date = new Date();
    $('#tanggal_sep_1').pickadate({
        // format: 'dd-mm-yyyy',
        format: 'dd mmm yyyy',
        formatSubmit: 'yyyy-mm-dd',
        max: [date.getFullYear(),date.getMonth(),date.getDate()],
        onStart: function () {
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });

    $('#tanggal_rujukan, #tanggal_kejadian').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'yyyy-mm-dd',
        max: [date.getFullYear(),date.getMonth(),date.getDate()],
        onStart: function () {
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });
    // hide beberapa field ketika kondisi IGD
    if (jenisPendaftaran == str_igd) {
        $(".asal_rujukan").hide();
        // $(".ppk_rujukan").hide();
        $(".tanggal_rujukan").hide();
        $(".no_rujukan").hide();
        $(".kelas_rawat").hide();
        $(".katarak").hide();

        refreshOptionSelect2($('#poli_tujuan'), [{ "id": "IGD", "text": "INSTALASI GAWAT DARURAT" }])
    } else {
        $(".asal_rujukan").show();
        $(".ppk_rujukan").show();
        $(".tanggal_rujukan").show();
        $(".no_rujukan").show();
        $(".kelas_rawat").show();
        $(".katarak").show();
    }

    if (jenisPendaftaran == str_ranap) {
        $("#jenis_pelayanan option[value='2']").remove();
        $(".form-poli_tujuan").hide();
        $('.kelas_rawat').show();
        $('.dpjp_form_melayani').hide();
    } else {
        $("#jenis_pelayanan option[value='1']").remove();
        $('.kelas_rawat').hide();
        $('.dpjp_form_melayani').show();
    }
    if(dataBpjs != null) {
        getPoliTujuan(dataBpjs.politujuan)
        getDpjpMelayani(dataBpjs.kode_dpjp_melayani, dataBpjs.nama_dpjp_melayani)
        // getPpkRujukan(dataBpjs.kode_ppk_perujuk, dataBpjs.nama_ppk_perujuk)
        setDpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), dataBpjs.politujuan, dataBpjs.kode_dpjp_spri)
        getDiagnosa(dataBpjs.diagnosaawal)
    }
});

$('#jenis_rujukan').change(function () {
    var base = $("input:radio[name='BpjsNewForm[jenis_rujukan]']:checked").val();
    if (base == 1) {
        $('#base-rujukan').show();
        $('#base-rujukan-manual').hide();
    } else {
        $('#base-rujukan').hide();
        $('#base-rujukan-manual').show();
        $('#no_rujukan').val(null).trigger('change')
        $('#no_rujukan_1').val(null).trigger('change')
    }
});

$('#poli_tujuan').change(function () {
    if ($(this).val() == 'IGD') {
        $('.frm-rujukan').hide();
        // meminimalisasi form jika poli IGD
        $(".asal_rujukan").hide();
        // $(".ppk_rujukan").hide();
        $(".tanggal_rujukan").hide();
        $(".no_rujukan").hide();
        $(".kelas_rawat").hide();
        $(".katarak").hide();
        // $(".dpjp_form").hide();
    } else {
        $('.frm-rujukan').show();
        // menormalkan form ketika bukan IGD
        $(".asal_rujukan").show();
        $(".ppk_rujukan").show();
        $(".tanggal_rujukan").show();
        $(".no_rujukan").show();
        // $(".kelas_rawat").show();
        $(".katarak").show();
        // $(".dpjp_form").show();
    }
});

$('#kasus_kecelakaan').change(function () {
    var base = $(this).val();
    if (base == 0) {
        $('.kasus_kecelakaan_form').hide();
        $('#status_suplesi').val('0');
    } else if (base == 3) {
        $('.kasus_kecelakaan_form').show();
        $('.suplesi_form').hide();
        $('#status_suplesi').val('0');
    } else {
        swal({
            title: "Perhatian!",
            text: "Apakah ini merupakan kasus kecelakaan lalu lintas baru?",
            type: "info",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonText: "Ya",
        }, function (i) {
            if (i) {
                $('.kasus_kecelakaan_form').show();
                $('.suplesi_form').hide();
                $('#status_suplesi').val('0');
                $("#tanggal_kejadian").focus()
            } else {
                $('.suplesi_form').show();
                $('.kasus_kecelakaan_form').hide();
                $('#status_suplesi').val('1');

                $('#button-list-sep').click();
            }
        });
    }
});

$('#no_surat_kontrol').on('change', function () {
    if($(this).val() == '') {
      return;
    }
    let url = window.location.origin + '/api/bpjs/cari-surat-kontrol?no_surat_kontrol=' + $(this).val();
    $.ajax({
        type: "GET",
        url: url,
        dataType: "JSON",
        success: function (res) {
            let responsebpjs = res.response;
            $("#option_dpjp").remove();
            $("#option_dpjp_pemberi").remove();

            if (responsebpjs.kodeDokter != null) {
                let option_dpjp = '<option id="option_dpjp" value="' + responsebpjs.kodeDokter + '" selected="">' + responsebpjs.namaDokter + '</option>'
                $("#kode_dpjp_melayani").append(option_dpjp);
                $('#kode_dpjp_melayani').val(responsebpjs.kodeDokter).trigger('change');
            }
            if (responsebpjs.kodeDokterPembuat) {
                let option_dpjp_pemberi = '<option id="option_dpjp_pemberi" value="' + responsebpjs.kodeDokterPembuat + '" selected="">' + responsebpjs.namaDokterPembuat + '</option>'
                $("#kode_dpjp").append(option_dpjp_pemberi);
                $('#kode_dpjp').val(responsebpjs.kodeDokterPembuat).trigger('change');
            } else {
                let option_dpjp_pemberi = '<option id="option_dpjp_pemberi" value="' + responsebpjs.kodeDokter + '" selected="">' + responsebpjs.namaDokter + '</option>'
                $("#kode_dpjp").append(option_dpjp_pemberi);
                $('#kode_dpjp').val(responsebpjs.kodeDokter).trigger('change');
            }
            if (responsebpjs.poliTujuan && jenisPendaftaran != str_ranap) {
                let option_poli_tujuan = '<option id="option_poli_tujuan" value="' + responsebpjs.poliTujuan + '" selected="">' + responsebpjs.namaPoliTujuan + '</option>'
                $("#poli_tujuan").append(option_poli_tujuan);
                $('#poli_tujuan').val(responsebpjs.poliTujuan).trigger('change');
            }
            if (responsebpjs.noSuratKontrol != null) {
                $('#no_surat_kontrol').val(responsebpjs.noSuratKontrol)
            }
        },
        error: function(res) {
            docoNotification("warning", "Peringatan!", res.responseJSON.response.text);
        }
    });
})

$(document).on('change', '#bpjsnewform-is_naikkelas_ranap', function (e) {
    e.preventDefault();

    if ($(this).prop("checked")) {
        $('.naik_kelas_rawat').show();
    } else {
        $('.naik_kelas_rawat').hide();
        $("#kelas_rawat").val(defaultKelasRawat).trigger('change');
        $(`[name^="BpjsNewForm[naik_kelas_rawat_inap]"]`).val('').change();
        $(`[name^="BpjsNewForm[pembiayaan]"]`).val('').change();
        $(`[name^="BpjsNewForm[nama_penganggung_jawab]"]`).val('').change();
    }
});

$(document).on('change', '[name="BpjsNewForm[pembiayaan]"]', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('[name="BpjsNewForm[nama_penganggung_jawab]"]').val('Pribadi').change();
    }
});

function getPoliTujuan(poli){
    var kode_poli;
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-poli-new',
        data: {
            search: poli
        },
        error: function() {
            console.log('Error get poli tujuan');
        },
        dataType: 'json',
        success: function(data) {
            if (data.results) {
                $.each(data.results, function (index, value) {
                    if (value.id == poli){
                        var newOption = new Option(value.text, value.id, false, false);
                        kode_poli = value.id;
                        $('#poli_tujuan').append(newOption);
                        return false;
                    }
                });
                $('#poli_tujuan').trigger('change');
                $('#poli_tujuan').val(kode_poli).trigger('change');
            }
        },
    });
}

function getDpjpMelayani(kode_dpjp, nama_dpjp){
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-dpjp',
        data: {
            search: nama_dpjp
        },
        error: function() {
            console.log('Error get DPJP melayani');
        },
        dataType: 'json',
        success: function(data) {
            if (data.results) {
                $.each(data.results, function (index, value) {
                    if (value.id == kode_dpjp){
                        var newOption = new Option(value.text, value.id, false, false);
                        $('#kode_dpjp_melayani').append(newOption);
                        return false;
                    }
                });
                $('#kode_dpjp_melayani').trigger('change');
                $('#kode_dpjp_melayani').val(kode_dpjp).trigger('change');
            }
        },
    });
}

function getPpkRujukan(kode_ppk, nama_ppk){
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-faskes-new',
        data: {
            search: nama_ppk,
            asal_rujukan: $('#asal_rujukan').val(),
        },
        error: function() {
            console.log('Error get PPK Rujukan');
        },
        dataType: 'json',
        success: function(data) {
            if (data.results) {
                $.each(data.results, function (index, value) {
                    if (value.id == kode_ppk){
                        var newOption = new Option(value.text, value.id, false, false);
                        $('#ppk_rujukan').append(newOption);
                        return false;
                    }
                });
                $('#ppk_rujukan').trigger('change');
                $('#ppk_rujukan').val(kode_ppk).trigger('change');
            }
        },
    });
}

function getDiagnosa(diagnosa){
    var kode_diagnosa;
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-diagnosa-new',
        data: {
            search: diagnosa
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            if (data.results) {
                $.each(data.results, function (index, value) {
                    var hitung_car = value.text.indexOf('-');
                    var res = value.text.substring(0, hitung_car - 1);
                    if (res == value.id){
                        var newOption = new Option(value.text, value.id, false, false);
                        kode_diagnosa = value.id;
                        $('#diagnosa_awal').append(newOption);
                        return false;
                    }
                });
                $('#diagnosa_awal').trigger('change');
                $('#diagnosa_awal').val(kode_diagnosa).trigger('change');
            }
        },
    });
}

function setDpjp(jnsPelayanan, tglSep, poliTujuan, kode_dpjp = null) {
    let url = window.location.origin + '/api/bpjs/referensi-dokter?jnsPelayanan=' + jnsPelayanan + '&tglSep=' + tglSep + '&poliTujuan=' + poliTujuan;
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'JSON',
        beforeSend: function () {
            $('#kode_dpjp').empty();
        },
        success: function (res) {
            $("#kode_dpjp").attr("data-placeholder", "--Pilih Dokter DPJP--");
            // foreach
            if (res.results) {
                $.each(res.results, function (index, value) {
                    var newOption = new Option(value.text, value.id, false, false);
                    $('#kode_dpjp').append(newOption);
                });
                $('#kode_dpjp').trigger('change');
                if (kode_dpjp != null) {
                    $('#kode_dpjp').val(kode_dpjp).trigger('change');
                }
            }
        }
    });
}

$('#btn-cari-rujukan').on('click', function() {
    /** on success */
    // $("html, body").animate({ scrollTop: $(document).height() }, 500);
    var jenis_rujukan = $('input[name="BpjsNewForm[jenis_rujukan]"]:checked').val();
    var asal_rujukan_1 = $('#asal_rujukan_1').val();
    var no_rujukan = $('#no_rujukan').val();
    var no_kartu = $('#no_kartu').val();
    var tgl_sep = $('#tanggal_sep_1').val();
    var jenis_kartu = $('input[name="BpjsNewForm[jenis_kartu]"]:checked').val();
    var validate = true;

    if (jenis_rujukan == null || jenis_rujukan == '') {
        docoNotification('error', "Jenis pencarian belum dipilih", '');
        validate = false;
    } else if (jenis_rujukan == rujukan_manual) {
        if (no_kartu == null || no_kartu == '') {
            docoNotification('error', "No Kartu harus diisi", "Silahkan input no kartu terlebih dahulu");
            validate = false;
        }
    } else {
        if (no_rujukan == null || no_rujukan == '') {
            docoNotification('error', "No Rujukan harus diisi", "Silahkan input no rujukan terlebih dahulu");
            validate = false;
        }
        if (tgl_sep == null || tgl_sep == '') {
            docoNotification('error', "Tanggal SEP harus diisi", "Silahkan input tanggal SEP terlebih dahulu");
            validate = false;
        }
    }

    if (validate) {
        $.ajax({
            url: '/pendaftaran/informasi-pasien/get-info-bpjs',
            type: 'GET',
            data: {
                jenis_rujukan: jenis_rujukan,
                asal_rujukan: asal_rujukan_1,
                no_rujukan: no_rujukan,
                no_kartu: no_kartu,
                pendaftaran_id:getUrlParam('id'),
                tanggal_sep: tgl_sep,
                jenis_kartu: jenis_kartu
            },
            dataType: 'JSON',
            success: function(response) {
                var peserta = response.peserta; // data peserta bpjs
                var asal_rujukan = asal_rujukan_1;
                var dataPostRanap = response.data_post_ranap;
                var post_ranap = response.post_ranap;
                var rujukan = {};
                var noRm = peserta.mr.noMR
                let rencanaKontrol = response?.rencana_kontrol;
                defaultKelasRawat = peserta.hakKelas.kode;
                infoResponseBpjs = response;
                if(noRm == null || noRm == '') {
                    noRm = (typeof response.no_rekam_medik != 'undefined') ? response.no_rekam_medik : dataPasien.no_rekam_medik
                }

                if (typeof post_ranap != 'undefined') {
                    no_rujukan = (post_ranap && jenisPendaftaran != str_igd) ? post_ranap.noSep : null;
                    var ppkrujukan_kode = (post_ranap) ? post_ranap.ppkPelayanan_kode : null;
                    var ppkrujukan_nama = (post_ranap) ? post_ranap.ppkPelayanan_nama : null;
                }
                var _disabled = (post_ranap || jenis_rujukan == rujukan_rujuk) ? true : false;

                if (post_ranap) {
                        asal_rujukan = faskes_rs
                }

                if (asal_rujukan == pelayanan_ranap) {
                    $('#no_surat_kontrol').val('');
                    $('.dpjp_form').show();
                } else {
                    $('#no_surat_kontrol').val('');
                    $('.dpjp_form').hide();
                }
                $("#no_rujukan_1").val(no_rujukan);

                var date = new Date();
                renderPickadate($('#tanggal_sep'), {
                    lowerThanToday: true,
                    defaultValue: $("#tanggal_sep_1").pickadate().val(),
                    max: [date.getFullYear(),date.getMonth(),date.getDate()],
                    readOnly: true
                })
                $("#tanggal_sep").prop("readonly", true);
                if (jenis_rujukan == rujukan_rujuk) { // rujukan
                    rujukan = response.rujukan;
                    let provPerujuk = rujukan.provPerujuk;

                    if (typeof post_ranap != 'undefined') {
                        no_rujukan = (post_ranap && jenisPendaftaran != str_igd) ? post_ranap.noSep : null;
                        ppkrujukan_kode = (post_ranap) ? post_ranap.ppkPelayanan_kode : null;
                        ppkrujukan_nama = (post_ranap) ? post_ranap.ppkPelayanan_nama : null;
                    } else {
                        no_rujukan = rujukan.noKunjungan;
                        ppkrujukan_kode = provPerujuk.kode;
                        ppkrujukan_nama = provPerujuk.nama;
                    }

                    // poli Tujuan
                    var poliRujukan = rujukan.poliRujukan;
                    var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                    // var peserta = response.peserta;

                    $("#poli_tujuan").append(option);
                    $('#poli_tujuan').val(poliRujukan.kode).trigger('change');

                    // form rujukan
                    $('.frm-rujukan').show();
                    $('#asal_rujukan').val(asal_rujukan).trigger('change');

                    // asal rujukan
                    var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                    $("#ppk_rujukan").append(option);
                    $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');

                    var tanggal_rujukan = new Date(rujukan.tglKunjungan);
                    renderPickadate($('#tanggal_rujukan'), {
                        lowerThanToday: true,
                        defaultValue: tanggal_rujukan
                    })

                    /** Untuk kunjungan ke-2 */
                    if (response.lastPoli) {
                        new PNotify({
                            title: '',
                            text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                            addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                            type: 'info'
                        });

                        $("#no_surat_kontrol").val('');
                        $(".dpjp_form").show();

                        setDpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), response.lastPoli);
                    } else {
                        $("#no_surat_kontrol").val('');
                        $(".dpjp_form").hide();
                    }

                    // diagnosa awal
                    var diagnosa = rujukan.diagnosa;
                    var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                    $("#diagnosa_awal").append(option);
                    $('#diagnosa_awal').val(diagnosa.kode).trigger('change');

                    $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                    $('#no_rujukan_1').val(no_rujukan);
                }

                $("#asal_rujukan").prop("disabled", _disabled);
                if ($("#asal_rujukan").prop('disabled')) {
                    $("#asal_rujukan_hidden").val(asal_rujukan);
                }
                $("[name='BpjsNewForm[asal_rujukan]']").val(asal_rujukan).change();

                // ppk rujukan
                var option = new Option(ppkrujukan_nama, ppkrujukan_kode, true, true);
                $("#ppk_rujukan").prop("disabled", _disabled);
                if ($("#ppk_rujukan").prop('disabled')) {
                    $("#ppk_rujukan_hidden").val(ppkrujukan_kode);
                }
                $("#ppk_rujukan").append(option);
                $('#ppk_rujukan').val(ppkrujukan_kode).trigger('change');

                // $("#no_rujukan_1").prop("readonly", _disabledNoRujukan);
                $('#no_kartu').val(peserta.noKartu);
                $('#nomr').val(noRm);
                $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
                $("#no_telp").val(peserta.mr.noTelepon ? (peserta.mr.noTelepon).toString().replace(/\s/g, '') : null);

                if (post_ranap) {
                    let noSuratKontrol = rencanaKontrol?.length == 1 ? rencanaKontrol[0]?.noSuratKontrol : ''
                    $('#no_surat_kontrol').val(noSuratKontrol).change();
                    $(".dpjp_form").show();
                }

                /** Set info peserta */
                var begin = ': ';
                $('.nama-pasien').html(dataPasien.nama_pasien);
                $('.rm-pasien').html(dataPasien.no_rekam_medik);

                $('.info-no_kartu').html(begin + peserta.noKartu);
                $('.info-nik').html(begin + peserta.nik);
                $('.info-nama_peserta').html(begin + peserta.nama);
                $('.info-tgl_lahir').html(begin + peserta.tglLahir);
                $('.info-jenis_peserta').html(begin + peserta.jenisPeserta.keterangan);
                $('.info-hak_kelas').html(begin + peserta.hakKelas.keterangan);
                var tmt = peserta.tglTMT;
                var tat = peserta.tglTAT;
                $('.info-tmt_tat').html(begin + tmt + ' - ' + tat);
                var kdProv = peserta.provUmum.kdProvider;
                var nmProv = peserta.provUmum.nmProvider;
                var infoProvinsi = '-'
                if(kdProv != null && nmProv != null) {
                    infoProvinsi = kdProv + " - " + nmProv
                }
                $('.info-ppk_peserta').html(begin + infoProvinsi);
                var statusPeserta = peserta.statusPeserta.keterangan;
                $('.info-status').html(begin + statusPeserta);

                $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)
                $('.btn-detail-bpjs').show();
                $("#container-bpjs").show();

                if(peserta.nik != dataPasien.no_identitas_pasien) {
                    var header = 'Perhatian !';
                    var message = `NIK Nomor Kartu <strong>${no_kartu}</strong> tidak sama dengan NIK Pasien <strong>${dataPasien.nama_pasien}.</strong><br>Apakah Anda yakin akan melanjutkan ? `;
                    if(jenis_rujukan == rujukan_rujuk) {
                        message = `NIK Nomor Rujukan <strong>${no_rujukan}</strong> tidak sama dengan NIK Pasien <strong>${dataPasien.nama_pasien}.</strong><br>Apakah Anda yakin akan melanjutkan ? `;
                    }

                    var label = {
                        buttons: {
                            'Yes': 'btn btn-success button-yes',
                            'No': 'btn btn-danger button-no',
                        }
                    };
                    $.showQuestionDialog(header, message, label, function(reaction){
                        if (reaction =='No') {
                            $("#container-bpjs").hide();
                        }
                    })
                }

            },
            error: function(response) {
                var _response = response.responseJSON.response;
                docoNotification('error', _response.title, _response.text);
            }
        });
    }
});

function getUrlParam(param) {
    var url = window.location.search.substring(1);
    var variable = url.split('&');
    for (i = 0; i < variable.length; i++) {
        paramName = variable[i].split('=');
        if (paramName[0] === param) {
            return paramName[1] === undefined ? true : decodeURIComponent(paramName[1]);
        }
    }
    return false;
};

$(document).on("submit", "#form-create-sep", function(event) {
    event.preventDefault();
    
    let tanggal_sep = $('#tanggal_sep').val();
    let lastSep = infoResponseBpjs?.lastSep?.response?.histori ? infoResponseBpjs?.lastSep?.response?.histori : [];
    let kunjungan = [];
    let poli_tujuan = $('#poli_tujuan').val();
    let is_tujuan_kunj = $('#is_tujuan_kunj').val();
    if (lastSep.length > 0) {
        let ppkPelayananRs = infoResponseBpjs.ppkPelayananRs_nama;
        lastSep.every((item, i) => {
            if (item['ppkPelayanan'].toLowerCase() == ppkPelayananRs.toLowerCase() && item['noRujukan'] == $('[name="BpjsNewForm[no_rujukan]"]').val()) {
                kunjungan.push(item);
                return false;
            }
            return true;
        });
    }
    if (is_tujuan_kunj == tujuanKunjTrue || kunjungan.length === 0 ) {
        // Reset is tujuan kunjungan
        // $('#is_tujuan_kunj').val(tujuanKunReset);
    } else {
        lastPoli = infoResponseBpjs?.lastPoli;
        poliAsalRujuk = infoResponseBpjs?.rujukan?.poliRujukan?.kode

        if (poliAsalRujuk == 1 && !lastPoli) {  
            $('#tujuan_kunjungan').val(tujuanKunNormal);
        } else if (tanggal_sep == kunjungan[0].tglSep) {
            if (poli_tujuan == poliAsalRujuk) {
                $("#tujuan_kunjungan").val(tujuanKunNormal);
                url = '/pendaftaran/daftar-rajal/tujuan-prosedur-bpjs';
                modalTujuanKunjunganBpjs(url);
                return false;
            } else {
                $("#tujuan_kunjungan").val(tujuanKunNormal);
            }
        } else {
            if (poli_tujuan == poliAsalRujuk) {
                url = '/pendaftaran/daftar-rajal/tujuan-kunjungan-bpjs';
            } else {
                $("#tujuan_kunjungan").val(tujuanKunNormal);
                url = '/pendaftaran/daftar-rajal/assesment-pelayanan-bpjs?bedaPoli=true';
            }
            modalTujuanKunjunganBpjs(url);
            return false;
        }
        if(lastPoli) {
            docoNotification("warning", "Peringatan!", "Anda Sudah melakukan kunjungan konsultasi dokter pada poli " + lastPoli + " pada tanggal " + kunjungan[0].tglSep);
            return false;
        }
    }

    var _data = $("#form-create-sep").serializeArray();

    _data.push({
        name: 'BpjsNewForm[nama_dpjp_melayani]',
        value: $("#kode_dpjp_melayani option:selected" ).text()
    })

    _data.push({
        name: 'BpjsNewForm[kode_ppk_perujuk]',
        value: $("#ppk_rujukan").val()
    })

    _data.push({
        name: 'BpjsNewForm[nama_ppk_perujuk]',
        value: $("#ppk_rujukan option:selected" ).text()
    })

    _data.push({
        name: 'BpjsNewForm[kode_dpjp_spri]',
        value: $('#kode_dpjp').val()
      });

    _data.push({
        name: 'BpjsNewForm[nama_dpjp_spri]',
        value: $("#kode_dpjp option:selected" ).text()
    });

    if ($("#asal_rujukan").prop('disabled')) {
        _data.push({
            name: 'BpjsNewForm[asal_rujukan]',
            value: $("#asal_rujukan_hidden").val()
        });
    }

    if ($("#ppk_rujukan").prop('disabled')) {
        _data.push({
            name: 'BpjsNewForm[ppk_rujukan]',
            value: $("#ppk_rujukan_hidden").val()
        });
    }
    $(this).find('input[type="submit"]').docoForm("click", {
        data: _data,
        before: function() {
            return false;
        },
        success : function(response) {
            var pendaftaran_id = getUrlParam('id');
            localStorage.setItem("createSep-" + pendaftaran_id, response.no_sep);
            if(typeof response.text != 'undefined' && typeof response.title != 'undefined' && typeof response.no_sep != 'undefined') {
                new PNotify({
                    title: "Berhasil",
                    text:
                        response.title +
                        "<strong>" +
                            response.no_sep +
                        "</strong>" +
                      " telah berhasil diterbitkan, apakah Anda ingin melakukan cetak?",
                    addclass:
                      "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                      closer: false,
                      sticker: false,
                    },
                    hide: false,
                    confirm: {
                      confirm: true,
                      buttons: [
                        {
                          text: "Ya",
                          addClass: "btn btn-xs btn-success",
                        },
                        {
                          text: "Tidak",
                          addClass: "btn btn-xs btn-danger",
                        },
                      ],
                    },
                    history: {
                      history: false,
                    },
                  })
                    .get()
                    .on("pnotify.confirm", function () {
                      // Print SEP
                      window.open(
                        "/pendaftaran/end-point/print-sep?pendaftaran_id=" + pendaftaran_id + "&nosep=" + response.no_sep
                      );
                    });
            }
    
            // Delay 2 seconds
            setTimeout(function() {
                window.close()
            }, 6000);
        },
        error: function() {  
        }
    });
});

function modalTujuanKunjunganBpjs(url) {
    $('#modal_backdrop').modal('hide');

    setTimeout(function () {
        $('.btn-tujuan-kunjungan').attr('action', url)
        $('.btn-tujuan-kunjungan').trigger('click');
    }, 1000)
}
