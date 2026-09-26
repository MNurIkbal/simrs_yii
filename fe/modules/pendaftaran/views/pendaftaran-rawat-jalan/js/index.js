$(document).on('click', '#btn-pasien-baru', function () {
    if ($('#form-kunjungan').hasClass('col-md-9')) {
        $('#form-kunjungan').removeClass('col-md-9');
        $('#form-kunjungan').addClass('col-md-12');
    }

    $('#form-infopasien').hide();
    $('#form-kunjungan').show();
    $('#form-pasien').show();
    $('#no_rekam_medik').val(null).trigger('change').trigger('depdrop:change');
});

function getInfoPasien(id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-info-pasien?id=' + id,
        dataType: 'JSON',
        beforeSend: function () {
            // before send
        },
        success: function (res) {
            data = res.response;
            form = res.form
            data_pasien = data;

            if ($('#form-kunjungan').hasClass('col-md-12')) {
                $('#form-kunjungan').removeClass('col-md-12');
                $('#form-kunjungan').addClass('col-md-9');
            }

            $('#form-infopasien').show();
            $('#form-kunjungan').show();
            $('#form-pasien').hide();

            $('.nama-pasien').text(data.nama_pasien ? data.nama_pasien : '-');
            $('.rm-pasien').text(data.no_rekam_medik ? data.no_rekam_medik : '-');
            $('.kelamin-pasien').text(data.jenis_kelamin ? data.jenis_kelamin : '-');
            $('.darah-pasien').text(data.golongan_darah ? data.golongan_darah : '-');
            $('.ibu-pasien').text(data.nama_ibu ? data.nama_ibu : '-');
            $('.tlp-pasien').text(data.no_telepon_pasien ? data.no_telepon_pasien : '-');
            $('.alamat-pasien').text(data.alamat_pasien ? data.alamat_pasien : '-');
            $('#bpjsnew_detail_nokartu').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_nik').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_tgl_lahir').text(data.tanggal_lahir ? data.tanggal_lahir : '-');
            $('#bpjsnew_detail_jenis_peserta').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_hak_kelas').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_tmt_tat').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_ppk_rujukan').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');
            $('#bpjsnew_detail_status_peserta').text(data.nopeserta_bpjs ? data.nopeserta_bpjs : '-');

            if (data.photopasien) {
              img = "/media/img/pasien/"+data.photopasien;
            } else {
              img = "/media/img/icon-app/default.jpg";
            }

            focusField("id", "carabayar_id", true, "select");
        },
        error: function (res) {
            //code
        },
    }).done(function () {
        
    });
}

function onClickPilihPasien(ini) {
    var pasien_id = $(ini).data('id');
    var bpjs = $(ini).data('bpjs');
    var _parentTr = $(ini).closest('tr');
    var data = table.row($(ini).parents('tr')).data();
    var newOption = new Option(data.info_pasien.replace(/<br>/g,' / '), data.no_rekam_medik, false, false);

    $('#no_rekam_medik').append(newOption).trigger('change');
    $('#no_rekam_medik').val(data.no_rekam_medik).trigger('change');
    $('#modal_pencarian_lanjutan').modal('hide');

    getInfoPasien(pasien_id);
}

$(document).on('change', '.antrian-id', function() {
    if ($(this).val() == '') {
        return;
    }

    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-antrian?antrian_id=' + $(this).val(),
        dataType: 'JSON',
        beforeSend: function() {

        },
        success: function(res) {
            // $('.select-no-rm').hide();
            if (res.pasien_id) {
                getInfoPasien(res.pasien_id);
                $('.pasien-id').val(res.pasien_id);

                focusField("id", "kunjunganform-jeniskasuspenyakit_id", true, "select");
            } else {
                $('#no_rekam_medik').val('').trigger('change');
                $('.inf-pasien').hide();
                $('.form-data-pasien').show();
                $('.btn-pasien-simpan-tambah').show();
                $('.btn-pasien-simpan-ubah').hide();
                $('.form-data-pasien').hide();
                $('.inf-pasien-title-nama').html("<strong>" + i18next.t('Tambah data pasien') + "</strong>");
                $('.inf-pasien-title-norm').html('');

                focusField("id", "frm-pasien-jenisidentitas", true, "select", true);
            }

            $('#instalasi_id').val(res.instalasi_id).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(res.pegawai_id);
            });
        }
    });
});

var $input_date = $('#pj_tanggal_lahir').pickadate({
    editable: true,
    format:'dd-mm-yyyy',
    formatSubmit:'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    onClose: function() {
        $('.datepicker').focus();
    }
});
var picker_date = $input_date.pickadate('picker');

$('#btn_addon_tgllahir').on('click',function(event) {
    if (picker_date.get('open')) {
        picker_date.close();
    } else {
        picker_date.open();
    }

    event.stopPropagation();
});

