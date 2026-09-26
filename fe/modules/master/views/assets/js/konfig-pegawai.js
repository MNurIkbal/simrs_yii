$.fn.stepy.defaults.legend = false;
$.fn.stepy.defaults.transition = "fade";
$.fn.stepy.defaults.duration = 150;
$.fn.stepy.defaults.backLabel =
  '<i class="icon-arrow-left13 position-left"></i> Back';
$.fn.stepy.defaults.nextLabel =
  'Next <i class="icon-arrow-right14 position-right"></i>';
$(document).ready(function($) {
    $(".input-group-addon.kv-date-remove").remove();
});

$("#konfig-form").addClass("stepy-basic");
$(".stepy-basic").stepy({
    validate: true,
    block: true,
    next: function(index) {
        var next = true;
        if ($('.opt-tgl').val() == '') {
            var error = '<label class="label label-danger label-roundless">Tanggal berlaku harus di isi</label>';
            $("#error-opt-tgl").html(error);
            next = false;
        }
        if ($(".formula").val() == "") {
            var error = '<label class="label label-danger label-roundless">Formula harus di isi</label>';
            $("#error-formula").html(error);
            next = false;
        
        }
        if ($(".ppn").val() == "") {
            var error = '<label class="label label-danger label-roundless">Persen PPn harus di isi</label>';
            $("#error-ppn").html(error);
            next = false;
            
        }
        if ($(".margin").val() == "") {
            var error = '<label class="label label-danger label-roundless">Persen margin harus di isi</label>';
            $("#error-margin").html(error);
            next = false;
            
        }
        return next;
    },
    finish: function(index) {
        var finish = true;
        if ($(".pembulatan").val() == "") {
            var error = '<label class="label label-danger label-roundless">Pembulatan harga harus di isi</label>';
            $("#error-pembulatan").html(error);
            finish = false;
        }
        if ($(".harga").val() == "") {
            var error = '<label class="label label-danger label-roundless">Harga yang digunakan harus di isi</label>';
            $("#error-harga").html(error);
            finish = false;
        }
        if ($(".metode").val() == "") {
            var error = '<label class="label label-danger label-roundless">Metode antrian stok harus di isi</label>';
            $("#error-metode").html(error);
            finish = false;
        }
        return finish;
    }
});


$(".stepy-basic")
    .find(".button-next")
    .addClass("btn bg-teal-700 btn-huge-next");
$(".stepy-basic")
    .find(".button-back")
    .addClass("btn bg-slate btn-huge-prev pull-left");
    
$("#konfig-form").submit(function(event) {
    event.preventDefault();
    let errors = 0;
    var formData = new FormData(this);
    var filesize_fotopegawai = typeof $("#file")[0].files[0] !== 'undefined' ? $("#file")[0].files[0].size : false;
    var filesize_ttd = typeof $("#file-ttd")[0].files[0] !== 'undefined' ? $("#file-ttd")[0].files[0].size : false;
    let filesformat_fotopegawai = typeof $("#file")[0].files[0] !== 'undefined' ? $("#file")[0].files[0].name.toLowerCase().split(/[\s.]+/) : false;
    let filesformat_ttd = typeof $("#file-ttd")[0].files[0] !== 'undefined' ? $("#file-ttd")[0].files[0].name.toLowerCase().split(/[\s.]+/) : false;
    const valid_format = ['jpg', 'png', 'jpeg', 'gif','svg','jfif'];

    if (filesize_ttd && filesize_ttd > 5242880){
        docoNotification('error', 'Proses Gagal!', 'File Tanda Tangan terlalu besar! Max 5MB!')
        ++errors;
    }

    if (filesformat_ttd && valid_format.includes(filesformat_ttd[filesformat_ttd.length - 1]) == false){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('Format File Foto Pegawai anda salah!'));
        ++errors;
    }

    if (filesize_fotopegawai && filesize_fotopegawai > 5242880){
        docoNotification('error', 'Proses Gagal!', 'File Foto Pegawai terlalu besar! Max 5MB!')
        ++errors;
    }

    if (filesformat_fotopegawai && valid_format.includes(filesformat_fotopegawai[filesformat_fotopegawai.length - 1]) == false){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('Format File tanda tangan anda salah!'));
        ++errors;
    }

    if(errors == 0 ){
        $(this).docoForm("submit", {
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            method: "POST",
            isUpload: true,
            success: function(data) {
                if (data.response.message == "Data Berhasil di simpan") {
                    $('#btn-back').click();
                }
                setTimeout(function() {
                    window.location.href = "/master/pegawai";
                }, 2000);
            },
            error: function() {
                setTimeout(() => {
                    let count_error = $('#konfig-form-step-0').find('.error').length;
                    if (parseInt(count_error) != 0) {
                        $('.button-back').trigger('click');
                    }
                }, 100);
            }
        });
    }else{ 
        return false;
    }
});


var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
$('.pickadate').pickadate({
    format: 'dd mmmm yyyy',
    disable: [{
        from: [0, 0, 0],
        to: yesterday
    }],
    onStart: function () {
        var date = new Date();
        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
    }
});

$(document).on('keydown', null, 'alt+s', function (event) {
    $("#btn-submit").click();
});

$(document).on('keydown', null, 'alt+S', function (event) {
    $("#btn-submit").click();
});

$('#file').bind('change', function() {
    var filesize = typeof $("#file")[0].files[0] !== 'undefined' ? $("#file")[0].files[0].size : false;
    let filesformat = typeof $("#file")[0].files[0] !== 'undefined' ? $("#file")[0].files[0].name.toLowerCase().split(/[\s.]+/) : false;
    const valid_format = ['jpg', 'png', 'jpeg', 'gif'];
    const last = filesformat[filesformat.length - 1];
    let check_format = valid_format.includes(last);
    if (filesize > 5242880){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('File Foto Pegawai terlalu besar! Max 5MB!'));
        return false;
    }

    if (check_format == false){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('Format File foto pegawai anda salah!'));
        return false;
    }
});

$('#file-ttd').bind('change', function() {
    var filesize = typeof $("#file-ttd")[0].files[0] !== 'undefined' ? $("#file-ttd")[0].files[0].size : false;
    let filesformat = typeof $("#file-ttd")[0].files[0] !== 'undefined' ? $("#file-ttd")[0].files[0].name.toLowerCase().split(/[\s.]+/) : false;
    const valid_format = ['jpg', 'png', 'jpeg', 'gif'];
    const last = filesformat[filesformat.length - 1];
    let check_format = valid_format.includes(last);
    if (filesize > 5242880){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('File Tanda Tangan terlalu besar! Max 5MB!'));
        return false;
    }
    if (check_format == false){
        docoNotification('error', i18next.t('Proses Upload Gagal'), i18next.t('Format File tanda tangan anda salah!'));
        return false;
    }
});
