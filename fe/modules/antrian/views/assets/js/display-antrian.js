

var dualistbox_loket = $('#dualistbox-loket').bootstrapDualListbox({
    selectedListLabel: 'Antrian yang ditampilkan :',
    nonSelectedListLabel: 'Antrian yang tidak ditampilkan :',
    sortByInputOrder: true,
    infoText: false
});
var dualistbox_ruangan = $('#dualistbox-ruangan').bootstrapDualListbox();
var dualistbox_pegawai = $('#dualistbox-pegawai').bootstrapDualListbox({});
var getContent = dualistbox_ruangan.bootstrapDualListbox('getContainer');
var getContentPegawai = dualistbox_pegawai.bootstrapDualListbox('getContainer');

getContent.find('.moveall i').removeClass().addClass('fa fa-arrow-right');
getContent.find('.removeall i').removeClass().addClass('fa fa-arrow-left');
getContentPegawai.find('.moveall i').removeClass().addClass('fa fa-arrow-right');
getContentPegawai.find('.removeall i').removeClass().addClass('fa fa-arrow-left');

$('.is_pegawai').hide();
$('.is_poli').hide();
$('.is_loket').hide();

var lastLoket = [];
var i = 0;

var jenis_id = $('.jenis_antrian').val();
if (jenis_id != '') {
    if (jenis_id == 312) {
        $('.is_poli').show();
        $('.is_pegawai').show();
        $('.is_loket').hide();
    } else {
        $('.is_poli').hide();
        $('.is_pegawai').hide();
        $('.is_loket').show();
    }

    var dataPost = {
        'jenisantrian_id': jenis_id
    }
    $.ajax({
        url: '/antrian/display-antrian/get-loket',
        data: dataPost,
        type: 'post',
        success: function (res) {
            var data = res.response;
            var count = Object.keys(data).length;
            if (parseInt(count) > 0) {
                $('#dualistbox-loket').empty();
                $.each(data, function (key, val) {
                    if (typeof dataLoket[val.loket_id]) {
                        if (dataLoket[val.loket_id]) {
                            dualistbox_loket.append('<option value=' + val.loket_id + ' selected="selected">' + val.loket_nama + '</option>');
                            lastLoket[i] = ''+val.loket_id;
                            i++;
                        } else {
                            dualistbox_loket.append('<option value=' + val.loket_id + '>' + val.loket_nama + '</option>');
                        }
                    }
                    dualistbox_loket.bootstrapDualListbox('refresh', true);
                    // dualistbox_loket.append('<option value=' + val.loket_id + '>' + val.loket_nama + '</option>');
                    // dualistbox_loket.bootstrapDualListbox('refresh', true);
                    // $('#ruangan-col').removeClass();
                    // $('#ruangan-col').addClass('col-md-6');
                    // $('.is_pegawai').show();
                })
            } else {
                $('#dualistbox-loket').empty();
                dualistbox_loket.bootstrapDualListbox('refresh', true);
                // $('#ruangan-col').removeClass();
                // $('#ruangan-col').addClass('col-md-12');
                // $('.is_pegawai').hide();
            }
        }
    })
}
$('.jenis_antrian').val(jenis_id).trigger('change');
$('.jenis_antrian').change(function() {
    var id = $(this).val();
    if (id == 312) {
        $('.is_poli').show();
        $('.is_loket').hide();
    } else {
        $('.is_poli').hide();
        $('.is_loket').show();
    }

    var dataPost = {
        'jenisantrian_id': id
    }
    $.ajax({
        url: '/antrian/display-antrian/get-loket',
        data: dataPost,
        type: 'post',
        success: function (res) {
            var data = res.response;
            var count = Object.keys(data).length;
            if (parseInt(count) > 0) {
                $('#dualistbox-loket').empty();
                $.each(data, function (key, val) {
                    dualistbox_loket.append('<option value=' + val.loket_id + '>' + val.loket_nama + '</option>');
                    dualistbox_loket.bootstrapDualListbox('refresh', true);
                    // $('#ruangan-col').removeClass();
                    // $('#ruangan-col').addClass('col-md-6');
                    // $('.is_pegawai').show();
                })
            } else {
                $('#dualistbox-loket').empty();
                dualistbox_loket.bootstrapDualListbox('refresh', true);
                // $('#ruangan-col').removeClass();
                // $('#ruangan-col').addClass('col-md-12');
                // $('.is_pegawai').hide();
            }
        }
    })
});

