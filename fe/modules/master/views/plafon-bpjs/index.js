var table;
var _baseUrl = "/master/plafon-bpjs";

$(document).ready(function () {
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
    cache: false,
    cacheFilter: false,
    ajax: {
      url: _baseUrl + "/get-data",
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
        title: "Instalasi",
        data: "instalasi",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        title: "Kelas Pelayanan",
        data: "kelas",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
    //   {
    //     title: "Jumlah Ruangan Yang Termapping",
    //     data: "jumlah_ruangan",
    //     searchable: false,
    //     orderable: false,
    //     render: (data, type, row) => {
    //         var jumlah = data || 0;
    //         var tooltip = row.list_ruangan ? row.list_ruangan : "";
    //         return `<a href="#" title="${tooltip}"><b>${jumlah}</b> Ruangan</a>`;
    //     },
    //   },
      {
        title: "Plafon",
        data: "plafon",
        searchable: false,
        className: "text-right",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Plafon Ruangan",
        data: "plafon_ruangan",
        searchable: false,
        className: "text-right",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Status",
        data: "is_active",
        orderable: false,
      },
    ],
    formFilters: [
        {
            fieldName: 'instalasi_id',
            label: 'Instalasi',
            type: {
                name: 'dropdownScroll',
                url: `${_baseUrl}/filters`,
                additionalPayload: {
                    type: 'instalasi'
                }
            }
        },
        {
            fieldName: "list_ruangan_id",
            label: "Ruangan",
            type: {
                name: "dropdownScroll",
            },
        },
        {
            fieldName: 'kelaspelayanan_id',
            label: 'Kelas Pelayanan',
            type: {
                name: 'dropdownScroll',
                url: `${_baseUrl}/filters`,
                additionalPayload: {
                    type: 'kelas'
                }
            }
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
    filterRendered: (wrapper) => {
        var data = [
            {
                id: '',
                text: '- Semua -'
            }
        ]
        $(wrapper).find('[name="instalasi_id"]').bind('change', ({ currentTarget }) => {
            $.ajax({
                    url: `${_baseUrl}/filters`,
                    data: {
                        type: 'ruangan',
                        additionalPayload: {
                            instalasi_id: $(currentTarget).val(),
                        }
                    },
                    success: (res) => {
                        data = data.concat(res.data)
                        // data = res.data
                        $(wrapper).find('[name="list_ruangan_id"]').html('')
                        $(wrapper).find('[name="list_ruangan_id"]').select2({
                            data,
                        })
                    }
                })
        })
    }
  });

  $(".dataTables_filter").hide();
  $("#btn-search__example").css("display", "none");
  $("#btn-reset__example").css("display", "none");
  $(document).on(
    "switchChange.bootstrapSwitch",
    ".change-status",
    function (e) {
      var dataStatus = "0";
      var dataId = $(this).attr("data-id");
      if (e.target.checked == true) dataStatus = "1";
      $(this).docoForm("delete", {
        url:
          "/master/plafon-bpjs/change-status?id=" +
          dataId +
          "&status=" +
          dataStatus,
        confirmTitle: "<?=Yii::t('fe', 'Konfirmasi')?>",
        confirmMessage:
          "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
        success: function (data) {
          table.draw();
        },
        error: function (res) {
          table.draw();
        },
      });
    }
  );
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
    tableElement.ajax.reload();
  });
});
