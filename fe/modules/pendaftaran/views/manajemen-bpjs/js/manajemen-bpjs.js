
var nama_dpjp_melayani = '';
$(document).ready(function(){
    var on_load_kecelakaan = false;
    $('#cek_lokasi').val('false');
    $('input[name=jenis_pencarian]').prop('checked', true).trigger('change');

    bindingRegionBpjs({
        province: $("#kode_provinsi"),
        city: $("#kode_kabupaten"),
        district: $("#kode_kecamatan")
    })
    
    $(".select2AsalRujukan").select2();
    $(".kode_kabupaten").select2();
    $(".kode_kecamatan").select2();
    $(".select2KasusKecelakaan").select2();
    $("select").on('select2:close', ({ delegateTarget }) => {
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
            url: "/pendaftaran/manajemen-bpjs/referensi-provinsi",
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

    $(".select2DpjpServe").select2({
        placeholder: "PILIH DOKTER DPJP MELAYANI",
        ajax: {
            url: "/api/bpjs/referensi-dpjp",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public',
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

    $(".select2JenisPeserta").select2({
        placeholder: "PILIH JENIS KEPESERTAAN",
        ajax: {
            url: "/api/bpjs/referensi-kepesertaan",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public',
                }

                return query;
            },
        },
    });

    $('#tanggal_kejadian').pickadate({
        format: 'dd mmm yyyy',
        formatSubmit: 'yyyy-mm-dd',
        onStart: function () {
            var date = new Date();
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });

});

$(".btn-cari").click(function() {
    if ($('#no_sep').val() == null || $('#no_sep').val() == '') {
        docoNotification('error', "No SEP wajib diisi", "Silahkan inputkan no SEP");
    }
    resetForm();
    $('#info-pasien').hide();
    $.ajax({
        url: '/pendaftaran/manajemen-bpjs/get-pasien',
        type: 'GET',
        data: {
            no_sep: $('#no_sep').val()
        },
        dataType: 'JSON',
        success: function(response) {
            if (response.sep.metaData.code == 201)
            {
                docoNotification('error', "Maaf, Data tersebut tidak ada di BPJS", "Silahkan diulang untuk melakukan pencarian");
                //$('#info-pasien').show();
            } else {
                var peserta = response.peserta.response.peserta;
                var sep = response.sep.response;
                var rujukan = response.rujukan.response;
                rujukan_internal = response.rujukan_internal.response;
                console.log(peserta);
                console.log(sep);
                console.log(rujukan);

                //panel info bpjs
                $('#bpjsnew_detail_nama').html(peserta.nama+' - '+peserta.mr.noMR);
                $('#bpjsnew_detail_nik').html('<strong>:</strong> ' + peserta.nik);
                $('#bpjsnew_detail_tgl_lahir').html('<strong>:</strong> ' + peserta.tglLahir);
                $('#bpjsnew_detail_jenis_peserta').html('<strong>:</strong> ' + peserta.jenisPeserta.keterangan);
                $('#bpjsnew_detail_hak_kelas').html('<strong>:</strong> ' + peserta.hakKelas.keterangan);
                $('#bpjsnew_detail_nokartu').html('<strong>:</strong> ' + peserta.noKartu);
                $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)
                var tmt = peserta.tglTMT;
                var tat = peserta.tglTAT;
                $('#bpjsnew_detail_tmt_tat').html('<strong>:</strong> ' + tmt + ' - ' + tat);
                var kdProv = peserta.provUmum.kdProvider;
                var nmProv = peserta.provUmum.nmProvider;
                $('#bpjsnew_detail_ppk_rujukan').html('<strong>:</strong> ' + kdProv + " - " + nmProv);
                var statusPeserta = peserta.statusPeserta.keterangan;
                $('#bpjsnew_detail_status_peserta').html('<strong>:</strong> ' + statusPeserta);

                //data sep
                $('#no_telp').val(peserta.mr.noTelepon);

                var newOption = new Option(sep.dpjp.nmDPJP, sep.dpjp.kdDPJP, false, false);
                $('#kode_dpjp_melayani').append(newOption);
                $('#kode_dpjp_melayani').val(sep.dpjp.kdDPJP).trigger('change');

                $('#no_rujukan_1').val(sep.noRujukan);
                $('#nomr').val(peserta.mr.noMR);
                $('#tanggal_rujukan').val(sep.tglSep);
                $('#tanggal_sep').val(sep.tglSep);
                $('#jenis_peserta').val(peserta.jenisPeserta.keterangan);
                if (response.rujukan.metaData.code == 200){
                    $('#asal_rujukan').val(rujukan.asalFaskes).trigger('change');
                    $('#ppk_rujukan').val(rujukan.rujukan.provPerujuk.nama);
                }
                $('#catatan_sep').val(sep.catatan);
                $('#kelas_rawat').val(sep.klsRawat.klsRawatHak);
                if (sep.poliEksekutif == true || sep.poliEksekutif == 'true' || sep.poliEksekutif == 1 || sep.poliEksekutif == '1')
                {
                    $('#poli_eksekutif').prop('checked', true).trigger('change');
                }
                if (sep.cob == true || sep.cob == 'true' || sep.cob == 1 || sep.cob == '1')
                {
                    $('#bpjsnewform-cob').prop('checked', true).trigger('change');
                }
                if (sep.katarak == true || sep.katarak == 'true' || sep.katarak == 1 || sep.katarak == '1')
                {
                    $('#manajemenbpjsform-katarak').prop('checked', true).trigger('change');
                }
                if (sep.kdStatusKecelakaan == "1" || sep.kdStatusKecelakaan == "2"){
                    on_load_kecelakaan = true;
                }
                $('#kasus_kecelakaan').val(sep.kdStatusKecelakaan).trigger('change');
                
                if (sep.kdStatusKecelakaan != 0){
                    $('.kasus_kecelakaan_form').show();
                    $('.suplesi_form').hide();
                    $('#status_suplesi').val('0');
                }

                
                $('#keterangan').val(sep.lokasiKejadian.ketKejadian).trigger('change');
                $('#tanggal_kejadian').val(sep.lokasiKejadian.tglKejadian).trigger('change');
                
                getPoliTujuan(sep.poli);
                getDiagnosa(sep.diagnosa);

                console.log(sep.lokasiKejadian.lokasi);

                if (sep.lokasiKejadian.lokasi !== null){
                    getProvinsi(sep.lokasiKejadian.lokasi);
                    $('#cek_lokasi').val('true');
                }
                

                //tampil panel
                $('#info-pasien').show();

                if (rujukan_internal != null) {
                    $('.btn-rujukan_internal').attr('disabled', false);
                } else {
                    $('.btn-rujukan_internal').attr('disabled', true);
                }
            }
        }
    });


});



$('#kasus_kecelakaan').change(function () {
    var base = $(this).val();


    if (base == 0) {
        on_load_kecelakaan = false;
        $('.kasus_kecelakaan_form').hide();
        $('#status_suplesi').val('0');
    } else if ( base == 3 ){
        on_load_kecelakaan = false;
        $('.kasus_kecelakaan_form').show();
        $('.suplesi_form').hide();
        $('#status_suplesi').val('0');
    } 
    else {
        if (on_load_kecelakaan == false){
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
                }
            });
        }
    }
});

