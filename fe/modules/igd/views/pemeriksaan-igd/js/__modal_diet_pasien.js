$(() => {
  $("#btn-save-diet-pasien").bind("click", () => {
    let _form = $("#diet-pasien-form").serializeArray();

    $().docoForm("click", {
      data: _form,
      skipScrollUp: false,
      url: $("#diet-pasien-form").attr("action"),
      success: function (data) {
        $("#modal-lab").find(".close").click();
      },
    });
  });
});
