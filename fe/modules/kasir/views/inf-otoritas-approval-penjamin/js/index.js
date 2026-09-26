let table;
let statusApproveOptions = [];
$(document).ready(function () {
  $.each(statusApprove, function (index, value) {
    statusApproveOptions.push({
      id: index,
      text: value,
    });
  });

  table = $("#example").docoTabel({
    filter: false,
    sorting: [[1, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    scrollY: true,
    ajax: {
      url: "/kasir/inf-otoritas-approval-penjamin/get-data",
    },
    stateSave: true,
    columns: [
      {
        data: "rowNum",
        name: "rowNum",
        searchable: false,
        orderable: false, // 0
      },
      {
        data: "tgl_pembayaran",
        name: "tgl_pembayaran", // 1
      },
      {
        data: "nama_pasien",
        name: "nama_pasien",
        searchable: false,
        orderable: false, // 2
      },
      {
        data: "no_pendaftaran",
        name: "no_pendaftaran",
        orderable: true, // 3
      },
      {
        data: "pegawai_kasir",
        name: "pegawai_kasir", // 4
      },
      {
        data: "limit_diskon",
        name: "limit_diskon",
        orderable: false, // 5
      },
      {
        data: "diskon",
        name: "diskon",
        searchable: false,
        orderable: false, // 6
      },
      {
        data: "nominal_diskon_display",
        name: "nominal_diskon_display",
        searchable: false,
        orderable: false, // 7
      },
      {
        data: "status",
        name: "status",
        searchable: false,
        orderable: false, // 8
      },
      {
        data: "pegawai_approve_nama",
        name: "pegawai_approve_nama",
        orderable: false, // 9
      },
      {
        data: "tgl_approve",
        name: "tgl_approve",
        orderable: false, // 10
      },
      {
        data: "action",
        name: "action",
        searchable: false,
        orderable: false, // 11
      },
    ],
    formFilters: [
      {
        fieldName: "tgl_pembayaran",
        label: "Tanggal Pembayaran",
        type: {
          name: "rangeDate",
        },
      },
      "no_pendaftaran",
      {
        fieldName: "nama_pasien",
        label: "Nama Pasien",
      },
      {
        fieldName: "status_approve",
        label: "Status Approve",
        type: {
          name: "select",
          payload: statusApproveOptions,
        },
      },
      {
        fieldName: "pegawai_kasir",
        label: "Nama Kasir",
      },
    ],
  });

  $(document).on("keyup", "#form-filter__example", function (e) {
    e.preventDefault();
    if (e.key == "Enter") {
      $("#btn-search__example").trigger("click");
    }
  });

  $(document).on("click", ".btn-reset", function (e) {
    const tableId = "example";
    const element = $(`#filter-section__${tableId}`);
    const formWrapper = $(`#form-filter__${tableId}`);
    element.find("input").val("");
    element.find("select").val(null).trigger("change");
    element
      .find("#tgl_pembayaran-startDate")
      .val(moment().locale("en").format("DD-MMM-YYYY"))
      .trigger("change");
    element
      .find("#tgl_pembayaran-endDate")
      .val(moment().locale("en").format("DD-MMM-YYYY"))
      .trigger("change");
    const tableElement = $(`#${tableId}`).DataTable();
    tableElement.context[0].ajax.data.advancedFilter =
      serializeArrayToJson(formWrapper);
    tableElement.ajax.reload();
  });
});

function ApprovalPenjamin(id) {
  $().docoForm("click", {
    data: {
      approval_id: id,
    },
    url: "/kasir/inf-otoritas-approval-penjamin/approval-penjamin",
    skipNotifyMessage: true,
    skipErrorNotif: true,
    type: "json",
    success: function () {
      table.ajax.reload(null, false);
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function RejectPenjamin(id) {
  $().docoForm("click", {
    data: {
      approval_id: id,
    },
    url: "/kasir/inf-otoritas-approval-penjamin/reject-penjamin",
    skipNotifyMessage: true,
    skipErrorNotif: true,
    type: "json",
    success: function () {
      table.ajax.reload(null, false);
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function DetailPenjamin(id) {
  $("#modal-detail").docoLoad({
    url: "/kasir/inf-otoritas-approval-penjamin/modal-detail",
    data: id,
    dataType: "html",
    success: function (data) {},
  });
}
