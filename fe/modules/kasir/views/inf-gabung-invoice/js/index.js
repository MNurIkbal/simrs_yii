$("#cetak-summary").prop('disabled', true);
$("#cetak-detail").prop('disabled', true);
$("#cetak-kwitansi").prop('disabled', true);
$("#batal-invoice").prop('disabled', true);
$("#detail-invoice").prop('disabled', true);
$(() => {
   table = $("#example").docoTabel({
      filter: false,
      columnDefs: [{
         orderable: false,
         className: 'select-checkbox',
         targets: 0
      }],
      select: {
         style: 'os',
         selector: 'tr'
      },
      sorting: [[5, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: false,
      ajax: {
         url: "/kasir/inf-gabung-invoice/get-data",
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
         },
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            data: "no_invoicegabung",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "pasien",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "no_pendaftaran",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "tgl_invoicegabung",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
         },
         {
            data: "tgl_invoicegabung_ref",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
         },
         {
            data: "penjamin",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "total_invoicegabung",
            className: "text-right",
         },
         {
            data: "status",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "no_pendaftaran_ref",
            orderable : false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "ref_invoice",
            orderable : false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "penjamin_nama_ref",
            orderable : false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_invoicegabung',
            label: 'Tanggal Invoice',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'pasien',
            label: 'Nama Pasien / No. RM'
         },
         'no_pendaftaran',
         {
            fieldName: 'no_invoicegabung',
             label: 'No. Invoice',
         },
         {
            fieldName: 'penjamin_id_cetak',
            label: 'Penjamin',
            type: {
               name: 'dropdownScroll',
               url: "/kasir/inf-gabung-invoice/filters-dropdown",
               additionalPayload: {
                  type: 'penjamin',
               }
            }
         },
         {
            fieldName: 'is_batal',
            label: 'Status Invoice',
            type: {
               name: 'select',
               payload: [
                  {id: false, text: 'INVOICE'},
                  {id: true, text: 'BATAL'}
               ]
            }
         },
      ],
   });
   $(document).on("click", ".btn-reset", function (e) {
      moment.lang("en");
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_invoicegabung-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_invoicegabung-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()

      $("#cetak-summary").prop('disabled', true);
      $("#cetak-detail").prop('disabled', true);
      $("#cetak-kwitansi").prop('disabled', true);
      $("#batal-invoice").prop('disabled', true);
      $("#detail-invoice").prop('disabled', true);
   });

   $(document).on("click", "#example tbody tr", function(){
      var primary = null;
      var is_deleted = false;
      var is_batal = false;
      try {
         primary = table.row(".selected").data().primary;
         is_deleted = table.row(".selected").data().is_deleted;
         is_batal = table.row(".selected").data().is_batal;
      }
      catch(e) {
         primary = null;
         is_deleted = false;
         is_batal = false;
      }
      
      if(primary && !is_deleted) {
         $("#cetak-summary").attr("data-target",'/kasir/inf-gabung-invoice/cetak?invoicegabung_id='+primary);
         if (!is_batal) {
            $("#cetak-summary").prop('disabled', false);
            $("#cetak-detail").prop('disabled', false);
            $("#cetak-kwitansi").prop('disabled', false);
            $("#batal-invoice").prop('disabled', false);  
         } else {
            $("#cetak-summary").prop('disabled', true);
            $("#cetak-detail").prop('disabled', true);
            $("#cetak-kwitansi").prop('disabled', true);
            $("#batal-invoice").prop('disabled', true); 
         }
         $("#detail-invoice").prop('disabled', false);
      } else {
         $("#cetak-summary").prop('disabled', true);
         $("#cetak-detail").prop('disabled', true);
         $("#cetak-kwitansi").prop('disabled', true);
         $("#batal-invoice").prop('disabled', true); 
         $("#detail-invoice").prop('disabled', true);
      }
   });

   $('#batal-invoice').click('click', function(e){
      e.preventDefault();

      const target = $(this).attr("data-target");
      const tableData = table.row(".selected").data();

      PNotify.removeAll();
      if (tableData.is_batal === true) {
         new PNotify({
            title: "Proses Gagal",
            text: "Data sudah dibatalkan!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
         });
      } else {
         if (typeof tableData !== "undefined") {
            $().docoForm("click",{
               url : target,
               method : "POST",
               type : "json",
               data: tableData,
               success : function (data) {
                  table.draw();
               }
         });
         } else {
            new PNotify({
              title: "Terjadi Kesalahan",
              text: "Belum ada data yang dipilih!",
              addclass: "alert alert-warning alert-arrow-right alert-styled-right",
              type: "warning"
            });
         }
      }
   });

   $("#cetak-summary-gabung").click(function(e){
      e.preventDefault();
      var kelompok = null;
      var tableData = table.row(".selected").data();
      var pendaftaran_id = null;
      var invoice_id = null;
      var penjualanresep_id = null;
      var invoicegabung_id = null;
   
      if(typeof tableData !== "undefined") {
         pendaftaran_id = tableData.pendaftaran_id_cetak;
         invoice_id = tableData.ref_pembayaran_id;
         penjualanresep_id = tableData.penjualanresep_id;
         invoicegabung_id = tableData.invoicegabung_id

         if(penjualanresep_id != null) {
            kelompok = PASIEN_ALKES;
         }
         var url = "/kasir/inf-gabung-invoice/cetak-invoice?invoice_id=" + invoice_id + "&invoicegabung_id=" + invoicegabung_id + "&kelompok=" + kelompok;
         window.open(url);
      }
   });
});