$('#form').submit(function(event){
    event.preventDefault();
    var _value = $(this).serializeArray();
    
    _value.push({
        name: 'ManajemenBpjsForm[nama_dpjp_melayani]',
        value: nama_dpjp_melayani
    })

    $(this).docoForm("submit", {
        data: _value,
        success: function (response) {
            $('.rujukan-id').val(response);
        }
    });
})


function getPoliTujuan(poli){
    var kode_poli;
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-poli-new',
        data: {
            search: poli
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            if (data.results) {
                $.each(data.results, function (index, value) {
                    if (value.text == poli){
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

var kode_provinsi;
var kode_kabupaten;
function getProvinsi(lokasi){
    var str_lokasi = lokasi.split("|");
    var provinsi = str_lokasi[2];
    var kabupaten = str_lokasi[1];
    var kecamatan = str_lokasi[0];
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-provinsi',
        data: {
            search: provinsi
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            if (data.data) {
                $.each(data.data, function (index, value) {
                    if (provinsi == value.nama){
                        var newOption = new Option(value.nama, value.kode, false, false);
                        kode_provinsi = value.kode;
                        $('#kode_provinsi').append(newOption);
                        return false;
                    }
                });
                $('#kode_provinsi').val(kode_provinsi).trigger('change');
                getKabupaten(kabupaten, kecamatan);
            }
        },
    });
}

function getKabupaten(kabupaten, kecamatan){
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-kabupaten',
        data: {
            kode_propinsi: kode_provinsi
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            if (data.data) {
                $.each(data.data, function (index, value) {
                    if (kabupaten == value.nama){
                        var newOption = new Option(value.nama, value.kode, false, false);
                        kode_kabupaten = value.kode;
                        $('#kode_kabupaten').append(newOption);
                        return false;
                    }
                });
                $('#kode_kabupaten').val(kode_kabupaten).trigger('change');
                $('#kode_kabupaten').prop('disabled', false)
                getKecamatan(kecamatan);
            }
        },
    });
}

function getKecamatan(kecamatan){
    var kode_kecamatan;
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-kecamatan',
        data: {
            kode_kabupaten: kode_kabupaten
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            if (data.data) {
                $.each(data.data, function (index, value) {
                    if (kecamatan == value.nama){
                        var newOption = new Option(value.nama, value.kode, false, false);
                        kode_kecamatan = value.kode;
                        $('#kode_kecamatan').append(newOption);
                        return false;
                    }
                });
                $('#kode_kecamatan').val(kode_kecamatan).trigger('change');
                $('#kode_kecamatan').prop('disabled', false);
                
            }
            $('#cek_lokasi').val('false');
        },
    });
}

function resetForm(){
    $('#no_telp').val(null);
    $('#kode_dpjp_melayani').val(null).trigger('change');
    $('#no_rujukan_1').val(null);
    $('#nomr').val(null);
    $('#tanggal_rujukan').val(null);
    $('#tanggal_sep').val(null);
    $('#jenis_peserta').val(null);
    $('#asal_rujukan').val(null).trigger('change');
    $('#ppk_rujukan').val(null);
    $('#catatan_sep').val(null);
    $('#poli_eksekutif').prop('checked', false).trigger('change');
    $('#bpjsnewform-cob').prop('checked', false).trigger('change');
    $('#manajemenbpjsform-katarak').prop('checked', false).trigger('change');
    $('#kasus_kecelakaan').val(null).trigger('change');
    $('#kelas_rawat').val(null);
    $('#kode_provinsi').val(null).trigger('change');
    $('#kode_kabupaten').val(null).trigger('change');
    $('#kode_kecamatan').val(null).trigger('change');
}

$(document).on("click", ".btn-hapus", function(e) {
    var no_sep = $('#no_sep').val();
    e.preventDefault();
    $(this).docoForm("delete",{
        url: baseUrl+"pendaftaran/manajemen-bpjs/hapus-sep?no_sep="+no_sep,
        success : function (data) {
            setTimeout(function () {
                location.reload();
            }, 1500);
        }
    });
    return false;
});

