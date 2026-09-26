$(document).ready(function() {
    // $("#no_rekam_medik").select2("open");
    // $("#no_rekam_medik").focus();
});


var data_pasien = '';
$('.btn-pasien-ubah').on('click', function () {
    $('.select-no-rm').hide();
    $('.inf-pasien').hide();
    $('.form-data-pasien').show();
    $('.btn-pasien-simpan-ubah').show();
    $('.btn-pasien-simpan-tambah').hide();

    $('#frm-pasien-propinsi_id').val(data_pasien.propinsi_id).trigger('change').trigger('depdrop:change');
    $('#frm-pasien-kabupaten_id').on('depdrop:afterChange', function (event, id, value) {
        // console.log(event)
        $(this).val(data_pasien.kabupaten_id).trigger('change').trigger('depdrop:change');
    });
    $('#frm-pasien-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
        $(this).val(data_pasien.kecamatan_id).trigger('change').trigger('depdrop:change');
    });
    setTimeout(() => {
        $('#frm-pasien-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
            $(this).val(data_pasien.kelurahan_id).trigger('change');
        });
    }, 500);

});
$('.btn-pasien-batal').on('click', function () {
    $('.select-no-rm').hide();
    if ($('.pasien-id').val()) {
        $('.inf-pasien').show();
        $('.select-no-rm').hide();
    } else {
        $('.select-no-rm').show();
        $('#no_rekam_medik').attr('disabled', false);
        $('.inf-pasien').hide();
    }
    $('.form-data-pasien').hide();
    $('#form-daftar-ranap')[0].reset();
     clearFormPasien()
});
$('.btn-pasien-inf-batal').on('click', function () {
    $('.select-no-rm').show();
    $('#no_rekam_medik').val('').trigger('change');
    $('.inf-pasien').hide();
    $('.form-data-pasien').hide();
    $("#nomor_cari").val("");
    $('input[name=source_peserta][value=2]').prop('checked', false);

    if ($('#ruangan_id').prop('disabled')) {
        focusField("id", "kunjunganform-jeniskasuspenyakit_id", true, "select");
    } else {
        focusField("id", "ruangan_id", true, "select");
    }
    $('.pasien-id').val('');
    $('#hidden-no_rekam_medik').val('');
    $('#form-daftar-ranap')[0].reset();
    clearFormKunjungan();

    if (tabelKunjungan instanceof $.fn.dataTable.Api) {
        tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar-igd/get-data-kunjungan-pasien?pasien_id=0").draw();
    }
});
$('input[name="PasienForm[is_aps]"]').change(function(){
    if( $(this).val() == 1 ){
        aps = true
        $('.selectRujukan').val('1').trigger('change')
        if(!$('.field-asalrujukan_id').hasClass('hidden')){
            $('.field-asalrujukan_id').addClass('hidden')
        }
    } else{
        aps = false
        $('.selectRujukan').val('').trigger('change')
        if($('.field-asalrujukan_id').hasClass('hidden')){
            $('.field-asalrujukan_id').removeClass('hidden')
        }
    }
    disableBpjs(aps)
})
var img = '';
$(document).on('change', '.antrian-id', function() {
    if ($(this).val() == '') {
        return;
    }

    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar-igd/get-antrian?antrian_id=' + $(this).val(),
        dataType: 'JSON',
        beforeSend: function() {

        },
        success: function(res) {
            $('.select-no-rm').hide();
            if (res.pasien_id) {
                getInfoPasien(res.pasien_id);
                $('.pasien-id').val(res.pasien_id);

                focusField("id", "kunjunganform-jeniskasuspenyakit_id", true, "select");
            } else {
                // pasien baru
                $('#no_rekam_medik').attr('disabled', true);
                $('#no_rekam_medik').val('').trigger('change');
                $('.inf-pasien').hide();
                $('.form-data-pasien').show();
                $('.btn-pasien-simpan-tambah').show();
                $('.btn-pasien-simpan-ubah').hide();

                $('.inf-pasien-title-nama').html("<strong>" + i18next.t('Tambah data pasien') + "</strong>");
                $('.inf-pasien-title-norm').html('');

                focusField("id", "frm-pasien-jenisidentitas", true, "select", true);
            }

            $('#ruangan_id').val(res.ruangan_id).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(res.pegawai_id);
            });


        }
    });
});

