$(document).on('click', '.btn-cetak-penunjang', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    console.log('data button', dataBtn.url);
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        $("#modalProgramTerapi").css('z-index', '1040')
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$(document).on('click', '#dismiss-preview-btn', () => {
    $("#preview-content").attr('src', 'about:blank')
    $("#modalProgramTerapi").css('z-index', '1050')
})
$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

$(document).on('click', '.btn-cetak-pelayanan', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        $("#modalProgramTerapi").css('z-index', '1040')
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$(document).on('click', '#dismiss-preview-btn', () => {
    $("#preview-content").attr('src', 'about:blank')
    $("#modalProgramTerapi").css('z-index', '1050')
})
$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

$('.btn-view-gambar').on('click', (e) => {
    $("#modal-gambar-radiologi iframe").attr('src', $(e.target).attr('href'))
    $("#modal-gambar-radiologi").modal('show');
    return false;
})
$('#modal-preview').on('shown.bs.modal', function() {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

