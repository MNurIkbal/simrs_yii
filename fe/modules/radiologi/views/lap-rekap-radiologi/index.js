var table;
$(() => {
   moment.locale("en");
   var _tipeProsedur = [
      {
         id: 'Cito',
         text: 'Cito'
      },
      {
         id: 'Elektif',
         text: 'Elektif'
      },
   ];
   table = $("#example").docoTabel({
      filter: false,
      select: {
         style: 'os',
         selector: 'tr'
      },
      sorting: [[1, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: false,
      scrollY: true,
      ajax: {
         url: _module + "get-data",
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
            title: "Tanggal Persetujuan",
            data: "tgl_persetujuan",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            title: "Kelas Pelayanan",
            data: "kelaspelayanan_nama",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Pemeriksaan",
            data: "daftartindakan_nama",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Tipe Prosedur",
            data: "tipe_prosedur",
            searchable: false,
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Jumlah Pemeriksaan",
            data: "jumlah",
            className: "text-right",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Harga Total",
            data: "total_harga",
            className: "text-right",
            searchable: false,
            render: $.fn.dataTable.render.number('.', ',', 0, ''),
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_persetujuan',
            label: 'Tanggal Persetujuan',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'kelaspelayanan_id',
            label: 'Kelas Pelayanan',
            type: {
               name: 'dropdownScroll',
               url: _module + "filters",
               additionalPayload: {
                  type: 'kelas',
               }
            }
         },
         {
            fieldName: 'daftartindakan_id',
            label: 'Nama Pemeriksaan',
            type: {
               name: 'dropdownScroll',
               url: _module + "filters",
               additionalPayload: {
                  type: 'pemeriksaan',
               }
            }
         },
         {
            fieldName: 'tipe_prosedur',
            label: 'Tipe Prosedur',
            type: {
               name: 'select',
               payload: _tipeProsedur,
            }
         },
      ],
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_persetujuan-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_persetujuan-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url(_module + "get-data").load()
   });

   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');
})