//uploads file
$('#file_input').on('click',function(){
    $('#pasang_image2').hide();
    $('.thumbnail').removeClass('thumbnail');
    $('.file-preview').removeClass('file-preview');
});

$(document).on('click', '.fileinput-remove', function(){
    $('#pasang_image2').show();
});

// ---pasien lama
function getInfoPasien(id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar-igd/get-info-pasien?id=' + id,
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            data = res.response;
            form = res.form
            data_pasien = data;
            jk = data.jeniskelamin
            $('.select-no-rm').hide();
            $('.inf-pasien').show();
            $('.pasien-id').val(id)
            $('.form-data-pasien').hide();
            $('.inf-pasien-title-nama').html("<strong>" + data.nama_depan + data.nama_pasien + "</strong>");
            $('.inf-pasien-title-norm').html(i18next.t('No rekam medis') + ' : ' + data.no_rekam_medik);
            $('#nomr').val(data.no_rekam_medik); // for bpjs form
            $('#inf-pasien-jenisidentitas').html(data.identitas);
            $('#inf-pasien-noidentitas').html(data.no_identitas_pasien);
            $('#inf-pasien-namadepan').html(data.nama_depan);
            $('#inf-pasien-namapasien').html(data.nama_pasien);
            $('#inf-pasien-namapanggilan').html(data.nama_bin);
            $('#inf-pasien-tempatlahir').html(data.tempat_lahir);
            $('#inf-pasien-tanggallahir').html(convertTanggalView(data.tanggal_lahir));
            $('#inf-pasien-umur').html(getUmur(data.tanggal_lahir, new Date()));
            $('#inf-pasien-jeniskelamin').html(data.jenis_kelamin);
            $('#inf-pasien-golongandarah').html(data.golongan_darah);
            $('#inf-pasien-statusperkawinan').html(data.status_perkawinan);
            $('#inf-pasien-namaibu').html(data.nama_ibu);
            $('#inf-pasien-namaayah').html(data.nama_ayah);
            $('#inf-pasien-anakke').html(data.anakke);
            $('#inf-pasien-jumlahbersaudara').html(data.jumlah_bersaudara);
            $('#inf-pasien-alamatpasien').html(data.alamat_pasien);
            $('#inf-pasien-rtrw').html((data.rt ? data.rt : '-') + ' / ' + (data.rw ? data.rw : '-'));
            $('#inf-pasien-kelurahan').html(data.kelurahan_nama);
            $('#inf-pasien-kecamatan').html(data.kecamatan_nama);
            $('#inf-pasien-kota').html(data.kabupaten_nama);
            $('#inf-pasien-propinsi').html(data.propinsi_nama);
            $('#inf-pasien-notelepon').html(data.no_telepon_pasien);
            $('#inf-pasien-nomobile').html(data.no_mobile_pasien);
            $('#inf-pasien-alamatemail').html(data.alamatemail);
            $('#inf-pasien-pendidikan').html(data.pendidikan_nama);
            $('#inf-pasien-pekerjaan').html(data.pekerjaan_nama);
            $('#inf-pasien-suku').html(data.suku_nama);
            $('#inf-pasien-warganegara').html(data.warganegara);
            $('#inf-pasien-agama').html(data.agama_pasien);
            if (data.photopasien) {
              img = "/media/img/pasien/"+data.photopasien;
            }else {
              img = "/media/img/icon-app/default.jpg";
            }

            $('#pasang_image').empty().append('<img id="profilePict" src="'+img+'" alt="">');
            $('#pasang_image2').empty().append('<img id="profilePict" src="'+img+'" alt="">');

            // hide as default{
            $('.collapse-pasien').click();

            // form bpjs
            $('#norm').val(data.no_rekam_medik);

            // form
            $('#form-daftar-ranap').autofill(form);
            $('#frm-pasien-jenisidentitas').val(data.jenisidentitas).trigger('change')
            $('#frm-pasien-namadepan').val(data.namadepan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-pendidikan_id').val(data.pendidikan_id).trigger('change')
            $('#frm-pasien-pekerjaan_id').val(data.pekerjaan_id).trigger('change')
            $('#frm-pasien-suku_id').val(data.suku_id).trigger('change')
            $('#frm-pasien-warga_negara').val(data.warga_negara).trigger('change')
            $('#frm-pasien-agama').val(data.agama).trigger('change')
            $('#frm-pasien-umur').val(getUmur(data.tanggal_lahir, new Date()));

            /*$('#frm-pasien-propinsi_id').val(data.propinsi_id).trigger('change').trigger('depdrop:change');
            $('#frm-pasien-kabupaten_id').on('depdrop:afterChange', function (event, id, value) {
                // console.log(event)
                $(this).val(data.kabupaten_id).trigger('change').trigger('depdrop:change');
            });
            $('#frm-pasien-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kecamatan_id).trigger('change').trigger('depdrop:change');
            });
            $('#frm-pasien-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kelurahan_id).trigger('change');
            });*/


            // flaggingFormPasien(true);

            focusField("id", "ruangan_id", true, "select");
        },
        error: function (res) {
            //code
        },
    }).done(function () {
        if (tabelKunjungan instanceof $.fn.dataTable.Api) {
            tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar-igd/get-data-kunjungan-pasien?pasien_id="+data.pasien_id).draw();
        } else {
            tabelKunjungan = $("#tbl-kunjungan").docoTabel({
                filter: false,
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollCollapse: true,
                order: [[0, "desc"]],
                displayLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                ajax: baseUrl + "pendaftaran/daftar-igd/get-data-kunjungan-pasien?pasien_id="+data.pasien_id,
                columns: [
                    {title: "Tanggal Pendaftaran", data: "tgl_pendaftaran"},
                    {title: "No. Pendaftaran", data: "no_pendaftaran"},
                    {title: "Instalasi", data: "instalasi_nama"},
                    {title: "Ruangan", data: "ruangan_nama"},
                    {title: "Dokter", data: "nama_pegawai"},
                ],
            });
        }
    });
}


