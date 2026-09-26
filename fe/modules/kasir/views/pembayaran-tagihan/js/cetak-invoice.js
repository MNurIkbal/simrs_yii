$(".btn-cetak-inv").on("click", function (event) {
    event.preventDefault();
    var $form = $("#cetak-invoice-form");
    var _data = $form.serializeArray();
    var _detail_invoice = $("#detailInvoice").val();
    var _endPoint = (_detail_invoice == 1) ? 'cetak-detail-invoice' : 'cetak-invoice';
    $().docoForm("click",{
        url:  $form.attr('action'),
        data : _data,
        skipConfirm: true,
        skipSuccessNotif: true,
        success : function (data) {
            data = data.data;
            var id = data.pembayaranpelayanan_id;
            var pembayaran_id = data.pembayaran_id;
            var jenis_invoice = data.jenis_invoice;
            var _url = "/kasir/pembayaran-tagihan/" + _endPoint + "?id=" + id + '&jenis_invoice=' + jenis_invoice + '&invoice_id=' + pembayaran_id;
            window.open(_url, '_blank');
            docoResetForm($form);
            $("#modal_backdrop").modal('toggle');
        }
    });
});