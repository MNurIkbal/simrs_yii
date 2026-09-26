setTimeout(function () {
    var table_riwayat_upload = $('#table-upload-dokumen').docoTabel({
        filter: false,
        cacheFilter: false,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url : url+'/get-dokumen-list?norm=' + norm + '&instalasi_id=' + instalasi_id,
            url: `/api/upload-dokumen/get-dokumen-list?norm=` +norm,
        }, 
        columns: table_column_upload,
        // formFilters: formfilt,
        // filterRendered: (wrapper) => {
        //     $('#advanced-filter-table-riwayat').find('.flex-1').css('display', 'none')
        //     setTimeout(function () {
        //         $('#ruangan_pend_id--filter').find('.select2').remove()
        //         $('#table-riwayat-ruangan_pend_id--form').select2()
        //         $('#table-riwayat-ruangan_pend_id--form').bind('change', () => {
        //             $('#btn-search__table-riwayat').trigger('click')
        //         })
        //      }, 300);
        // }
    
    });
}, 300); // dibuat set timeout agar table riawayat pasien muncul terlebih dahulu

$(document).on('click', '.btn-cetak-penunjang', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        // $("#modal_riwayat").css('z-index', '1040')
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$(document).on('click', '#dismiss-preview-btn', () => {
    $("#preview-content").attr('src', 'about:blank')
    // $("#modal_riwayat").css('z-index', '1050')
})
$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

$('#modal-preview-img').on('shown.bs.modal', function() {
    // $("#modal_riwayat").css('z-index', '1050')
})

$(document).on('click', '.btn-cetak-pelayanan', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        // $("#modal_riwayat").css('z-index', '1040')
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$(document).on('click', '.btn-show-konsul', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        // $("#modal_riwayat").css('z-index', '1040')
        $("#modal-show-konsul").modal({
            backdrop: 'static',
            keyboard: false
        })

        $("#preview-wrapper-konsul").docoLoad({
            url: dataBtn.url,
            dataType: 'html',
            success: function (data) { }
        });
    }
})