var $input_date = $('#frm-pasien-tanggal_lahir').pickadate({
    editable: true,
    format:'dd-mm-yyyy',
    formatSubmit:'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    min: [1900, 01, 01],
    max: true,
    onClose: function() {
        $('.datepicker').focus();
    }
});
var picker_date = $input_date.pickadate('picker');
$('#btn_addon_tgllahir').on('click',function(event){
    if (picker_date.get('open')) {
        picker_date.close();
    } else {
        picker_date.open();
    }
    event.stopPropagation();
});

// -- pasien baru
$(document).on('change', '#frm-pasien-tanggal_lahir', function () {
    var umur = '';
    if($(this).val() != ''){
        umur = generateUmur($(this).val());
    }

    $('#frm-pasien-umur').val(umur);
    $("input[name='PasienForm[tanggal_lahir]_submit']").val($('#frm-pasien-tanggal_lahir').val());
});

$(".btn-pasien-simpan-ubah").click(function (event) {
    event.preventDefault();
    var dataPost = {
        pasien_id: $('.pasien-id').val(),
        jenisidentitas: $('#frm-pasien-jenisidentitas').val(),
        no_identitas_pasien: $('#frm-pasien-no_identitas_pasien').val(),
        namadepan: $('#frm-pasien-namadepan').val(),
        nama_pasien: $('#frm-pasien-nama_pasien').val(),
        nama_bin: $('#frm-pasien-nama_bin').val(),
        tempat_lahir: $('#frm-pasien-tempat_lahir').val(),
        tanggal_lahir: $('#frm-pasien-tanggal_lahir').val(),
        umur: $('#frm-pasien-umur').val(),
        jeniskelamin: $('input[name="jeniskelamin"]:checked').val(),
        golongandarah: $('input[name="golongandarah"]:checked').val(),
        statusperkawinan: $('#frm-pasien-statusperkawinan').val(),
        nama_ibu: $('#frm-pasien-nama_ibu').val(),
        nama_ayah: $('#frm-pasien-nama_ayah').val(),
        anakke: $('#frm-pasien-anakke').val(),
        jumlah_bersaudara: $('#frm-pasien-jumlah_bersaudara').val(),
        alamat_pasien: $('#frm-pasien-alamat_pasien').val(),
        rt: $('#frm-pasien-rt').val(),
        rw: $('#frm-pasien-rw').val(),
        propinsi_id: $('#frm-pasien-propinsi_id').val(),
        kabupaten_id: $('#frm-pasien-kabupaten_id').val(),
        kecamatan_id: $('#frm-pasien-kecamatan_id').val(),
        kelurahan_id: $('#frm-pasien-kelurahan_id').val(),
        no_telepon_pasien: $('#frm-pasien-no_telepon_pasien').val(),
        no_mobile_pasien: $('#frm-pasien-no_mobile_pasien').val(),
        alamatemail: $('#frm-pasien-alamatemail').val(),
        pekerjaan_id: $('#frm-pasien-pekerjaan_id').val(),
        suku_id: $('#frm-pasien-suku_id').val(),
        warga_negara: $('#frm-pasien-warga_negara').val(),
        agama: $('#frm-pasien-agama').val(),
        statusrekammedis: $('#frm-pasien-statusrekammedis').val(),
        is_aps: aps,
    };
    ajaxSubmit();


});

