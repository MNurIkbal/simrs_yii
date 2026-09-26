$(document).on('click', '.btn-cetak-penunjang', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$(document).on('click', '#dismiss-preview-btn', () => {
    $("#preview-content").attr('src', 'about:blank')
})
$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
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

$(document).on('click', '#dismiss-preview-btn', () => {
    $("#preview-content").attr('src', 'about:blank')
    // $("#modal_riwayat").css('z-index', '1050')
})
$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

$('.btn-view-gambar').on('click', (e) => {
    let hasilpemeriksaanrad_id = $('.btn-view-gambar').attr('data-id');
    $.ajax({
        url: '/radiologi/expertise/get-hasil-radiologi?hasilpemeriksaanrad_id='+ hasilpemeriksaanrad_id,
        method: 'GET',
        success: function (res) {
            if(typeof(res.response) != 'undefined') {
                var _response = res.response
                var imageLink = _response.image_link
                $("#modal-gambar-radiologi iframe").attr('src', imageLink)
                $("#modal-gambar-radiologi").modal('show');
            }
        }
    });
    
    return false;
})
$('#modal-preview').on('shown.bs.modal', function() {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})  



$(document).on('click', '#btn-cetak-penunjang-rad', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if(dataBtn.isread == 'not-read' && dokter_dpjp == user){
        $.ajax({
            url: "/igd/riwayat-pasien/read-cetakan-radiologi",
            data: [{
                name: 'pasienmasukpenunjang_id',
                value: dataBtn.pasienmasukpenunjang_id
            },
            {
                name: 'daftartindakan_id',
                value: dataBtn.daftartindakan_id
            },
            {
                name: 'tindakanpelayanan_id',
                value:dataBtn.tindakanpelayanan_id
            },
            ],
            method: 'POST',
            success: () => {
                $(currentTarget).removeClass('btn-warning').addClass('btn-info')
                $(currentTarget)[0].childNodes[0].remove()
                dataBtn.isread = 'is-read'
            }
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
            success : function(data) {}
        });
    }
})