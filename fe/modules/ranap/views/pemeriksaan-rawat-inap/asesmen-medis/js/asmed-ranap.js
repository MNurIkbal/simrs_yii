$("#form-asmed-ranap").on("submit", function (e) {
  var _form = $("#form-asmed-ranap").serializeArray();
  var isUnduhDokumen = 0;
  if (document.getElementById("is_dokumen_eklaim").checked) {
    isUnduhDokumen = 1;
  }

  _form.push({
    name: "is_dokumen_eklaim",
    value: JSON.stringify(isUnduhDokumen),
  });

  $(this).docoForm("submit", {
    data: _form,
    success: function (res) {
      $(`#modal_backdrop`).modal("hide");
      $('#tab-asmed-ranap a[href="#view-asmed-ranap"]').trigger("click");
    },
  });
});