$(".btn-pasien-simpan-tambah").click(function (event) {
    event.preventDefault();
    var dataPost = {
        pasien_id: $('.pasien-id').val(),
        jenisidentitas: $('#frm-pasien-jenisidentitas').val(),
        no_identitas_pasien: $('#frm-pasien-no_identitas_pasien').val(),
        namadepan: $('#frm-pasien-namadepan').val(),
        nama_pasien: $('#frm-pasien-nama_pasien').val(),
        nama_bin: $('#frm-pasien-nama_bin').val(),
        tempat_lahir: $('#frm-pasien-tempat_lahir').val(),
        tanggal_lahir: $('#frm-pasien-tanggal_lahir').val(),
        umur: $('#frm-pasien-umur').val(),
        jeniskelamin: $('input[name="jeniskelamin"]:checked').val(),
        golongandarah: $('input[name="golongandarah"]:checked').val(),
        statusperkawinan: $('#frm-pasien-statusperkawinan').val(),
        nama_ibu: $('#frm-pasien-nama_ibu').val(),
        nama_ayah: $('#frm-pasien-nama_ayah').val(),
        anakke: $('#frm-pasien-anakke').val(),
        jumlah_bersaudara: $('#frm-pasien-jumlah_bersaudara').val(),
        alamat_pasien: $('#frm-pasien-alamat_pasien').val(),
        rt: $('#frm-pasien-rt').val(),
        rw: $('#frm-pasien-rw').val(),
        propinsi_id: $('#frm-pasien-propinsi_id').val(),
        kabupaten_id: $('#frm-pasien-kabupaten_id').val(),
        kecamatan_id: $('#frm-pasien-kecamatan_id').val(),
        kelurahan_id: $('#frm-pasien-kelurahan_id').val(),
        no_telepon_pasien: $('#frm-pasien-no_telepon_pasien').val(),
        no_mobile_pasien: $('#frm-pasien-no_mobile_pasien').val(),
        alamatemail: $('#frm-pasien-alamatemail').val(),
        pekerjaan_id: $('#frm-pasien-pekerjaan_id').val(),
        suku_id: $('#frm-pasien-suku_id').val(),
        warga_negara: $('#frm-pasien-warga_negara').val(),
        agama: $('#frm-pasien-agama').val(),
        statusrekammedis: $('#frm-pasien-statusrekammedis').val(),
        is_aps: aps,
    };
    // $('#form-daftar-rajal').submit();
    // event.preventDefault();
    var data = new FormData();
    var dataPost = $("#form-daftar-rajal").serializeArray();
    // data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });
    var _url = data['pasien_id'] ? '/pendaftaran/daftar-igd/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar-igd/tambah-pasien';
    $(this).docoForm("click", {
        url: _url, // point to server-side PHP script
        dataType: false, // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: 'post',
        isUpload: true,
        success: function (data) {
            // var succTitle = 'Proses Berhasil';
            // var succMsg = 'Data Berhasil Disimpan!';
            // docoNotification('success', succTitle, succMsg);
            if (data.response.pasien_id) {
                getInfoPasien(data.response.pasien_id);
            }
        }
        // error: function (data) {
        //     var errTitle = 'Proses Gagal';
        //     var errMsg = 'Data gagal disimpan';
        //     docoNotification('error', errTitle, errMsg);
        // },
        // complete: function () {
        //     hideQuestionDialog();
        //     $('body').find('.confirm-dialog-overlay').remove();
        // }
    });

});
/*$('#form-daftar-rajal').on('beforeSubmit', function(e){
    var form = $(this);
    console.log('test');
    var formData = form.serialize();
    var data = new FormData();
    var dataPost = $(this).serializeArray();
    data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    // console.log(data);
    // console.log(formData);
    // formData.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);

    var _url = data['pasien_id'] ? '/pendaftaran/daftar-igd/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar-igd/tambah-pasien';
    $.ajax({
        url: _url,
        method: "POST",
        type: "json",
        async:false,
        cache:false,
        contentType:false,
        processData:false,
        data : data,
        beforeSend: function(){
            var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
            var dialogTemplate = '<div id="confirm-dialog">';
                    dialogTemplate += '<div class="dialog-content">';
                        dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                    dialogTemplate += '</div>';
                dialogTemplate += '</div>';

                $('body').append(overlayTemplate);
                $('body').append(dialogTemplate);
                $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbsp;Sedang memproses . . .');
            $('.form-group').removeClass('has-error');
            $('span.help-block.error').remove();
            $('div.help-block.error').remove();
        },
        success: function(data){
            // var succTitle = 'Proses Berhasil';
            // var succMsg = 'Data Berhasil Disimpan!';
            // docoNotification('success', succTitle, succMsg);
            if (data.response.pasien_id) {
                getInfoPasien(data.response.pasien_id);
            }
        }
        // error: function(data){
        //     var errTitle = 'Proses Gagal';
        //     var errMsg = 'Data gagal disimpan';
        //     docoNotification('error', errTitle, errMsg);
        // },
        // complete: function(){
        //     hideQuestionDialog();
        //     $('body').find('.confirm-dialog-overlay').remove();
        // }
    })
}).on('submit', function(e){
    setTimeout(function () {
        hideQuestionDialog();
        $('body').find('.confirm-dialog-overlay').remove();
    }, 500);
    e.preventDefault();
});*/

