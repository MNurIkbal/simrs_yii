var table;
var optionStatus = [];
$.each(_status_mcu, function (index, value) {
   optionStatus.push({
      id: index,
      text: value,
   });
});
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
      sorting: [[3, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: false,
      ajax: {
         url: "/mcu/inf-mcu-collective/get-data",
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
         },
         {
            title: 'No',
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Nomor Order",
            data: "no_order",
         },
         {
            title: "Tanggal Order",
            data: "tgl_order",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
         },
         {
            title: "Penjamin",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} </br> ${rowData.penjamin != null ? rowData.penjamin : ''}`
            }
         },
         {
            title: "Paket MCU",
            data: "paket_mcu",
         },
         {
            title: "Pasien Order",
            searchable: false,
            orderable: false,
            data: "pasien_order",
            className: "text-right",
         },
         {
            title: "Pasien Periksa",
            searchable: false,
            orderable: false,
            data: "pasien_periksa",
            className: "text-right",
         },
         {
            title: "Sisa Pasien",
            searchable: false,
            orderable: false,
            data: "sisa_pasien",
            className: "text-right",
         },
         {
            title: "Status",
            data: "status_mcu",
         },
      ],
      formFilters: [
         'no_order',
         {
            fieldName: 'carabayar_id',
            label: 'Cara Bayar',
            type: {
               name: 'dropdownScroll',
               url: "/mcu/inf-mcu-collective/filters",
               additionalPayload: {
                  type: 'carabayar_id'
               }
            }
         },
         {
            fieldName: 'penjamin_id',
            label: 'Penjamin',
            type: {
               name: 'dropdownScroll',
               url: "/mcu/inf-mcu-collective/filters",
               additionalPayload: {
                  type: 'penjamin_id'
               }
            }
         },
         {
            fieldName: 'status_mcu_id',
            label: 'Status',
            type: {
               name: 'select',
               payload: optionStatus
            }
         },
      ],
      filterRendered: (wrapper) => {
         $(wrapper).find('[name="status_mcu_id"]').val(status_open).trigger('change');
         $(wrapper).find('[name="carabayar_id"]').bind('change', ({ currentTarget }) => {
            if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
               var data = [
                  {
                     id: '',
                     text: '- Semua -'
                  }
               ]
               $(wrapper).find('[name="penjamin_id"]').html('')
               $(wrapper).find('[name="penjamin_id"]').select2({
                  data,
               })
            }
            else {
               $(wrapper).find('[name="penjamin_id"]').select2InfinityScroll({
                  url: '/mcu/inf-mcu-collective/filters',
                  callbackData: (params) => {
                     return {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                        type: 'penjamin_id',
                        additionalPayload: {
                           carabayar_id: $(currentTarget).val(),
                        }
                     }
                  }
               });
            }
         });
      }
   });

   $(document).on("click", ".reset-mcu", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#example-status_mcu_id--form').val(status_open).trigger('change');
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()
   });
   $('.flex-1').css('display', 'none');
   if ($(this).find('row row__hidden')) {
      $("#filter-section__example .row__hidden").removeClass("row__hidden");
   }

   $('#form-filter__example').on('keyup keypress', function (e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
         e.preventDefault();
         return false;
      }
   });
});


