$(document).ready(function () {
  $("#provider_id").select2InfinityScroll({
    url: "/master/konfig-asuransi/filters",
    callbackData: (param) => {
      return {
        payload: {
          ...param,
        },
      };
    },
  });

  $(document).on("change", "#provider_id", function (e) {
    const provider = $(this).select2("data");
    const providerCode = provider[0].text;
    $("#provider_code").val(providerCode);
  });

  $(document).on("click", "#btn-save", function (e) {
    e.preventDefault();
    const _form = $("#konfig-asuransi-form").serializeArray();
    const _action = _id ? "update?id=" + _id : "create";

    $().docoForm("click", {
      url: `/master/konfig-asuransi/${_action}`,
      method: "POST",
      type: "json",
      data: _form,
      success: function () {
        window.location.href = "/master/konfig-asuransi/index";
      },
    });
  });

  $(document).on("click", "#cek-koneksi", function (e) {
    e.preventDefault();
    const _form = $("#konfig-asuransi-form").serializeArray();
    var header = "Perhatian !";
    var message = "Apakah anda yakin melakukan Cek Koneksi ?";
    var label = {
      buttons: {
        Yes: "button-yes",
        No: "button-no",
      },
    };

    $.showQuestionDialog(header, message, label, function (reaction) {
      if (reaction == "Yes") {
        $().docoForm("click", {
          url: `/master/konfig-asuransi/cek-koneksi?konfigasuransi_id=${_id}`,
          method: "POST",
          type: "json",
          data: _form,
          skipConfirm: true,
          skipSuccessNotif: true,
          success: function (res) {
            if (res.responseAuth.code != "undefined") {
              if (res.responseAuth.code == 200) {
                docoNotification(
                  "success",
                  "Success!",
                  res.responseAuth.message
                );
              } else {
                console.log("sss");
                docoNotification(
                  "error",
                  res.responseAuth.message,
                  res.message
                );
              }
            }
          },
          error: function (res) {
            if (res.status && res.status == 500) {
              docoNotification(
                "error",
                "Proses Gagal!",
                "Harap cek kembali inputan!"
              );
            }
          },
        });
      } else {
        hideQuestionDialog();
        $('[data-popup="tooltip"]').tooltip();
      }
    });
  });

  if (optionProvider) {
    var newOption = new Option(
      optionProvider.text,
      optionProvider.id,
      true,
      true
    );
    $("#provider_id").append(newOption).trigger("change");
  }
});
