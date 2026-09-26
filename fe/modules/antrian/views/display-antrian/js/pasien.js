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
        $('.inf-pasien').hide();
    }
    $('.form-data-pasien').hide();
    $('#form-daftar-rajal')[0].reset();
     clearFormPasien()
});
$('.btn-pasien-inf-batal').on('click', function () {
    $('.select-no-rm').show();
    $('#no_rekam_medik').val('').trigger('change');
    $('.inf-pasien').hide();
    $('.form-data-pasien').hide();
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
        url: '/pendaftaran/daftar/get-antrian?antrian_id=' + $(this).val(),
        dataType: 'JSON',
        beforeSend: function() {

        },
        success: function(res) {
            $('.select-no-rm').hide();
            if (res.pasien_id) {
                getInfoPasien(res.pasien_id);
                $('.pasien-id').val(res.pasien_id);
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
        url: '/pendaftaran/daftar/get-info-pasien?id=' + id,
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            data = res.response;
            form = res.form
            data_pasien = data;
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

            // form bpjs
            $('#norm').val(data.no_rekam_medik);

            // form
            $('#form-daftar-rajal').autofill(form);
            $('#frm-pasien-jenisidentitas').val(data.jenisidentitas).trigger('change')
            $('#frm-pasien-namadepan').val(data.namadepan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-pekerjaan_id').val(data.pekerjaan_id).trigger('change')
            $('#frm-pasien-suku_id').val(data.suku_id).trigger('change')
            $('#frm-pasien-warga_negara').val(data.warga_negara).trigger('change')
            $('#frm-pasien-agama').val(data.agama).trigger('change')
            $('#frm-pasien-tanggal_lahir').pickadate('picker').set('select', data.tanggal_lahir, { format: 'yyyy-mm-dd' });
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
           

            flaggingFormPasien(true);
        },
        error: function (res) {
            //code
        },
    });
}




// -- pasien baru
$(document).on('change', '#frm-pasien-tanggal_lahir', function () {
    var umur = getUmur(convertTanggalYmd($(this).val()), new Date());
    $('#frm-pasien-umur').val(umur);
});

$(".btn-pasien-simpan-ubah").click(function (event) {
    event.preventDefault();
    // $('#form-daftar-rajal').submit()
    // var data = new FormData();
    // var _value = $('#form-daftar-rajal').serializeArray();
    // var file = document.getElementById('file_input').files[0];
    // var id = $('.pasien-id').val();
    // var param = $('.params-header').val();
    // data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    
    // $.each(_value, function(key, value) {
    //     data.append(value.name, value.value);
    // });

    // $(this).docoForm("click", {
    //     url : "/pendaftaran/daftar/ubah-pasien?id=" + id + "&param=" + param,
    //     method : "POST",
    //     type : "json",
    //     data : data,
    //     success : function (data) {
    //         $('.form-data-pasien').hide()
    //         if (data.response.pasien_id) {
    //             getInfoPasien(data.response.pasien_id);
    //         }
    //     }
    // });
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
    // console.log(dataPost);
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
    data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });
    var _url = data['pasien_id'] ? '/pendaftaran/daftar/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar/tambah-pasien';
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
    
    var _url = data['pasien_id'] ? '/pendaftaran/daftar/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar/tambah-pasien';
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
    data.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    // console.log(data);
    // console.log(formData);
    // formData.append("PasienForm[photopasien]", $("#file_input")[0].files[0]);
    
    var id = $('.pasien-id').val();
    var param = $('.params-header').val();
    var _url = $('.pasien-id').val() ? '/pendaftaran/daftar/ubah-pasien?id=' + id + '&param=' + param : '/pendaftaran/daftar/tambah-pasien';
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
        flaggingFormPasien(true);
        clearFormPasien();
        $('.btn-pasien-simpan-tambah').hide();
        $('.btn-pasien-simpan-ubah').show();
    } else {
        // pasien baru
        $('#no_rekam_medik').attr('disabled', true);
        $('#no_rekam_medik').val('').trigger('change');
        $('.inf-pasien').hide();
        $('.form-data-pasien').show();
        flaggingFormPasien(false);
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
    }
});

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
    $('#frm-pasien-warga_negara').val('');
    $('#frm-pasien-agama').val('');
    $('.select2').val('').trigger('change');

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
    $('#selectCarabayar').find('option:contains("BPJS")').prop('disabled', _disabled)
    $('#selectCarabayar').select2().val('').trigger('change');

}

