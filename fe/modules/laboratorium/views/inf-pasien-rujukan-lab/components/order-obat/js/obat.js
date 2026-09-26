var _tmp = {};
var _checkStatus = function () {
   var _currentStatus = _status;
   if (_statusSelesai === _currentStatus) {
      $("#form-order-obat").find("input, select").prop("disabled", true);
      $("#btn-save, #btn-ulang").prop("disabled", true);
   }
}

$(document).ready(function () {
   _checkStatus();
   $(document).on("keydown", null, "alt+s", function (event) {
      if ($("#modal_backdrop").hasClass("in")) {
         $("#modal_backdrop, #btn-save").click();
      } else {
         $(".form-horizontal, #btn-save").click();
      }
   });
   $("#obatalkes_id").select2InfinityScroll({
      url: `${_endPoint}/order-obat-alkes/list-obat`,
      callbackData: (param) => {
         return {
            ...param,
            penjamin_id: _penjamin_id,
            kelaspelayanan_id: _kelaspelayanan_id,
         }
      },
      callbackProccess: ((response) => {
         let results = []
         response.results.map((item) => {
            results.push({
               id: item.obatalkes_id,
               text: `${item.obatalkes_kode} - ${item.obatalkes_nama} - stok ${item.qty_tersedia}`,
               ...item
            })
         })
         delete response.results
         return {
            ...response,
            results
         }
      })
   })
   $('#obatalkes_id').on('change', function () {
      var _data = $(this).select2('data');
      if (typeof _data[0] != 'undefined') {
         var _dataValue = _data[0];
         var _harga = typeof _dataValue.hargaygdipakai != 'undefined' ? docoHelper.convertToRupiah(_dataValue.hargaygdipakai) : null;
         $('#jumlah_tarif-bmhp').val(_harga);
      }
   });
   var _muatUlang = function () {
      docoResetForm($("#form-order-obat"));
      $('div').removeClass('has-error');
      $('span.help-block.error').remove();
      $('div.help-block.error').remove();
      $("#obatalkes_id").val("").trigger("change");
      $('#pemakaian-barang-qty').val(1).trigger("change");
      $("#pemeriksaan_id").val(_pelayananId).trigger("change");
      $("#orderobatalkesform-is_tagihkan").prop('checked', true);
      $('#petugas_satu-bmhp').val("").trigger("change");
      $('#petugas_dua-bmhp').val("").trigger("change");
      $('#jumlah_tarif-bmhp').val("").trigger("change");
   }
   $(document).on("click", ".delete", function (event) {
      event.preventDefault();
      $(this).docoForm("delete", {
         success: function (data) {
         }
      });
   });

   $("#btn-ulang").on("click", function (event) {
      event.preventDefault();
      _muatUlang();
   });

   $("#pemakaian-barang-qty").on("keyup change", function (event) {
      event.preventDefault();
      var _val = docoHelper.convertToAngka($(this).val());
      var _idObatAlkes = $("#obatalkes_id").select2('data');
      if (typeof _idObatAlkes[0] != 'undefined') {
         var _dataValue = _idObatAlkes[0];
         if (_dataValue.qty_tersedia < _val) {
            $(this).val(_dataValue.qty_tersedia);
            return true;
         }
         var result = docoHelper.convertToRupiah(_dataValue.hargaygdipakai * _val);
         $("#jumlah_tarif-bmhp").val(result);
      }
   })

   $("#btn-simpan-add-obat").on("click", function (event) {
      var _idObatAlkes = $("#obatalkes_id").select2('data');
      if (typeof _idObatAlkes[0] != "undefined") {
         _obatAlkes = _idObatAlkes[0];
         var _dataValue = _obatAlkes;
      }
      event.preventDefault();
      var _form = $("#form-order-obat");
      var _data = _form.serializeArray();
      _data.push(
         {
            name: "OrderObatAlkesForm[stok]",
            value: _dataValue.qty_tersedia
         },
         {
            name: "OrderObatAlkesForm[jenis]",
            value: 'obat'
         }
      );
      $().docoForm("click", {
         data: _data,
         url: _form.attr("action"),
         success: function (data) {
            _muatUlang();
         }
      });
   });
});

