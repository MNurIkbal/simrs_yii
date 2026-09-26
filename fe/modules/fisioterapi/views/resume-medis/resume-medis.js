function validateInput(input) {
  input.value = input.value.replace(/[^0-9/]/g, "");
  const parts = input.value.split("/");
  if (
    parts.length > 2 ||
    (parts[0] && parts[0].length > 3) ||
    (parts[1] && parts[1].length > 3)
  ) {
    input.value = input.value.slice(0, -1);
  }
}

$(document).ready(function () {
  $("#resumemedisfisioform-tgl_masuk").prop("readonly", true);
  $("#resumemedisfisioform-tgl_keluar").prop("readonly", true);
  $("#tekanan_darah").on("input", function () {
    validateInput(this);
  });
  $("#btn-save").on("click", function (e) {
    var _form = $("#resume-medis-form").serializeArray();
    $(this).docoForm("click", {
      url: `/fisioterapi/resume-medis/save`,
      method: "POST",
      data: _form,
      success: function (res) {
        $("#tab-resume-medis").trigger("click");
      },
    });
  });
  $("#btn-cetak-resume").on("click", function () {
    window.open(
      "/reports/viewer/resume-medis-fisio?pendaftaran_id=" + pendaftaran_id
    );
  });
});
