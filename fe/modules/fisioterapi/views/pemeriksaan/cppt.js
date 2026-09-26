var isEditCppt = false;
var tableCppt = null;

const { listPegawai, defaultUrlAction, isReadOnly, currentUser } = phpVars;

function generateTerapisOptions(exceptOption = null) {
  // Refresh List Pegawai
  $(`#terapis`).empty().trigger("change");
  let exceptId = null;
  let exceptText = null;
  if (exceptOption) {
    exceptId = exceptOption.id;
    exceptText = exceptOption.text;
  }
  for (const property in listPegawai) {
    let currentKey = property;
    let currentText = listPegawai[property];
    if (exceptId == property) continue;
    const terapisOptions = new Option(currentText, currentKey, false, false);
    $(`#terapis`).append(terapisOptions).trigger("change");
  }
}

function fillForm({
  tgl_soap,
  terapis,
  subject,
  object,
  planning,
  a_diag_utama,
  a_diag_penyerta,
  catatan_dokter,
  instruksi,
  diagnosa_fungsi,
  prosedur_kerja,
  goal
}) {
  $(":input", "#form-soap")
    .not(":button, :submit, :reset, :hidden")
    .val("")
    .prop("checked", false)
    .prop("selected", false);
  generateTerapisOptions(terapis);
  if (tgl_soap)
    tgl_soap = moment(new Date(tgl_soap)).format("DD/MM/YYYY HH:mm:ss");
  $("#soapform-tgl_soap").val(tgl_soap);
  var terapisOptions = new Option(terapis.text, terapis.id, false, false);
  $(`#terapis`).append(terapisOptions).val(terapis.id).trigger("change");
  $("#soapform-subject").val(subject).trigger("change");
  $("#soapform-object").val(object).trigger("change");
  $("#soapform-catatan_dokter").val(catatan_dokter).trigger("change");
  $("#soapform-instruksi").val(instruksi).trigger("change");
  $("#soapform-planning").val(planning).trigger("change");
  $("#soapform-a_diag_utama").val(null).trigger("change");
  $("#soapform-a_diag_penyerta").empty().val(null).trigger("change");
  // Set Diagnosa to select
  if (Object.keys(a_diag_utama).length) {
    var selectedDiagnosa = `${a_diag_utama.id}_${a_diag_utama.text}`;
    var newOption = new Option(
      a_diag_utama.text,
      selectedDiagnosa,
      false,
      false
    );
    $(`#soapform-a_diag_utama`)
      .append(newOption)
      .val(selectedDiagnosa)
      .trigger("change");
  }
  if (a_diag_penyerta && a_diag_penyerta.length) {
    var selectedOption = [];
    $.each(a_diag_penyerta, (index, diagnose) => {
      var newOption = new Option(
        diagnose.text,
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`,
        false,
        false
      );
      $(`#soapform-a_diag_penyerta`).append(newOption);
      selectedOption.push(
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`
      );
    });
    $(`#soapform-a_diag_penyerta`).val(selectedOption).trigger("change");
  }
  if (diagnosa_fungsi && diagnosa_fungsi.length) {
    var selectedOption = [];
    var _diagnosaFungsi = JSON.parse(diagnosa_fungsi)
    $.each(_diagnosaFungsi, (index, diagnose) => {
      var newOption = new Option(
        diagnose.text,
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`,
        false,
        false
      );
      $(`#soapform-diagnosa_fungsi`).append(newOption);
      selectedOption.push(
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`
      );
    });
    $(`#soapform-diagnosa_fungsi`).val(selectedOption).trigger("change");
  }
  if (prosedur_kerja && prosedur_kerja.length) {
    var selectedOption = [];
    var _tindakanProsedur = JSON.parse(prosedur_kerja)
    $.each(_tindakanProsedur, (index, diagnose) => {
      var newOption = new Option(
        diagnose.text,
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`,
        false,
        false
      );
      $(`#soapform-prosedur`).append(newOption);
      selectedOption.push(
        `${diagnose.id != undefined ? diagnose.id + "_" : ""}${diagnose.text}`
      );
    });
    $(`#soapform-prosedur`).val(selectedOption).trigger("change");
  }
  $("#soapform-goal").val(goal).trigger("change");
}

function clearForm() {
  generateTerapisOptions();
  $("#form-soap").attr(`action`, defaultUrlAction);
  $(":input", "#form-soap")
    .not(":button, :submit, :reset, :hidden")
    .val("")
    .prop("checked", false)
    .prop("selected", false);
  $("#soapform-tgl_soap").val(moment(new Date()).format("DD/MM/YYYY HH:mm:ss"));
  if (listPegawai[currentUser]) {
    $(`#terapis option[value=${currentUser}]`).attr("selected", "selected");
    $(`#terapis`).trigger("change");
  } else {
    $("#terapis").val("").trigger("change");
  }
  $("#soapform-subject").val("").trigger("change");
  $("#soapform-object").val("").trigger("change");
  $("#soapform-catatan_dokter").val("").trigger("change");
  $("#soapform-instruksi").val("").trigger("change");
  $("#soapform-planning").val("").trigger("change");
  $("#soapform-a_diag_utama").val(null).trigger("change");
  $("#soapform-a_diag_penyerta").empty().val(null).trigger("change");
  $("#soapform-diagnosa_fungsi").empty().val(null).trigger("change");
  $("#soapform-prosedur").empty().val(null).trigger("change");
}

function openForm() {
  if (!isReadOnly) {
    $("#form-soap :input").prop("disabled", false);
    $("#btn-save-soap").prop("disabled", false);
  } else {
    closeForm();
  }
}

function closeForm() {
  $("#form-soap :input").prop("disabled", true);
  $("#btn-save-soap").prop("disabled", true);
}

function initAfterDrawCppt() {
  // Reset Event
  $(`.edit-cppt`).unbind();
  $(`.batal-edit-cppt`).unbind();
  // New Event
  $(`.edit-cppt`).on(`click`, function () {
    isEditCppt = true;
    const soapfisioterapi_id = $(this).data(`soapfisioterapi_id`);
    const urlTarget = $(this).data(`urlTarget`);
    const indexNo = $(this).data(`index`);
    const instalasiId = $(this).data(`instalasi`);
    clearForm();
    openForm();
    // IMPROVE: add instalasi id for flaging soap
    $("#instalasi_id").val(instalasiId);
    $("#form-soap").attr(`action`, urlTarget);
    $(".edit-cppt.hidden").removeClass("hidden");
    $(".batal-edit-cppt:not(.hidden)").addClass("hidden");
    $(`.edit-cppt[data-index="${indexNo}"]`).addClass("hidden");
    $(`.batal-edit-cppt[data-index="${indexNo}"]`).removeClass("hidden");
    // Fill Data
    let dataTableCppt = tableCppt.data().toArray();
    let dataRowIndex = dataTableCppt.findIndex(
      (cpptData) => cpptData.soapfisioterapi_id == soapfisioterapi_id
    );
    if (dataRowIndex < 0) return null;
    const currentData = dataTableCppt[dataRowIndex];
    fillForm({
      tgl_soap: currentData.tgl_soapfisioterapi,
      terapis: {
        id: currentData.terapis_id,
        text: currentData.terapis_nama,
      },
      subject: currentData.subject,
      object: currentData.object,
      planning: currentData.planning,
      a_diag_utama: currentData.a_diag_utama,
      a_diag_penyerta: currentData.a_diag_penyerta,
      catatan_dokter: currentData.catatan_dokter,
      instruksi: currentData.instruksi,
      diagnosa_fungsi: currentData.diagnosa_fungsi,
      prosedur_kerja: currentData.prosedur_kerja,
      goal: currentData.goal,
    });
    bindSave();
  });
  $(`.batal-edit-cppt`).on(`click`, function () {
    isEditCppt = false;
    const urlTarget = $(this).data(`urlTarget`);
    const indexNo = $(this).data(`index`);
    clearForm();
    openForm();
    $("#form-soap").attr(`action`, urlTarget);
    $(`.batal-edit-cppt[data-index="${indexNo}"]`).addClass("hidden");
    $(`.edit-cppt[data-index="${indexNo}"]`).removeClass("hidden");
    // checkBinderSave();
  });
}

function bindSave() {
  unbindSave();
  $("#btn-save-soap").bind("click", function () {
    const url = $("#form-soap").prop("action");
    const data = $("#form-soap").serializeArray();
    $("#btn-save-soap").docoForm("click", {
      url: url,
      data: data,
      method: "POST",
      success: function (data) {
        clearForm();
        tableCppt.draw();
        // checkBinderSave();
      },
      error: function(res) {
        var respon = res.responseJSON;
        var res_title = respon.response?.title
        var res_message = respon.response?.message
        if(res_message && res_title) {
            docoNotification('error', res_title, res_message);
        }
      }
    });
  });
}

function unbindSave() {
  $("#btn-save-soap").unbind("click");
}

function checkBinderSave() {
  clearForm();
  closeForm();
  $.ajax({
    url:
      "/fisioterapi/pemeriksaan/is-create-soap?id=" +
      $("#pendaftaran_id").val(),
    method: "GET",
    success: function (res) {
      if (res.allowed == 1) {
        bindSave();
        openForm();
      } else {
        unbindSave();
        closeForm();
      }
    },
  });
}

function renderCppt() {
  let urlDatatable =
    baseUrl +
    `fisioterapi/pemeriksaan/get-data-cppt?pendaftaran_id=${phpVars.pendaftaranId}&program_terapi_id=${phpVars.programTerapiId}`;

  if (isReadOnly) urlDatatable = `${urlDatatable}&readonly=true`;

  tableCppt = $("#tb-cppt").docoTabel({
    filter: false,
    displayLength: 10,
    processing: true,
    serverSide: true,
    paging: false,
    info: false,
    aaSorting: [],
    ajax: urlDatatable,
    columns: [
      {
        title: "No",
        data: "rowNum",
        searchable: false,
        orderable: false,
      },
      {
        title: "Ruangan",
        data: "ruangan",
        searchable: false,
        orderable: false,
      },
      {
        title: "Hasil Asesmen Penatalaksanaan Pasien",
        data: "penatalaksanaan",
        searchable: false,
        orderable: false,
      },
      {
        title: "Instruksi",
        data: "instruksi",
        searchable: false,
        orderable: false,
      },
      {
        title: "Aksi",
        data: "action",
        searchable: false,
        orderable: false,
        width: "10%",
      },
    ],
    createdRow: (row, data, dataIndex, cells) => {
      const { is_edit } = data;
      if (is_edit == true) {
        for (var i = 0; i < row.childNodes.length; ++i) {
          if (i != row.childNodes.length - 1) {
            $(row.childNodes[i]).addClass("strike-text");
          }
        }
      }
    },
    drawCallback: function () {
      initAfterDrawCppt();
    },
  });
  $("#soapform-tgl_soap").val(moment(new Date()).format("DD/MM/YYYY HH:mm:ss"));
  // checkBinderSave();
  clearForm();
  openForm();
  bindSave();
}

$(document).ready(function () {
  setTimeout(() => {
    $("#tab-cppt").trigger("click");
  }, 100);

  $("#tab-cppt").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      pendaftaran_id: phpVars.pendaftaranId,
      program_terapi_id: phpVars.programTerapiId,
      program_terapi_detail_id: phpVars.programTerapiDetailId,
    }).toString();
    $("#content-cppt").docoLoad({
      url: "/fisioterapi/pemeriksaan/cppt?" + tabUrlParams,
      dataType: "html",
      success: function (data) {
        renderCppt();
      },
    });
  });
  $("#tab-upload-dokumen").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      id: phpVars.pendaftaranId,
    }).toString();
    $("#content-upload-dokumen").docoLoad({
      url: "/api/upload-dokumen/tab-upload-dokumen?" + tabUrlParams,
      dataType: "html",
      success: function (data) {},
    });
  });
  
  $("#tab-resume-medis").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      id: phpVars.pendaftaranId,
      pasien_id: phpVars.pasienId,
      program_terapi_ids: phpVars.programTerapiId,
    }).toString();
    
    $("#content-resume-medis").docoLoad({
      url: "/fisioterapi/resume-medis/index?" + tabUrlParams,
      dataType: "html",
      success: function (data) {},
    });
  });
  
  $("#tab-uji-fungsi-fisioterapi").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      id: phpVars.pendaftaranId,
      pasien_id: phpVars.pasienId,
      program_terapi_ids: phpVars.programTerapiId,
    }).toString();

    $("#content-uji-fungsi-fisioterapi").docoLoad({
      url: "/fisioterapi/uji-fungsi-fisioterapi/index?" + tabUrlParams,
      dataType: "html",
      success: function (data) {},
    });
  });
  
  $("#tab-formulir-rajal").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      id: phpVars.pendaftaranId,
      pasien_id: phpVars.pasienId,
      program_terapi_ids: phpVars.programTerapiId,
    }).toString();

    $("#content-formulir-rajal").docoLoad({
      url: "/fisioterapi/formulir-rajal/index?" + tabUrlParams,
      dataType: "html",
      success: function (data) {},
    });
  });

  $("#tab-rehabilitasi").on("click", function (e) {
    let tabUrlParams = new URLSearchParams({
      id: phpVars.pendaftaranId,
      pasien_id: phpVars.pasienId,
      program_terapi_ids: phpVars.programTerapiId,
    }).toString();

    $("#content-rehabilitasi").docoLoad({
      url: "/fisioterapi/rehabilitasi/index?" + tabUrlParams,
      dataType: "html",
      success: function (data) {},
    });
  });
});
