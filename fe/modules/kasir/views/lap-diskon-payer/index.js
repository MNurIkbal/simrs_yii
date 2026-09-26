var table;
$(() => {
   const _module = "kasir/lap-diskon-payer/";
   const _baseUrl = baseUrl + _module;
   table = $("#example").docoTabel({
      filter: false,
      sorting: [[1, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: false,
      ajax: {
         url: _baseUrl + "get-data",
      },
      columns: [
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
            title: "Bill No",
            data: "no_pembayaran",
            render: (data) => {
               return data == "" || data == null ? "-" : data
               //moment(data).format("DD MMMM YYYY")
            }
         },
         {
            title: "Discount Date",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.tgl_diskon != null ? moment(rowData.tgl_diskon).format("DD MMMM YYYY") : '-'}`
            }
         },
         {
            title: "IP No.",
            data: "no_pendaftaran",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Patient Name",
            data: "nama_pasien",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Authorized By",
            data: "authorized_by",
            searchable: false,
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Bill Amt.",
            data: "billing_total",
            searchable: false,
            orderable: false,
            render: $.fn.dataTable.render.number(".", ",", 0, ""),
         },
         {
            title: "Discount",
            data: "discount_total",
            searchable: false,
            orderable: false,
            render: $.fn.dataTable.render.number(".", ",", 0, ""),
         },
         {
            title: "Username",
            data: "username",
            searchable: false,
            className: "text-right",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Remarks",
            data: "remarks",
            searchable: false,
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Discharge Date",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.tgl_keluar != null ? moment(rowData.tgl_keluar).format("DD MMMM YYYY") : '-'}`
            }
            //moment(data).format("DD MMMM YYYY")
         },
         {
            title: "Discount Type",
            data: "diskon_type",
            searchable: false,
            className: "text-right",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            },
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_diskon',
            label: 'Tanggal Diskon',
            type: {
               name: 'rangeDate',
            }
         },
         // {
         //    fieldName: 'carabayar_id',
         //    label: 'Cara Bayar',
         //    type: {
         //       name: 'select',
         //       payload: caraBayar
         //    }
         // },
         // {
         //    fieldName: 'penjamin_id',
         //    label: 'Nama Payer',
         //    type: {
         //       name: 'dropdownScroll',
         //       url: baseUrl + _module + "get-penjamin"
         //    }
         // },
      ],
      filterRendered: (wrapper) => {
         $(wrapper).find('[name="carabayar_id"]').bind('change', ({ currentTarget }) => {
            $(wrapper).find('[name="penjamin_id"]').val(null).trigger('change')
            $(wrapper).find('[name="penjamin_id"]').select2InfinityScroll({
               url: baseUrl + _module + 'get-penjamin',
               callbackData: (params) => {
                  return {
                     payload: {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                        carabayar_id: $(currentTarget).val(),
                     }
                  }
               }
            });
         });
      }
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('.startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('.endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()
   });
});