$('#dualistbox-ruangan').change(function () {
    var list_id = $(this).val();
    var dataPost = {
        'list_id': list_id
    }

    var length = $(this).find(":selected").length;

    if (length > length+1) {
        $(this).find(":selected").each(function(ind, sel) {
            if (ind > length+1) {
                $(this).prop("selected", false)
            }
        });

        $(this).bootstrapDualListbox('refresh', true);
    }
    var selectedData = $('#dualistbox-pegawai').val();

    $.ajax({
        url: '/antrian/display-antrian/get-ruangan',
        data: dataPost,
        type: 'post',
        success: function(res) {
            var data = res.response;
            var count = data.length;
    
            // Kosongkan daftar pegawai
            $('#dualistbox-pegawai').empty();
    
            if (parseInt(count) > 0) {
                $.each(data, function (key, val){
                    // Tambahkan pegawai ke daftar pegawai
                    dualistbox_pegawai.append('<option value=' + val.pegawai_id + '>' + val.nama_pegawai + '</option>');
                });
            }
    
            // Atur atribut 'selected' berdasarkan data yang sudah disimpan sebelumnya
            $('#dualistbox-pegawai').val(selectedData);
    
            // Perbarui dual listbox pegawai
            dualistbox_pegawai.bootstrapDualListbox('refresh', true);
    
            // Mengatur tampilan berdasarkan hasil
            $('#ruangan-col').removeClass();
            $('#ruangan-col').addClass('col-md-6');
    
            if (parseInt(count) > 0) {
                $('.is_pegawai').show();
            } else {
                $('#dualistbox-pegawai').empty();
                dualistbox_pegawai.bootstrapDualListbox('refresh', true);     
                $('#ruangan-col').removeClass();
                $('#ruangan-col').addClass('col-md-12');  
                $('.is_pegawai').hide();              }
        }
    });
})
$('#dualistbox-pegawai').change(function () {
    var length = $(this).find(":selected").length;

    if (length > length+1) {
        $(this).find(":selected").each(function(ind, sel) {
            if (ind > length+1) {
                $(this).prop("selected", false)
            }
        });

        $(this).bootstrapDualListbox('refresh', true);
    }
})

$('#dualistbox-loket').change(function () {
    var length = $(this).find(":selected").length;
    var newLoket = $('#dualistbox-loket').val();
    var addedLoket = $(newLoket).not(lastLoket).get();
    var removedLoket = $(lastLoket).not(newLoket).get();

    if (isBanyakLoket == -1) {
        var maxLoket = 4;
    } else {
        var maxLoket = 8;
    }

    if (length > maxLoket) {
        $(this).find(":selected").each(function(ind, sel) {
            if ($(this).val() == ''+addedLoket[0]) {
                $(this).prop("selected", false);
            }
        });

        $(this).bootstrapDualListbox('refresh', true);
    } else {
        lastLoket = newLoket;
    }
});

$("#btn-save").on('click', function (event) {
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#antrian-form").serializeArray();
    // data.append("DisplayAntrianForm[layarantrian_latarbelakang]", $("#file")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    
    $(this).docoForm("click", {
        // url: '/antrian/display-antrian/create', // point to server-side PHP script 
        dataType: "json",  // what to expect back from the PHP script, if anything
        cache: false,
        data: dataPost,                         
        method: "POST",              
        // isUpload: true,
        success: function (data) {
            setTimeout(function () {
                window.location.href = "/antrian/display-antrian"
            }, 1000);
        }
    });
})

