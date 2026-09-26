let table;
let _base_table_url = "/master/pasien-meninggal/get-data-pasien-meninggal";

$(document).ready(function () {
  table = $("#tablePasienMeninggal").docoTabel({
    filter: false,
    sorting: [[2, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    scrollY: true,
    ajax: {
      url: _base_table_url,
    },
    stateSave: true,
    columns: [
      {
        data: "nama_norm",
        name: "nama_norm",
      },
      {
        data: "no_pendaftaran",
        name: "no_pendaftaran",
      },
      {
        data: "tgl_pendaftaran",
        name: "tgl_pendaftaran",
      },
      {
        data: "tglpasienpulang",
        name: "tglpasienpulang",
      },
      {
        data: "status_aktif",
        name: "status_aktif",
      },
    ],
    formFilters: [
      {
        fieldName: "tgl_pendaftaran",
        label: "Tanggal Pendaftaran",
        type: {
          name: "rangeDate",
        },
      },
      {
        fieldName: "tglpasienpulang",
        label: "Tanggal Pulang",
        type: {
          name: "rangeDate",
        },
      },
      "no_rekam_medik",
      "no_pendaftaran",
      "nama_pasien",
      {
        fieldName: "status_aktif",
        label: "Status Aktifasi",
        type: {
          name: "select",
          payload: statusActivate,
        },
      },
    ],
  });

  $(".btn-reset").trigger("click");
  $(".hide-pasien-meninggal").bootstrapSwitch();
});

$(document).on("click", ".btn-reset", function (e) {
  localStorage.clear();
  const tableId = "tablePasienMeninggal";
  const element = $(`#filter-section__${tableId}`);
  const formWrapper = $(`#form-filter__${tableId}`);
  element.find("input").val("");
  element.find("select").val(null).trigger("change");
  const tableElement = $(`#${tableId}`).DataTable();
  tableElement.context[0].ajax.data.advancedFilter =
    serializeArrayToJson(formWrapper);
  tableElement.ajax.reload();
});

$(document).on("keyup", "#form-filter__tablePasienMeninggal", function (e) {
  e.preventDefault();
  if (e.key == "Enter") {
    $("#search-button").trigger("click");
  }
});

$(document).on(
  "switchChange.bootstrapSwitch",
  ".hide-pasien-meninggal",
  function (e, state) {
    $(this).attr("data-state", state);
    let that = $(this);
    let header = "Konfirmasi";
    let message = "Apa anda yakin ingin meerubah konfigurasi Cron ?";
    let label = {
      buttons: { Yes: "button-yes", No: "button-no" },
      hidden: true,
    };
    $.showQuestionDialog(header, message, label, function (reaction) {
      showReaction(reaction, that, function (str) {
        hideIt();
      });
    });
  }
);

$(document).on(
  "switchChange.bootstrapSwitch",
  ".change-status-pasien-meninggal",
  function (e, state) {
    $(this).attr("data-state", state);
    let that = $(this);
    let header = "Konfirmasi";
    let message = "Apa anda yakin ingin merubah Status Pasien ?";
    let label = {
      buttons: { Yes: "button-yes", No: "button-no" },
      hidden: true,
    };
    $.showQuestionDialog(header, message, label, function (reaction) {
      showReaction(reaction, that, function (str) {
        hideIt();
      });
    });
  }
);

function showReaction(str, that, callback) {
  let dataId = that.attr("data-id");
  let dataState = that.attr("data-state");
  let dataStatus = "1";
  if (dataState == "false") {
    dataStatus = "0";
  }
  //jika pilih No
  if (dataState == "false") {
    dataState = true;
  } else {
    dataState = false;
  }

  if (str == "Yes") {
    if (dataId === "hide-data-pasien-meninggal") {
      let payloadCron = {
        status: dataStatus,
        state: dataState
      }

      updateCron(payloadCron, that);
    } else {
      let payloadPasien = {
        pendaftaran_id: dataId,
        status: dataStatus,
        state: dataState,
      };

      updateStatus(payloadPasien, that);
    }
  } else {
    that.bootstrapSwitch("state", dataState);
  }
  callback(str);
}

function updateCron(payload, that) {
  $().docoForm("click", {
    url: "pasien-meninggal/change-status-cron",
    data: {
      status: payload.status,
    },
    type: "POST",
    skipConfirm: true,
    success: function (data) {
      console.log(data)
      // const message = data?.message == undefined ? data?.text : data?.message;
      // docoNotification("success", "Proses Berhasil !", message);
      hideIt();
    },
    error: function (data) {
      console.log(data);
      that.bootstrapSwitch("state", payload.state);
    },
  });
}

function updateStatus(payload, that) {
  $().docoForm("click", {
    url: "pasien-meninggal/change-status-pasien",
    data: payload,
    type: "POST",
    skipConfirm: true,
    success: function (data) {
      const message = data?.meta?.message == undefined ? 'Status Pasien Berhasil diubah !' : data?.meta?.message;
      docoNotification("success", "Proses Berhasil !", message);
      hideIt();
    },
    error: function (data) {
      that.bootstrapSwitch("state", payload.state);
    },
  });
}

function hideIt() {
  $("#confirm-dialog-overlay").remove();
  $("#confirm-dialog").remove();
  $("#confirm-dialog-overlay").remove();
  $("#confirm-dialog").remove();
}
