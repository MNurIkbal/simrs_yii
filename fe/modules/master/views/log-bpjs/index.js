var table;
$(() => {
   moment.locale("en");
   table = $("#example").docoTabel({
      filter: true,
      select: {
         style: 'os',
         selector: 'tr'
      },
      sorting: [[2, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      cache: false,
      cacheFilter : true,
      ajax: {
         url: "/master/log-bpjs/get-data",
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
            title: "Detail", 
            data: "detail",
            searchable: false,
            orderable: false
         },
         {
            title: "Log BPJS ID",
            data: "logbpjs_id",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Created Date",
            data: "created_date",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Url",
            data: "url",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         // {
         //    title: "Request",
         //    data: "request",
         //    orderable: false,
         //    render: (data) => {
         //       return data == "" || data == null ? "-" : data
         //    }
         // },
         // {
         //    title: "Response",
         //    data: "response",
         //    searchable: false,
         //    orderable: false,
         //    render: (data) => {
         //       return data == "" || data == null ? "-" : data
         //    }
         // },
      ],
      formFilters: [
         {
            fieldName: 'created_date',
            label: 'Tanggal Log',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'request',
            label: 'Pencarian berdasarkan Request/Response/Url'
         },
      ],
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      $(".legend-information").css("border", "1px solid #dddddd");
      element.find('input').val('')
      element.find('#created_date-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#created_date-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url("/master/log-bpjs/get-data").load()
   });
   $(".dataTables_filter").hide();
   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');
})
