var tableTindakan;
var _checkStatus = function () {
   var _currentStatus = _status;
   if (_statusSelesai === _currentStatus) {
      $("#tindakan-form").find("input, select").prop("disabled", true);
      // $("#btn-save, #btn-ulang").prop("disabled", true);
   }
}

$(document).ready(function () {
   _checkStatus();
   tableTindakan = $("#tmp-tindakan").docoTabel({
      filter: false,
      sorting: [[1, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
         url: _endPoint + "/order-obat-alkes/get-data-tindakan-obat?pendaftaran_id=" + _pendaftaran_id + '&pasienmasukpenunjang_id=' + _id + '&jenis=tindakan',
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = tableTindakan.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            data: "tgl_tindakan",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            data: "nama_tindakan",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "petugas_1",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "petugas_2",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "qty",
            className: 'text-right',
            searchable: false,
            sortable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
			{
            data: "harga_tindakan",
            className: 'text-right',
            searchable: false,
            sortable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: null,
            orderable: false,
            className: 'text-center',
            render: (data, rowElement, rowData) => {
               return rowData.action
            }
         },
      ],
      drawCallback: function( settings ) {
         var _baseUrl = `${_endPoint}/order-obat-alkes`;
         _paramsTindakan = `penunjang_id=${_id}&pendaftaran_id=${_pendaftaran_id}&jenis=tindakan`;

         $.ajax({
            url:`${_baseUrl}/data-pemakaian-tindakan?${_paramsTindakan}`, 
            success: function(result){
               result.result.map((tindakan) => {
                  if(typeof tindakan.datavalue != "undefined" ){
                     if(typeof tindakan.datavalue.tindakanpelayanan_id != "undefined" ){
                        tindakanPelayananId = tindakan.datavalue.tindakanpelayanan_id;
                        if(!_arrayTindakan[tindakan.id]){
                           var _options = new Option(tindakan.text, tindakan.id, false, false);
                           $('#daftartindakan_id').append(_options);
                           _arrayTindakan[tindakan.id] = {
                              daftartindakan_nama : tindakan.text,
                              tindakanpelayanan_id : tindakanPelayananId,
                           }
                        }
                     }
                  }
               })            
            }
         });
      }
   });
   // $(document).on("keydown", null, "alt+s", function (event) {
   //    if ($("#modal_backdrop").hasClass("in")) {
   //       $("#modal_backdrop, #btn-add-tindakan").click();
   //    } else {
   //       $(".form-horizontal, #btn-add-tindakan").click();
   //    }
   // });
   $("#tindakan_id").docoPaginationSelec2(
      config = {
         placeholder: "-- Cari Tindakan --",
         _api: _endPoint + "/order-obat-alkes/data-tindakan-ruangan?kelaspelayanan_id=" + _kelaspelayanan_id + '&penjamin_id=' + _penjamin_id + '&ruangan_id=' + _ruangan_id,
      }
   )
   $('#tindakanform-petugas_satu').on('change', function(){
      var _dokterId = $(this).val();
      var _tindakanId = $('#tindakan_id').val();
      if(_tindakanId == '') {
         return false;
      }

      var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruangan_id}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_kelaspelayanan_id}&dokter_id=${_dokterId}`
      $.ajax({
         url: _endPoint + '/order-obat-alkes/get-tarif?' + _params,
         type: 'GET',
         success: function(data) {
            $("#harga_tariftindakan").val(data.harga_tariftindakan)
            _hitungTarif()
         }
      })
   });
   $('#tindakan_id').on('change', function(){
      var _tindakanId = $(this).val();
      if(_tindakanId == "") {
         return false;
      }
      else {
         var _dokterId = $('#tindakanform-petugas_satu').val();
         var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruangan_id}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_kelaspelayanan_id}&dokter_id=${_dokterId}`
         $.ajax({
            url: _endPoint + '/order-obat-alkes/get-tarif?' + _params,
            type: 'GET',
            success: function(data) {
               $("#harga_tariftindakan").val(data.harga_tariftindakan)
               _hitungTarif()
            }
         })
      }
   });
   var _muatUlang = function () {
      $('div').removeClass('has-error');
      $('span.help-block.error').remove();
      $('div.help-block.error').remove();
      $("#tindakan_id").val("").trigger("change");
      $('#pemakaian-barang-qty_tindakan').val(1).trigger("change");
      $("#pemeriksaan_id").val(_pelayananId).trigger("change");
      $('#tindakanform-petugas_satu').val("").trigger("change");
      $('#tindakanform-jumlah_tarif').val("").trigger("change");
      $('#tindakanform-petugas_dua').val("").trigger("change");
   }
   $(document).on("click", ".delete", function (event) {
      event.preventDefault();
      $(this).docoForm("delete", {
         success: function (data) {
            tableTindakan.draw();
            _muatUlang()
         }
      });
   });
   $("#btn-ulang").on("click", function (event) {
      event.preventDefault();
      _muatUlang();
   });
   $("#btn-add-tindakan").on("click", function (event) {
      event.preventDefault();
      var _form = $("#tindakan-form");
      var _data = _form.serializeArray();
      _data.push(
         {
            name: "jenis",
            value: 'tindakan'
         }
      );
      $().docoForm("click", {
         data: _data,
         url: _form.attr("action"),
         success: function (data) {
            tableTindakan.draw();
            _muatUlang();
         }
      });
   });
   $("#tindakanform-qty").on("keyup", function (event) {
      event.preventDefault();
      _hitungTarif()
   })
   $('.more-filter').css('display', 'none')
   var _hitungTarif = function () {
      var qty = $('#tindakanform-qty').val() ? parseFloat($('#tindakanform-qty').val()) : 0;
      var harga_satuan = parseFloat($('#harga_tariftindakan').val()) 
      var jumlah = parseFloat(harga_satuan*qty);
      $('#tindakanform-jumlah_tarif').val(docoHelper.convertToRupiah(jumlah))
   }
});

