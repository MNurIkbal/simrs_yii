
$(() => {
    jenisInvoice = 1
    $('#penjamin_id').prop('disabled', true);
    $('.jenis_invoice').on('change', function () {
        if ($(this).val() == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
            $('#penjamin_id').prop('disabled', false);
        }
        else {
            $('#penjamin_id').prop('disabled', true);
        }
    })

    $('.jenis_invoice').on('click', function(){
        jenisInvoice = $(this).val()
    })

    $(".btn-cetak-invoice").on("click", function (event) {
        var _data = $("#invoice-form").serializeArray();
        penjaminId = $('#penjamin_id').val()
        var url_detail = "/kasir/inf-pasien-belum-bayar/show-popup?id="
        var url_summary = "/kasir/inf-pasien-belum-bayar/cetak-rincian?id="
        var url_link = (isDetail == 1) ? url_detail : url_summary
        var url = url_link + _pendaftaranId+"&instalasi_id="+_instalasiId;

        url += "&jenis_invoice="+jenisInvoice
        if(jenisInvoice == 3){
            url += "&penjamin_id="+penjaminId
        }
       
        window.open(url);
    });
})

