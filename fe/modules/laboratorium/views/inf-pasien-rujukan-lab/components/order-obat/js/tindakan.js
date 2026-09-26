var tableObat;
var _checkStatus = function () {
   var _currentStatus = _status;
   if (_statusSelesai === _currentStatus) {
      $("#form-order-tindakan").find("input, select").prop("disabled", true);
      $("#btn-simpan-obat").prop("disabled", true);
   }
}

var hapusTindakanObat = function (jenis, id) {
   var action = $('#delete-item-' + id).attr('action');
   $('#delete-item-' + id).docoForm('delete',{
      url: action,
      success: function () {
         tableObat.draw();
      }
   });
}

$(document).ready(function () {
   _checkStatus();
   tableObat = $("#example-obat").docoTabel({
      filter: false,
      destroy: true,
      sorting: [[1, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
         url: "/laboratorium/inf-pasien-rujukan-lab/get-data-tindakan-obat?pendaftaran_id=" + _pendaftaran_id + '&pasienmasukpenunjang_id=' + _id,
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = tableObat.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Tanggal Tindakan",
            data: "tgl_tindakan",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            title: "Nama Pemeriksaan",
            data: "nama_pemeriksaan",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Nama Tindakan",
            data: "nama_tindakan",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Obat/Alkes",
            data: "obat",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Petugas 1",
            data: "petugas_1",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Petugas 2",
            data: "petugas_2",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Qty",
            data: "qty",
            className: 'text-right',
            searchable: false,
            sortable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Ditagihkan",
            data: "is_ditagihkan",
            searchable: false,
            className: 'text-center',
            sortable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Hapus",
            data: null,
            orderable: false,
            className: 'text-center',
            render: (data, rowElement, rowData) => {
               return rowData.action
            }
         },
      ],
      formFilters: [
         {
            fieldName: 'nama_pemeriksaan',
            label: 'Nama Pemeriksaan',
         },
         {
            fieldName: 'nama_tindakan',
            label: 'Nama Tindakan',
         },
         {
            fieldName: 'obat',
            label: 'Obat/Alkes',
         },
         {
            fieldName: 'petugas_1',
            label: 'Petugas 1',
         },
      ]
   });

   $(document).on("keydown", null, "alt+s", function (event) {
      if ($("#modal_backdrop").hasClass("in")) {
         $("#modal_backdrop, #btn-simpan-obat").click();
      } else {
         $(".form-horizontal, #btn-simpan-obat").click();
      }
   });
   
   $("#tindakan_id").docoPaginationSelec2(
      config = {
         placeholder: "-- Cari Tindakan --",
         _api: _endPoint + "/order-obat-alkes/list-tindakan?kelaspelayanan_id=" + _kelaspelayanan_id + '&penjamin_id=' + _penjamin_id,
      }
   )

   $('#tindakan_id').on('change', function () {
      var _data = $(this).select2('data');
      if (typeof _data[0].datavalue != 'undefined') {
         var _dataValue = _data[0].datavalue;
         var _harga = typeof _dataValue.harga_tariftindakan != 'undefined' ? docoHelper.convertToRupiah(_dataValue.harga_tariftindakan) : null;
         $('#pemakaian-barang-jumlah_tarif').val(_harga);
      }
   });
   var _muatUlang = function () {
      docoResetForm($("#form-order-tindakan"));
      $('div').removeClass('has-error');
      $('span.help-block.error').remove();
      $('div.help-block.error').remove();
      $("#tindakan_id").val("").trigger("change");
      $('#pemakaian-barang-qty_tindakan').val(1).trigger("change");
      $("#pemeriksaan_id").val(_pelayananId).trigger("change");
      $('#pemakaian-barang-jumlah_tarif').val("").trigger("change");
      $('#petugas_satu').val("").trigger("change");
      $('#petugas_dua').val("").trigger("change");
   }

   // function hapusTindakanObat(jenis, id) {
   //    event.preventDefault();
   //    $(this).docoForm("delete", {
   //       success: function (data) {
   //          tableObat.draw();
   //       }
   //    });
   // }

   // $(document).on("click", ".delete", function (event) {
   //    event.preventDefault();
   //    $(this).docoForm("delete", {
   //       success: function (data) {
   //          tableObat.draw();
   //       }
   //    });
   // });

   $("#btn-ulang").on("click", function (event) {
      event.preventDefault();
      _muatUlang();
   });

   $("#btn-simpan-obat").on("click", function (event) {
      event.preventDefault();
      var _form = $("#form-order-tindakan");
      var _data = _form.serializeArray();
      _data.push(
         {
            name: "OrderObatAlkesForm[jenis]",
            value: 'tindakan'
         }
      );
      _data.push(
         {
            name: "OrderObatAlkesForm[pemeriksaan_id]",
            value: $("#pemeriksaan_id").val()
         }
      );
      _data.push(
         {
            name: "OrderObatAlkesForm[tindakan_id]",
            value: $("#tindakan_id").val()
         }
      );
      _data.push(
         {
            name: "OrderObatAlkesForm[qty_tindakan]",
            value: $("#pemakaian-barang-qty_tindakan").val()
         }
      );
      _data.push(
         {
            name: "OrderObatAlkesForm[petugas_satu]",
            value: $("#petugas_satu").val()
         }
      );
      _data.push(
         {
            name: "OrderObatAlkesForm[petugas_dua]",
            value: $("#petugas_dua").val()
         }
      );
      $().docoForm("click", {
         data: _data,
         url: _form.attr("action"),
         success: function (data) {
            tableObat.draw();
            _muatUlang();
         }
      });
   });

   $("#pemakaian-barang-qty_tindakan").on("keyup change", function (event) {
      event.preventDefault();
      var _val = docoHelper.convertToAngka($(this).val());
      var _idTindakan = $("#tindakan_id").select2('data');
      if (typeof _idTindakan[0].datavalue != 'undefined') {
         var _dataValue = _idTindakan[0].datavalue;
         var result = docoHelper.convertToRupiah(_dataValue.harga_tariftindakan * _val);
         $("#pemakaian-barang-jumlah_tarif").val(result);
      }
   })
   $('.more-filter').css('display', 'none')
});