// DEPRECATED FUNCTION
// reason : button "edit" is not use anymore
function flaggingFormPasien(value) {
    $('#frm-pasien-jenisidentitas').prop('disabled', value);
    $('#frm-pasien-no_identitas_pasien').prop('readonly', value);
    $('#frm-pasien-namadepan').prop('disabled', value);
    $('#frm-pasien-nama_pasien').prop('readonly', value);
    $('#frm-pasien-nama_bin').prop('readonly', value);
    $('#frm-pasien-tempat_lahir').prop('readonly', value);
    $('#frm-pasien-tanggal_lahir').prop('disabled', value);
    // $('#frm-pasien-umur').prop('readonly', value);
    // $(':radio[name="PasienForm[jeniskelamin]"]:not(:checked)').prop('disabled', value);
    // $(':radio[name="PasienForm[golongandarah]"]:not(:checked)').prop('disabled', value);
    $('#frm-pasien-statusperkawinan').prop('disabled', value);
    $('#frm-pasien-nama_ibu').prop('readonly', value);
    $('#frm-pasien-nama_ayah').prop('readonly', value);
    $('#frm-pasien-anakke').prop('readonly', value);
    $('#frm-pasien-jumlah_bersaudara').prop('readonly', value);

    $('#frm-pasien-suku_id').prop('disabled', value);
    $('#frm-pasien-warga_negara').prop('disabled', value);
    $('#frm-pasien-agama').prop('disabled', value);
}

$('#frm-pasien-no_identitas_pasien').on('blur', function(){
    if($(this).val().length < 16){
        return false;
    }
    function padZero(num){
        str = num;
        return str < 10 ? '0'+num : num;
    }
    var kodeProv = $(this).val().substring(0, 2);
    var kodeKab = $(this).val().substring(2, 4);
    var kodeKec = $(this).val().substring(4, 6);
    var jk = parseInt($(this).val().substring(6, 8)) - 40 >= 0 ? 16 : 15;
    var day_date_s = (jk == 16 ? $(this).val().substring(6, 8) - 40 : $(this).val().substring(6, 8));
    var day_date = padZero(day_date_s);
    var month_date = $(this).val().substring(8, 10);
    var year_date_s = $(this).val().substring(10, 12);
    var year_date = year_date_s > 24 ? '19'+year_date_s : '20'+year_date_s;
    var tglLahir = day_date+'-'+month_date+'-'+year_date;
    if($('#frm-pasien-jenisidentitas').val() == '94') {
        // set propinsi
        $('#frm-pasien-propinsi_id option').each(function(id, el) {
            if (String($(el).data('kode')) == kodeProv) {
                // $(el).attr('selected', true);
                $('#frm-pasien-propinsi_id').val($(el).attr('value')).trigger('change').trigger('depdrop:change');
            }
        })
        // set jenis kelamin
        $("input[name='PasienForm[jeniskelamin]'][value="+jk+"]").prop('checked', true).trigger('change');
        $('#frm-pasien-tanggal_lahir').val(tglLahir).trigger('change');

    }

});

$('#frm-pasien-nopeserta_bpjs').on('keypress', function(e){
    if(e.which == 13 || e.keyCode == 13){
        var valNum = $(this).val();
        if(valNum.match(/\d{3,20}/g)){
            $.ajax({
                type: 'GET',
                url: '/pendaftaran/daftar/get-pasien-autofill?nopesertabpjs=' + valNum,
                dataType: 'JSON',
                beforeSend: function() {
                },
                success: function(apiResponse) {
                    if(apiResponse){
                        if(apiResponse.nik){
                            $('#frm-pasien-jenisidentitas').val('94').trigger('change');
                            $('#frm-pasien-no_identitas_pasien').val(apiResponse.nik);
                        }
                        if(apiResponse.namapasien){
                            $('#frm-pasien-nama_pasien').val(apiResponse.namapasien);
                        }
                        if(apiResponse.jeniskelamin){
                            $("input[name='PasienForm[jeniskelamin]'][value="+apiResponse.jeniskelamin+"]").prop('checked', true).trigger('change');
                        }
                        if(apiResponse.tanggallahir){
                            $('#frm-pasien-tanggal_lahir').val(apiResponse.tanggallahir).trigger('change');
                        }
                        if(apiResponse.provinsi){
                            $('#frm-pasien-propinsi_id option').each(function(id, el) {
                                if (String($(el).data('kode')) == apiResponse.provinsi) {
                                    $('#frm-pasien-propinsi_id').val($(el).attr('value')).trigger('change').trigger('depdrop:change');
                                }
                            })
                        }
                    }
                }
            });
        }
    }
});

$("input[name='PasienForm[jeniskelamin]']").on('change', function() {
    var jk = $("input[name='PasienForm[jeniskelamin]']:checked").val();
    if (jk == 15) {
        $('#frm-pasien-namadepan').val(201).trigger('change');
    } else if (jk == 16) {
        $('#frm-pasien-namadepan').val(202).trigger('change');
    } else {
        $('#frm-pasien-namadepan').val('').trigger('change');
    }
});

function clearFormKunjungan() {
    $("select#ruangan_id").val("").trigger("change");
    $("select#kunjunganform-jeniskasuspenyakit_id").val("").trigger("change");
    $("select#kunjunganform-kelaspelayanan_id").val("").trigger("change");
    $("select#selectCarabayar").val("").trigger("change");
    $("select#penjamin_id").val("").trigger("change");
    $("select#asalrujukan_id").val("").trigger("change");
    $("select#kunjunganform-keadaan_masuk").val("").trigger("change");
    $("select#kunjunganform-transportasi").val("").trigger("change");
    $("textarea#kunjunganform-keterangan").val("");
}

/**
 * @todo Fungsi untuk memfokuskan cursor ke suatu input
 * @author Sigit Arif Munandar <sigit@docotel.com>
 */
function focusField(attribute = "id", name, focus = true, type = "input", open = false) {
    if (attribute == "id") {
        if (type == "text" || type == "textarea") {
            $("#" + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("#" + name).focus();
        }
    } else {
        if (type == "text" || type == "textarea") {
            $("." + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("." + name).focus();
        }
    }
}