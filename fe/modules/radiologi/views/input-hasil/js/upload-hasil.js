
let list_upload = $('.upload-section').get();
let increment = list_upload.length;
let list = [1];

if (parseInt(increment) == 0) {
    $('.info-upload').hide();
}

$(document).ready(function () {
    _checkButton();
});

var _checkButton = function() {
    var _length = $('.inputfile').length;
    var _no = 0;
    $.each($('.inputfile'), function(){
        var _value = $(this).val();
        if (_value) {
            _no++;
        }
    });

    if (_length == _no) {
        $("#add-upload").prop("disabled",false);
    } else {
        $("#add-upload").prop("disabled",true);
    }
}

$(document).on('click', '.delete', function() {
    let button = this;
    let pkId = $(this).attr('data-id');
    let parent = $(this).attr('data-parent');
    if (pkId) {
        $(this).docoForm('delete', {
            url: '/radiologi/input-hasil/delete-upload?id='+pkId + '&parent='+ parent,
            success: function (params) {
                $(button).parent().parent().remove();
            }
        });
    } else {
        $(button).parent().parent().remove();
        _checkButton();
    }
})

function uploadForm(increment) {
    let uploadDiv = '';
    uploadDiv += '<div class="col-md-8 col-xs-offset-1 upload-section" style="margin-left: 178px" id="upload-section">';
        uploadDiv += '<div class="lurus">';
            uploadDiv += '<button type="button" class="btn btn-sm btn-block btn-danger delete"><i class="fa fa-trash"></i></button>';
        uploadDiv += '</div>';
        uploadDiv += '<div class="lurus">';
            uploadDiv += '<input type="file" name="UploadHasilForm[upload_file][]" id="file-' + increment +'" class="form-control inputfile inputfile-1" multiple="true">';
            uploadDiv += '<label for="file-'+increment+'" class="label-upload">';
                uploadDiv += '<i class="fa fa-upload"></i> ';
                uploadDiv += '<span id="label-file"> Pilih Berkas</span>';
            uploadDiv += '</label>';
        uploadDiv += '</div>';
        uploadDiv += '<div class="lurus">';
            uploadDiv += '<div class="col-md-12">';
                uploadDiv += '<input class="form-control" name="UploadHasilForm[catatan][]" type="text" style="width:400px;" placeholder="Masukan catatan">';
            uploadDiv += '</div>';
        uploadDiv += '</div>';
    uploadDiv += '</div>';

    $("#list-upload").append(uploadDiv);
}

$('#add-upload').on('click', function() {
    increment++;
    uploadForm(increment)
    list.push(increment)
    // let last_array = list[list.length - 1];
    // if (list.length > 1) {
    //     let check_file = $('#file-'+last_array)[0].files[0];
    //     if (typeof check_file == 'undefined') {
    //         docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harus pilih berkas terlebih dahulu !"));
    //     } else {
    //         increment++;
    //         uploadForm(increment)
    //         list.push(increment)
    //     }
    // } else {
    //     let check_file = $('#file-1')[0].files[0];
    //     if (typeof check_file == 'undefined') {
    //         docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harus pilih berkas terlebih dahulu !"));
    //     } else {
    //         increment++;
    //         uploadForm(increment)
    //         list.push(increment)
    //     }
    // }
    $('.info-upload').show();
    let list_upload = $('.upload-section').get();
    for (var i = 1; i < list.length+1; i++) {
        $('#file-'+i).hide();
        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if(this.files[0].size > MAX_UPLOAD) {
                    let message = i18next.t("File harus maksimal 250 mb");
                    docoNotification('error', "Upload Gagal", message);
                    // $('.error-upload').text(message);
                    // $('.error-upload').css('color', 'red');
                    return false;
                }
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
                    fileName = e.target.value.split('\\').pop();
                }

                if (fileName)
                    $('#add-upload').prop("disabled",false);
                    label.querySelector('span').innerHTML = fileName;
                // else
                //     label.innerHTML = labelVal;
            });

            input.addEventListener('focus', function () {
                input.classList.add('has-focus');
            });
            input.addEventListener('blur', function () {
                input.classList.remove('has-focus');
            });
        });
    }
    $(this).prop("disabled",true);
});

(function (document, window, index) {
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;
            console.log(this.files);
            input.addEventListener('change', function (e) {
            var fileName = '';

            if(this.files[0].size > MAX_UPLOAD) {
                let message = i18next.t("File harus maksimal 250 mb");
                docoNotification('error', "Upload Gagal", message);
                return false;
            }
            
            if (this.files && this.files.length > 1) {
                fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
            } else {
                fileName = e.target.value.split('\\').pop();
            }

            if (fileName)
                $('#add-upload').prop("disabled",false);
                label.querySelector('span').innerHTML = fileName;
        });

        input.addEventListener('focus', function () {
            input.classList.add('has-focus');
        });
        input.addEventListener('blur', function () {
            input.classList.remove('has-focus');
        });
    });
}(document, window, 0));

$("#btn-save").on('click', function (event) {
    event.preventDefault();
    let pegawai = $('.pegawai').val();
    if (pegawai == '') {
        docoNotification("warning", i18next.t("Perhatian"), i18next.t("Petugas radiologi tidak boleh kosong"));
        return false;
    }
    let data = new FormData();
    let hasilpemeriksaanrad_id = $('.hasilpemeriksaanrad_id').val();
    let penunjang_id = $('.penunjang_id').val();
    let dataPost = $("#form-hasil").serializeArray();
    for (let i = 1; i < list.length+1; i++) {
        let getFile = $("#file-" + i)[0];
        if (typeof getFile !== 'undefined') {
            data.append("UploadHasilForm[upload_file][]", $("#file-" + i)[0].files[0]);
            
        }
    }
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    data.append("UploadHasilForm[hasilpemeriksaanrad_id]", hasilpemeriksaanrad_id);
    data.append("UploadHasilForm[penunjang_id]", penunjang_id);
    
    console.log(data)
    $(this).docoForm("click", {
        url : '/radiologi/input-hasil/save-upload',
        dataType: false,  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: 'post',
        isUpload: true,
        success: function (data) {
            setTimeout(() => {
                $('#btn-kembali').trigger("click");
            }, 1000);
        }
    });
})

$(document).on('change', '.catatan', function() {
    let pkId = $(this).attr('data-id');
    let catatan = $(this).val();
    $.ajax({
        url: "/radiologi/input-hasil/update-catatan?id="+pkId,
        data: {
            catatan: catatan
        },
        type: "post",
        success: function() {
            docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di update"));
        }
    })
});