// $(".btn-pasien-simpan-tambah").on('click', function (event) {

// })


function ajaxSubmit() {
    var data = new FormData();
    var dataPost = $('#form-daftar-rajal').serializeArray();
    // data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });

    var id = $('.pasien-id').val();
    var param = $('.params-header').val();
    var _url = $('.pasien-id').val() ? '/pendaftaran/daftar-igd/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar-igd/tambah-pasien';
    // console.log(_url); return;
    $.ajax({
        url: _url,
        method: "POST",
        type: "json",
        async:false,
        cache:false,
        contentType:false,
        processData:false,
        data : data,
        beforeSend: function(){
            var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
            var dialogTemplate = '<div id="confirm-dialog">';
                    dialogTemplate += '<div class="dialog-content">';
                        dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                    dialogTemplate += '</div>';
                dialogTemplate += '</div>';

                $('body').append(overlayTemplate);
                $('body').append(dialogTemplate);
                $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbsp;Sedang memproses . . .');
            $('.form-group').removeClass('has-error');
            $('span.help-block.error').remove();
            $('div.help-block.error').remove();
        },
        success: function(data){
            var succTitle = 'Proses Berhasil';
            var succMsg = 'Data Berhasil Disimpan!';
            setTimeout(function(){
                docoNotification('success', succTitle, succMsg);
                if (data.response.pasien_id) {
                    getInfoPasien(data.response.pasien_id);
                }
            },500);


        },
        error: function(data){
            var errTitle = 'Proses Gagal';
            var errMsg = 'Data gagal disimpan';
            docoNotification('error', errTitle, errMsg);
        },
        complete: function(){
            setTimeout(function(){
                hideQuestionDialog();
                $('body').find('.confirm-dialog-overlay').remove();
            },1000);
        }
    })
}

