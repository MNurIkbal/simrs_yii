$(".cetak-invoice-belum-bayar").on("click", function (event) {
   event.preventDefault();
   var _data = $("#invoice-form").serializeArray();
   $().docoForm("click", {
      url: $("#invoice-form").attr('action'),
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      success: function (data) {
         data = data.data;
         var id = data.id;
         var kelompok = data.kelompok;
         var jenis_invoice = data.jenis_invoice;
         var _url = "/kasir/inf-pasien-sudah-bayar/generate-invoice?id=" + id + '&jenis_invoice=' + jenis_invoice + '&invoice_type=tmp&kelompok=' + kelompok;
         window.open(_url, '_blank');
         docoResetForm($("#invoice-form"));
         $("#modal_backdrop").modal('toggle');
      }
   });
});