const temp_data_diagnosa ={};
var index_temp_data_diagnosa =0;
const temp_data_procedure ={};
var index_temp_data_procedure =0;
$(document).ready(function () {
    $('.btn-add-form-diagnosa').on('click', function () {
        var next = true;
        const temp ={};
        var temp_type_diagnosa = $('input[name="type_diagnosa"]:checked').val();
        var temp_diagnosa = $('#diagnosa_rujukan').val();
        var fieldHtml ='';

        $('input[name="type_diagnosa"]').each(function(key, obj) {
            if (!$('input[name="type_diagnosa"]:checked').val()) {
                next = false;
                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Type Diagnosa cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');
                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }
        });

        $('#diagnosa_rujukan').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;
                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Diagnosa cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');
                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }
        });

        if (next == true) {
            // temp.type_diagnosa = temp_type_diagnosa;
            // temp.diagnosa = temp_diagnosa;
            temp.kode = temp_type_diagnosa+';'+temp_diagnosa;
            temp_data_diagnosa[index_temp_data_diagnosa] = temp;
            $('#error-diagnosa').hide();
            
            fieldHtml = '<tr>'+
                            '<td style="display:none">'+index_temp_data_diagnosa+'</td>'+
                            '<td>'+temp_type_diagnosa+'</td>'+
                            '<td>'+$('#select2-diagnosa_rujukan-container').text() +'</td>'+
                            '<td><button type="button" class="btn btn-danger btn-sm btn-delete-form-diagnosa" style=""><b><i class="fa fa-trash"></i></b></button></td>'+
                        '</tr>';

            $('#tbody-diagnosa').append(fieldHtml);
            $('#diagnosa_rujukan').val('').trigger('change');
            $('input[name="type_diagnosa"]:checked').prop('checked', false);
            index_temp_data_diagnosa = index_temp_data_diagnosa+1;
        }
    });

    $('.btn-add-form-procedure').on('click', function () {
        var next = true;
        const temp ={};
        var temp_procedure = $('#procedure').val();
        var fieldHtml ='';

        $('#procedure').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;

                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Diagnosa cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }
        });

        if (next == true) {
            temp.kode = temp_procedure;
            temp_data_procedure[index_temp_data_procedure] = temp;
            $('#error-procedure').hide();
            fieldHtml = '<tr>'+
                            '<td style="display:none">'+index_temp_data_procedure+'</td>'+
                            '<td>'+ $('#select2-procedure-container').text() +'</td>'+
                            '<td><button type="button" class="btn btn-danger btn-sm btn-delete-form-procedure" style=""><b><i class="fa fa-trash"></i></b></button></td>'+
                        '</tr>'
            $('#tbody-procedure').append(fieldHtml);
            $('#procedure').val('').trigger('change');
            index_temp_data_procedure = index_temp_data_procedure+1;
        }
    });

    $(".select2Diagnosa").select2({
        placeholder: "Pilih Diagnosa",
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

    $(".select2Procedure").select2({
        placeholder: "Pilih Procedure",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-procedure",
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

    $('#form').submit(function(event){
        event.preventDefault();
        var _data = $(this).serializeArray();
        var next = true;
        $('#error-diagnosa').hide();
        $('#error-procedure').hide();
        if (Object.entries(temp_data_diagnosa).length == 0) {
            next = false
            $('#error-diagnosa').show();
        }

        if (Object.entries(temp_data_procedure).length == 0) {
            next = false
            $('#error-procedure').show();
        }

        _data.push({
            name: 'data_diagnosa',
            value:  JSON.stringify(Object.values(temp_data_diagnosa)),
        });

        _data.push({
            name: 'data_procedure',
            value:  JSON.stringify(Object.values(temp_data_procedure)),
        });

        if (next ==true) {
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
                        location.reload();
                    }
                }
            });
        }
        
    });
});

$(document).on('click', '.btn-delete-form-diagnosa', function(event) {
        var index_delete = $(this).parent().parent().find('td:eq(0)').text();
        delete temp_data_diagnosa[index_delete];
        // console.log(temp_data_diagnosa);
        $(this).parent().parent().remove();
});

$(document).on('click', '.btn-delete-form-procedure', function(event) {
        var index_delete = $(this).parent().parent().find('td:eq(0)').text();
        delete temp_data_procedure[index_delete];
        // console.log(temp_data_procedure);
        $(this).parent().parent().remove();
});


