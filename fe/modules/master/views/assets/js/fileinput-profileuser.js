    var tabel = $('#data-profil').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'nama_pemakai', name : 'nama_pemakai'},
            {data: 'jabatan_nama',name : 'jabatan_nama'},
            {data: 'ruangan_nama', name:'ruangan_nama'},                        
            {data: 'aksi',name : 'aksi'}
        ],
        colNoOrder : [0,4]
    });
    var _afterSave = function (bool) {
        tabel.reload();
    }    
    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        tabel.reset();
    });

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