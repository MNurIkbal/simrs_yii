function closePopup() {
  $("#modal_backdrop").modal("hide");
  $(".dataTables_filter").hide();
}
$(document).ready(function () {
  let isFormChanged = false;
  let loadUrl = `${_baseUrl}?id=${pendaftaranId}&pasienadmisi_id=${pasienAdmisiId}&pasien_id=${pasienId}&formasesmen_id=${formAsesmenId}`;
  if (asesmenMedisId) {
    loadUrl = loadUrl + "&asesmenmedis_id=" + asesmenMedisId;
  }
  $(".section-form-asesmen").prop("display", "none");
  $(".section-form-asesmen").docoLoad({
    url: loadUrl,
    dataType: "html",
    success: function (data) {
      $("#is_dokumen_eklaim")
        .prop("checked", isDokumenEklaim)
        .trigger("change");
      document.querySelectorAll("input, textarea, select").forEach((input) => {
        input.addEventListener("input", function () {
          isFormChanged = true;
        });
        input.addEventListener("change", function () {
          isFormChanged = true;
        });
      });
    },
  });
  $(".batal, .close").on("click", function (e) {
    let header = "Perhatian !";
    let messages =
      "Perubahan belum disimpan. Apakah Anda yakin ingin menutup ?";
    let label = {
      buttons: {
        Yes: "button-yes",
        No: "button-no",
      },
    };
    if (isFormChanged) {
      $.showQuestionDialog(header, messages, label, function (reaction) {
        if (reaction == "Yes") {
          closePopup();
        }
        if (reaction == "No") {
        }
      });
    } else {
      closePopup();
    }
  });
});
