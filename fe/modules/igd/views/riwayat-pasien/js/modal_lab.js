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

$('#modal-preview').on('shown.bs.modal', function () {
   $("#preview-content").attr('src', $('#modal-preview').data('url'))
})