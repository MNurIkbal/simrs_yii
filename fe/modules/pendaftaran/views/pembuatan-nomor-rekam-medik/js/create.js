var arrayJenisIdentitas = [];

$(document).ready(function() {
    $("#jenisidentitas").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Identitas"
    });
    $("#namadepan").prepend("<option selected=></option>").select2({
        placeholder: "Nama Depan"
    });
    $("#jeniskelamin").prepend("<option selected=></option>").select2({
        placeholder: "Jenis kelamin *"
    });
    $("#golongandarah").prepend("<option selected=></option>").select2({
        placeholder: "Golongan Darah"
    });
    $("#statusperkawinan").prepend("<option selected=></option>").select2({
        placeholder: "Status Perkawinan"
    });
    $("#propinsi_id").prepend("<option selected=></option>").select2({
        placeholder: "Propinsi"
    });
    $("#kabupaten_id").prepend("<option selected=></option>").select2({
        placeholder: "Kabupaten"
    });
    $("#kecamatan_id").prepend("<option selected=></option>").select2({
        placeholder: "Kecamatan"
    });
    $("#kelurahan_id").prepend("<option selected=></option>").select2({
        placeholder: "Kelurahan"
    });
    $("#pendidikan_id").prepend("<option selected=></option>").select2({
        placeholder: "Pendidikan"
    });
    $("#pekerjaan_id").prepend("<option selected=></option>").select2({
        placeholder: "Pekerjaan"
    });
    $("#suku_id").prepend("<option selected=></option>").select2({
        placeholder: "Suku"
    });
    $("#warga_negara").prepend("<option selected=></option>").select2({
        placeholder: "Kewarganegaraan"
    });
    $("#agama").prepend("<option selected=></option>").select2({
        placeholder: "Agama"
    });
});

$(document).on('change', '#tanggal_lahir', function () {
    var umur = generateUmur($(this).val());

    $('#umur').val(umur);
});

$(document).on('click', '#btn-muat-ulang', function () {
    window.location.reload();
});

$(document).on('click', '.tambah-jenis', function(event) {
    var next = true;
    var html = $('.identitas:last').clone();
    arrayJenisIdentitas = [];
    html.find('span').remove();
    html.find('select').select2();
    html.find('.no_identitas_pasien').val(null);
    html.find('.tambah-jenis').html('X');
    html.find('.tambah-jenis').removeClass('btn-success');
    html.find('.tambah-jenis').addClass('btn-danger');
    html.find('.tambah-jenis').addClass('hapus-jenis');
    html.find('.tambah-jenis').removeClass('tambah-jenis');
    html.find('.tambah-jenis').prop('id', null);

    $('.no_identitas_pasien').each(function(key, obj) {
        if (!$(this).val()) {
            next = false;

            $(this).addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }
    });

    $('.jenis_identitas').each(function(key, obj) {
        if (!$(this).val()) {
            next = false;

            $(this).parent().addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }

        arrayJenisIdentitas.push($(this).val());
    });

    if (next) {
        // Append in the last
        $('.identitas:last').after(html);
    }
});

$(document).on('click', '.hapus-jenis', function(event) {
    $(this).parent().parent().parent().remove();
});

$(document).on('change', '.jenis_identitas', function(event) {
    if (jQuery.inArray($(this).val(), arrayJenisIdentitas) !== -1) {
        $(this).val(null).trigger('change.select2');

        $(this).parent().addClass('has-error');
        $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas sudah dipilih.');
        $(this).parent().find('.fa').addClass('fa-exclamation-circle');

        docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
    }
});

$(document).on('blur', '.no_identitas_pasien', function(event) {
    if ($(this).val()) {
        $(this).removeClass('has-error');
        $(this).parent().find('.help-block').html('');
    }
});

$('#form-pasien').on('submit', function(e){
    e.preventDefault();
    var formPasien = $('#form-pasien').serializeArray();

    $(this).docoForm('submit',{
        data: formPasien,
        before: function(){
            return false;
        },
        skipSuccessNotif: true,
        success : function(response) {
            let res = response
            let data = Object.values(res)
            var mr = data[1].pasien_id
            if((data[0].code == 200) && (mr !== undefined)) {
                (new PNotify({
                    title: "Pembuatan Nomor Rekam Medik Berhasil",
                    text: "<b>Nomor Rekam Medik :" + mr,
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: true,
                        sticker: true
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [{
                                text: 'Salin',
                                addClass: 'btn btn-xs btn-success setText',
                            },
                            {
                                text: 'Tutup',
                                addClass: 'btn btn-xs btn-warning',
                            }
                        ]
                    },
                    history: {
                        history: true
                    }
                })).get().on('pnotify.confirm', function () {
                  setText(mr)
                }).on('pnotify.cancel', function () {
                    location.reload();
                });
            }else{
                docoNotification('warning', "Proses Gagal", "Pembuatan Nomor Rekam Medik Gagal");
                return false;
            }
        },
        error: function(res) {
            var responseData = res.responseJSON;
            if(typeof responseData.data != 'undefined') {
                if(typeof responseData.data.no_rm != 'undefined') {
                    let noRekamMedik = responseData.data.no_rm;
                    docoNotification('warning', "Proses Gagal", `Pasien sudah terdaftar dengan Nomor Rekam Medik : ${noRekamMedik}`);
                }
            }
            return false;
        }
    }); 
});

function setText(rm) {
    let input     = $("#copy-me");
    let success   = true,
        range     = document.createRange(),
        selection;

    input.val(rm)

    // For IE.
    if (window.clipboardData) {
        window.clipboardData.setData("Text", input.val());        
    } else {
        let tmpElem = $('<div>');
        tmpElem.css({
            position: "absolute",
            left:     "-1000px",
            top:      "-1000px",
        });
        tmpElem.text(input.val());
        $("body").append(tmpElem);
        range.selectNodeContents(tmpElem.get(0));
        selection = window.getSelection ();
        selection.removeAllRanges ();
        selection.addRange (range);
        try { 
            success = document.execCommand ("copy", false, null);
        }
        catch (e) {
           console.log(e)
        }
        if (success) {
            tmpElem.remove();
            setTimeout(() => {
            location.reload();
            }, 1000);
        }
    }
}