$("input[name=chk-statuspasien]").change(function () {
    if (this.checked) {
        // pasien lama
        $('#no_rekam_medik').attr('disabled', false);
        $('.inf-pasien').hide();
        $('.form-data-pasien').hide();
        $('#no_rekam_medik').val('').trigger('change');
        // flaggingFormPasien(true);
        clearFormPasien();
        $('.btn-pasien-simpan-tambah').hide();
        $('.btn-pasien-simpan-ubah').show();

        $('.select-no-rm').show();

        focusField("id", "no_rekam_medik", true, "select", true);
} else {
        // pasien baru
        $('#no_rekam_medik').attr('disabled', true);
        $('#no_rekam_medik').val('').trigger('change');
        $('.inf-pasien').hide();
        $('.form-data-pasien').show();
        // flaggingFormPasien(false);
        clearFormPasien();
        // $('#frm-pasien-propinsi_id').trigger('change');
        $('.btn-pasien-simpan-tambah').show();
        $('.btn-pasien-simpan-ubah').hide();

        $('.inf-pasien-title-nama').html("<strong>" + i18next.t('Tambah data pasien') + "</strong>");
        $('.inf-pasien-title-norm').html('');

        $('#form-daftar-rajal').autofill('');
        $('div.form-group.has-error').each(function(){
            $(this).removeClass('has-error');
        });
        $('div.form-group.has-success').each(function(){
            $(this).removeClass('has-success');
        });

        $('.select-no-rm').hide();
        focusField("id", "frm-pasien-jenisidentitas", true, "select", true);
    }
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

function clearFormPasien() {
    $('#frm-pasien-jenisidentitas').val('');
    $('#frm-pasien-no_identitas_pasien').val('');
    $('#frm-pasien-namadepan').val('');
    $('#frm-pasien-nama_pasien').val('');
    $('#frm-pasien-nama_bin').val('');
    $('#frm-pasien-tempat_lahir').val('');
    $('#frm-pasien-tanggal_lahir').val('');
    $('#frm-pasien-umur').val('');
    // $('input[name="jeniskelamin"]').prop('checked', false);
    // $('input[name="golongandarah"]').prop('checked', false);
    $('#frm-pasien-statusperkawinan').val('');
    $('#frm-pasien-nama_ibu').val('');
    $('#frm-pasien-nama_ayah').val('');
    $('#frm-pasien-anakke').val('');
    $('#frm-pasien-jumlah_bersaudara').val('');

    $('#frm-pasien-suku_id').val('');
    $('#frm-pasien-agama').val('');
    $('.select2pasien').val('').trigger('change');
    $('#frm-pasien-warga_negara').val('308').trigger('change');

    alamat_pasien: $('#frm-pasien-alamat_pasien').val('');
    rt: $('#frm-pasien-rt').val('');
    rw: $('#frm-pasien-rw').val('');
    propinsi_id: $('#frm-pasien-propinsi_id').val('');//.trigger('change');
    no_telepon_pasien: $('#frm-pasien-no_telepon_pasien').val('');
    no_mobile_pasien: $('#frm-pasien-no_mobile_pasien').val('');
    alamatemail: $('#frm-pasien-alamatemail').val('');
    pekerjaan_id: $('#frm-pasien-pekerjaan_id').val('');
}

$(document).on('click', '.btn-pasien-reset', function () {
    clearFormPasien();
    propinsi_id: $('#frm-pasien-propinsi_id').trigger('change');
});


var disableBpjs = function(_disabled){
    if (isPenunjang) {
        $("#selectCarabayar option").each(function()
        {
            if(_disabled) { // aps
                if ($(this).data('id') != 417) {
                    $(this).attr('disabled', true);
                } else {
                    $(this).attr('disabled', false);
                }
            } else {
                if ($(this).data('id') == 418) {
                    $(this).attr('disabled', true);
                } else {
                    $(this).attr('disabled', false);
                }

            }
        });
        if (_disabled) {
            $('#selectCarabayar').find('option:contains("BPJS")').prop('disabled', _disabled);

        }
        $('#selectCarabayar').select2().val('').trigger('change');
    }

    // $('#selectCarabayar').find('option:contains("BPJS")').prop('disabled', _disabled)
    // $('#selectCarabayar').select2().val('').trigger('change');
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
                url: '/pendaftaran/daftar-igd/get-pasien-autofill?nopesertabpjs=' + valNum,
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
    if ($('#frm-pasien-namadepan').val().length < 1) {
        var jk = $("input[name='PasienForm[jeniskelamin]']:checked").val();
        if (jk == 15) {
            $('#frm-pasien-namadepan').val(201).trigger('change');
        } else if (jk == 16) {
            $('#frm-pasien-namadepan').val(202).trigger('change');
        } else {
            $('#frm-pasien-namadepan').val('').trigger('change');
        }
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