$(".btn-cari").click(function() {
    if ($('#no_rujukan').val() == null || $('#no_rujukan').val() == '') {
        docoNotification('error', "No Rujukan wajib diisi", "Silahkan inputkan no Rujukan");
    } else {
        var no_rujukan = $('#no_rujukan').val();
        $(".populate-data").css("display", "block")
        $(".populate-data").html(`Mempersiapkan data ...`);
        $('#form-informasi_create').hide();
        $('#form-rencana_kontrol').hide();
        // resetForm();
        setTimeout(() => {
            $.ajax({
                url: '/pendaftaran/rujukan-khusus/get-pasien-by-rujukan',
                type: 'GET',
                data: {
                    no_rujukan: $('#no_rujukan').val()
                },
                // dataType: 'JSON',
                error: function(response) {
                    $(".populate-data").css("display", "none")
                    docoNotification('error', response.responseJSON.response.title, response.responseJSON.response.message);
                },
                success: function(response) {
                    var rujukan;
                    if ((response.response.data) == null) {
                        $(".populate-data").css("display", "none")
                        docoNotification('error', 'Peringatan', 'Data Tidak Ditemukan! Informasi Peserta akan dikosongkan.');
                        $('#form-informasi_create').show();
                        $('#form-rujukan-khusus').show();
                    } else { 
                        rujukan = response.response.data.rujukan;
                        $(".populate-data").css("display", "none")
                    
                        $('#form-informasi_create').show();
                        $('#form-rujukan-khusus').show();
    
                        $('.nama-pasien').html(rujukan.peserta.nama);
                        $('.rm-pasien').html(rujukan.peserta.noKartu);
    
                        // Info Pasien
                        $('.info-nama_peserta').html(": " + rujukan.peserta.nama);
                        $('.info-jenis_kelamin').html(": " + rujukan.peserta.sex);
                        $('#bpjsnew_detail_nokartu').html(": " + rujukan.peserta.noKartu);
                        $('#bpjsnew_detail_nik').html(": " + rujukan.peserta.nik);
                        $('#bpjsnew_detail_tgl_lahir').html(": " +  rujukan.peserta.tglLahir);
                        $('#bpjsnew_detail_jenis_peserta').html(": " + rujukan.peserta.jenisPeserta.keterangan);
                        $('#bpjsnew_detail_hak_kelas').html(": " + rujukan.peserta.hakKelas.keterangan);
                        var tmt = rujukan.peserta.tglTMT;
                        var tat = rujukan.peserta.tglTAT;
                        $('#bpjsnew_detail_tmt_tat').html(": " + tmt + ' - ' + tat);
                        var kdProv = rujukan.peserta.provUmum.kdProvider;
                        var nmProv = rujukan.peserta.provUmum.nmProvider;
                        $('#bpjsnew_detail_ppk_rujukan').html(": " + kdProv + " - " + nmProv);
                        var statusPeserta = rujukan.peserta.statusPeserta.keterangan;
                        $('#bpjsnew_detail_status_peserta').html(": " + statusPeserta);
    
                        // Rujukan
                        if(rujukan != null) {
                            $('#no_kunjungan').html(": " + rujukan.noKunjungan);
                            $('#tgl_kunjungan').html(": " + rujukan.tglKunjungan);
                            $('#keluhan').html(": " + rujukan.keluhan);
                            $('#diagnosa').html(": " + rujukan.diagnosa.kode+"-"+rujukan.diagnosa.nama);
                            $('#pelayanan').html(": " + rujukan.pelayanan.kode+"-"+rujukan.pelayanan.nama);
                            $('#poli-rujukan').html(": " + rujukan.poliRujukan.kode+"-"+rujukan.poliRujukan.nama);
                            $('#prov-rujukan').html(": " + (rujukan.provPerujuk.nama == null ? '-' : rujukan.provPerujuk.nama) + ' - ' + rujukan.provPerujuk.kode);
                            $('.info-masa_rujukan').html(": " + rujukan.tglKunjungan);
                            $('.info-no_rujukan').html(": " + rujukan.noKunjungan);
                        }
                    }
                }
            })
        });
    }
});

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
                        $('#diagnosa_rujukan').append(newOption);
                        return false;
                    }
                });
                $('#diagnosa_rujukan').trigger('change');
                $('#diagnosa_rujukan').val(kode_diagnosa).trigger('change');
            }
        },
    });
}

function getProcedure(procedure){
    var kode_procedure;
    $.ajax({
        type: 'GET',
        url: '/api/bpjs/referensi-procedure',
        data: {
            search: procedure
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
                        kode_procedure = value.id;
                        $('#procedure').append(newOption);
                        return false;
                    }
                });
                $('#procedure').trigger('change');
                $('#procedure').val(kode_procedure).trigger('change');
            }
        },
    });
}

$('#reset').on('click', function () {
    location.reload();
    // resetForm();
});

$('.btn-hapus').on('click', function () {
    window.location.href = "/pendaftaran/rujukan-khusus";
});

function resetForm() {
    $('#procedure').val('').trigger('change');
    $('#diagnosa_rujukan').val('').trigger('change');
    $('input[name="type_diagnosa"]:checked').prop('checked', false);
    $('#tbody-procedure').child().remove();
    $('#tbody-diagnosa').child().remove();
}