var modalTemplate = '<div class="modal-dialog modal-lg" role="document">\n' +
    '  <div class="modal-content">\n' +
    '    <div class="modal-header">\n' +
    '      <div class="kv-zoom-actions btn-group">{toggleheader}{fullscreen}{borderless}{close}</div>\n' +
    '      <h6 class="modal-title">{heading} <small><span class="kv-zoom-title"></span></small></h6>\n' +
    '    </div>\n' +
    '    <div class="modal-body">\n' +
    '      <div class="floating-buttons btn-group"></div>\n' +
    '      <div class="kv-zoom-body file-zoom-content"></div>\n' + '{prev} {next}\n' +
    '    </div>\n' +
    '  </div>\n' +
    '</div>\n';

// Buttons inside zoom modal
var previewZoomButtonClasses = {
    toggleheader: 'btn btn-default btn-icon btn-xs btn-header-toggle',
    fullscreen: 'btn btn-default btn-icon btn-xs',
    borderless: 'btn btn-default btn-icon btn-xs',
    close: 'btn btn-default btn-icon btn-xs'
};

// Icons inside zoom modal classes
var previewZoomButtonIcons = {
    prev: '<i class="icon-arrow-left32"></i>',
    next: '<i class="icon-arrow-right32"></i>',
    toggleheader: '<i class="icon-menu-open"></i>',
    fullscreen: '<i class="icon-screen-full"></i>',
    borderless: '<i class="icon-alignment-unalign"></i>',
    close: '<i class="icon-cross3"></i>'
};

// File actions
var fileActionSettings = {
    zoomClass: 'btn btn-link btn-xs btn-icon',
    zoomIcon: '<i class="icon-zoomin3"></i>',
    dragClass: 'btn btn-link btn-xs btn-icon',
    dragIcon: '<i class="icon-three-bars"></i>',
    removeClass: 'btn btn-link btn-icon btn-xs',
    removeIcon: '<i class="icon-trash"></i>',
    indicatorNew: '<i class="icon-file-plus text-slate"></i>',
    indicatorSuccess: '<i class="icon-checkmark3 file-icon-large text-success"></i>',
    indicatorError: '<i class="icon-cross2 text-danger"></i>',
    indicatorLoading: '<i class="icon-spinner2 spinner text-muted"></i>'
};

$('.file-input').fileinput({
    browseLabel: 'Browse',
    browseIcon: '<i class="icon-file-plus"></i>',
    uploadIcon: '<i class="icon-file-upload2"></i>',
    removeIcon: '<i class="icon-cross3"></i>',
    layoutTemplates: {
        icon: '<i class="icon-file-check"></i>',
        modal: modalTemplate
    },
    initialCaption: "No file selected",
    previewZoomButtonClasses: previewZoomButtonClasses,
    previewZoomButtonIcons: previewZoomButtonIcons,
    fileActionSettings: fileActionSettings
});


$('#btn-ulang').on('click', function () {
    location.reload();
})

$('#btn-kembali').on('click', function () {
    window.location.href = "/antrian/display-antrian"
})

if (typeof dataRuangan !== 'undefined') {
    var dataPost = {
        'list_id': dataRuangan
    }
    $.ajax({
        url: '/antrian/display-antrian/get-ruangan',
        data: dataPost,
        type: 'post',
        success: function (res) {
            var data = res.response;
            var count = data.length;
            if (parseInt(count) > 0) {
                $('#dualistbox-pegawai').empty();
                $.each(data, function (key, val) {
                    if (typeof dataPegawai[val.pegawai_id]) {
                        if (dataPegawai[val.pegawai_id]) {
                            dualistbox_pegawai.append('<option value=' + val.pegawai_id + ' selected="selected">' + val.nama_pegawai + '</option>');
                            $('#ruangan-col').removeClass();
                            $('#ruangan-col').addClass('col-md-6');
                            $('.is_pegawai').show();
                        } else {
                            dualistbox_pegawai.append('<option value=' + val.pegawai_id + '>' + val.nama_pegawai + '</option>');
                            $('#ruangan-col').removeClass();
                            $('#ruangan-col').addClass('col-md-6');
                            $('.is_pegawai').show();
                        }
                    }
                    dualistbox_pegawai.bootstrapDualListbox('refresh', true);
                })
            } else {
                $('#dualistbox-pegawai').empty();
                dualistbox_pegawai.bootstrapDualListbox('refresh', true);
            }
        }
    })
}
