var table;
var _base_table_url = "/master/konfig-asuransi/get-data";

$(document).ready(function () {
  moment.locale("en");
  table = $("#example").docoTabel({
    filter: true,
    columnDefs: [
      {
        orderable: false,
        className: "select-checkbox",
        targets: 0,
      },
    ],
    select: {
      style: "os",
      selector: "tr",
    },
    sorting: [[2, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    cache: false,
    cacheFilter: false,
    ajax: {
      url: _base_table_url,
    },
    columns: [
      {
        data: null,
        searchable: false,
        orderable: false,
        defaultContent: "",
      },
      {
        data: null,
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData, rowAdditionalData) => {
          var tableInfo = table.page.info();
          return tableInfo.start + rowAdditionalData.row + 1;
        },
      },
      {
        title: "Nama Asuransi",
        data: "provider_code",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        title: "Created Date",
        data: "created_date",
        render: (data) => {
          return data == "" || data == null
            ? "-"
            : moment(data).format("DD MMM YYYY");
        },
      },
      {
        title: "Status",
        data: "is_active",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
    ],
    formFilters: [
      {
        fieldName: "provider_code",
        label: "Nama Asuransi",
      },
      {
        fieldName: "is_active",
        label: "Status",
        type: {
          name: "select",
          payload: [
            {
              id: 1,
              text: "Aktif",
            },
            {
              id: 0,
              text: "Tidak Aktif",
            },
          ],
        },
      },
    ],
  });

  $(".dataTables_filter").hide();
  $("#btn-search__example").css("display", "none");
  $("#btn-reset__example").css("display", "none");
  $(document).on("click", ".btn-reset", function (e) {
    const tableId = "example";
    const element = $(`#filter-section__${tableId}`);
    const formWrapper = $(`#form-filter__${tableId}`);
    element.find("input").val("");
    element.find("select").val(null).trigger("change");
    const tableElement = $(`#${tableId}`).DataTable();
    showLoader();
    tableElement.context[0].ajax.data.advancedFilter =
      serializeArrayToJson(formWrapper);
    tableElement.ajax.url("/master/konfig-asuransi/get-data").load();
  });
});
