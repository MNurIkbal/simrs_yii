$(document).ready(function () {
  loadPageDiagnosa()
});


$(document).on("click", "#import_koding", function (event) {
  saveImportkoding()
  setTimeout(() => {
    $.ajax({
      type: "POST",
      url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/import-koding",
      data: {
        kunjungan_id: _infoPasien.kunjungan_id
      },
      success: function (response) {
        $('#load_diagnosa').empty()
        loadPageDiagnosa()
      },
      error: function (jqXhr) {
        docoNotification("error","Proses Gagal !", jqXhr.responseText);
      }
    });
  }, 500)
});

  
function loadPageDiagnosa() {
  $('#load_diagnosa').append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
  $("#load_diagnosa").load(`/penjamin-asuransi/informasi-pasien-ranap-bpjs/load-diagnosa?id=${_infoPasien.kunjungan_id}`, (e) => {
    // Trigger pertama kali click halaman.
    $("#tab-unu").trigger("click");

    if(isEditKoreksi) {
      $('#form-koreksi-diagnosa').attr('action', linkEdit)
    }

    if(disableEdit) {
      $('.koreksi-diagnosa').attr('disabled', true);
      $('.btn-xsm').attr('disabled', true);
      $('.check-inacbg').attr('disabled', true);
      $('.radio-icdprimer').attr('disabled', true);
    }

    getDiagnosa()
  });
}  

$(document).on("click", "#add-diagnosa-2", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-2");
  let tr = ".tr-diagnosa-2";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-2", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-2");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-3", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-3");
  let tr = ".tr-diagnosa-3";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-3", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-3");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-4", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-4");
  let tr = ".tr-diagnosa-4";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-4", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-4");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-5", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-5");
  let tr = ".tr-diagnosa-5";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-5", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-5");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-6", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-6");
  let tr = ".tr-diagnosa-6";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-6", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-6");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

// START INAGROUPER ==============================================

$(document).on("click", "#add-diagnosa-ina-2", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-2");
  let tr = ".tr-diagnosa-ina-2";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-ina-2", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-2");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-ina-3", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-3");
  let tr = ".tr-diagnosa-ina-3";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-ina-3", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-3");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-ina-4", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-4");
  let tr = ".tr-diagnosa-ina-4";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-ina-4", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-4");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-ina-5", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-5");
  let tr = ".tr-diagnosa-ina-5";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-ina-5", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-5");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

$(document).on("click", "#add-diagnosa-ina-6", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-6");
  let tr = ".tr-diagnosa-ina-6";
  newDiagnosa(diagnosa, tr);
  getDiagnosa();
});

$(document).on("click", ".remove-diagnosa-ina-6", function (event) {
  event.preventDefault();
  let diagnosa = $(".diagnosa-ina-6");
  let parent = $(this).closest("tr");
  removeDiagnosa(diagnosa, parent);
});

function newDiagnosa(id, tr) {
  let lastTr = $("" + tr + "").last();
  let classTr = tr.slice(1);
  let kelompok = tr.slice(-1);
  let icd = "ICD IX";
  let typeIna = id.attr("data-ina")
  var el = '';

  if (kelompok == 3) {
    icd = "ICD X";
    el = "";
  }
  let rowSpan = parseInt(id.attr("rowspan")) + 1;
  let nameInput = typeIna != undefined ? `koreksi_diagnosa_ina[]` : `koreksi_diagnosa[]`
  let removeDiagnosa = typeIna != undefined ? `remove-diagnosa-ina-${kelompok}` : `remove-diagnosa-${kelompok}`
  let attrDataIna = typeIna != undefined ? `data-ina='1'` : ''
  let newRow =
    '<tr class="' + classTr + '"><td style="width:20%"> </<td><td style="width:30%"><select class="koreksi-diagnosa" name="'+nameInput+'" data-type="' + icd + '" data-kelompok="' +kelompok + '" data-icd="" data-asal="" '+attrDataIna+' ></select></td><td style="width:1%"><button type="button" class="btn btn-danger btn-xsm '+ removeDiagnosa +'"><i class="fa fa-trash"></i></button></td><td class="hidden" width="1%"><input type="checkbox" class="check-inacbg validate-update" name="FormKoreksi[is_inacbg]" disabled></td><td class="hidden" width="1%">' + el +
    "</td></tr>";
  id.attr("rowspan", rowSpan);
  lastTr.after(newRow);
}

function removeDiagnosa(id, parent) {
  let rowSpan = parseInt(id.attr("rowspan")) - 1;
  id.attr("rowspan", rowSpan);
  parent.remove();
}

var tempval = "";

function getDiagnosa() {
  $(".koreksi-diagnosa")
    .select2({
      placeholder: "Pilih Diagnosa",
      minimumInputLength: 3,
      ajax: {
        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-icd",
        dataType: "json",
        quietMillis: 250,
        data: function (params) {
          var notIn = [];
          var notInIna = [];
          var typeIna = $(this).attr("data-ina");
          $(".koreksi-diagnosa").each(function (index) {
            var val = $(this).val();
            var attributIna = $(this).attr("data-ina");
            if(attributIna != undefined) {
              if (val) {
                notInIna.push(val);
              }
            } else {
              if (val) {
                notIn.push(val);
              }
            }
          });
          params.type_icd = $(this).attr("data-type");
          params.type_ina = typeIna
          params.not_in = typeIna != undefined ? notInIna : notIn;
          var query = {
            search: params,
          };
          return params;
        },
        processResults: function (data) {
          return {
            results: data.result,
          };
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) {
          return m;
        },
      },
    })
    .on("select2:opening", function (event) {
      tempval = $(this).val();
    })
    .on("select2:selecting", function (event) {
      const typeIna = $(this).attr("data-ina");
      if(typeIna != undefined) {
        const data = event.params?.args?.data; 
        const validCode = data?.validcode
        if(validCode == 0) {
          alert('Diagnosa tidak cocok')
          return false;
        }
        return true;
      }
    })
    .on("select2:select", function (event) {
      var val = $(this).val();
      if (tempval !== val) {
        updateStatus = true;
        var hasClass = $(this).closest("tr").hasClass("have-update");

        if (!hasClass) {
          $(this).closest("tr").addClass("have-update");
        }
      }

      var checkBox = $(this).closest("tr").find(".check-inacbg");
      var radio = $(this).closest("tr").find(".radio-icdprimer");
      var text = $(this).select2("data")[0].text;

      if (val) {
        checkBox.attr("disabled", false);
        checkBox.attr("name", "FormKoreksi[is_inacbg][" + val + "]");
        checkBox.prop("checked", true);
        radio.attr("disabled", false);
        radio.val(val);
        var radioValue = $(
          "input[name='FormKoreksi[is_icdprimer]']:checked"
        ).val();

        var radioValueIna = $(
          "input[name='FormKoreksi[is_icdprimer_ina]']:checked"
        ).val();

        if (typeof radioValue == "undefined") {
          radio.prop("checked", true);
        } else if (typeof radioValueIna == "undefined") {
          radio.prop("checked", true);
        }
      } else {
        checkBox.attr("disabled", true);
        checkBox.prop("checked", false);
        radio.prop("checked", false);
      }
    })
    .on("select2:closing", function (event) {});
}

$(document).on("click", ".confirm-update", function (event) {
  var target = $(this).data("target");
  var message =
    "Perubahan data diagnosa pasien belum di koreksi, apakah anda akan tetap melanjutkan proses tanpa menyimpan dan mengkoreksi perubahan diagnosa ?";

  if (updateStatus) {
    $.showQuestionDialog(
      "Perhatian !",
      message,
      {
        buttons: {
          Yes: "button-yes",
          No: "button-no",
        },
      },
      function (reaction) {
        if (reaction == "Yes") {
          if (target == "back") {
            history.go(-1);
          } else if (target == "reset") {
            location.reload();
          } else if (target == "eklaim") {
            goToEklaim();
          }
        }
      }
    );
  } else {
    if (target == "back") {
      history.go(-1);
    } else if (target == "reset") {
      location.reload();
    } else if (target == "eklaim") {
      goToEklaim();
    }
  }
});

$(document).on("change", ".validate-update", function () {
  updateStatus = true;
  var hasClass = $(this).closest("tr").hasClass("have-update");

  if (!hasClass) {
    $(this).closest("tr").addClass("have-update");
  }
});

$(document).on("click", ".diagnosa-reset", function () {
  var checkBox = $(this)
    .closest("tr")
    .find(".check-inacbg")
    .prop("checked", false);
  var radio = $(this)
    .closest("tr")
    .find(".radio-icdprimer")
    .prop("checked", false);
  var koreksiDiagnosa = $(this).closest("tr").find(".koreksi-diagnosa");
  koreksiDiagnosa.val("").trigger("change");
});

function goToEklaim() {
  $().docoForm("click", {
    skipConfirm: true,
    skipSuccessNotif: true,
    data: {},
    url:
      "/penjamin-asuransi/informasi-pasien-ranap-bpjs/proses-eklaim?id=" + _id,
    success: function (data) {
      // setTimeout(function () {
      $(location).attr(
        "href",
        "/penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=" +
          data.id +
          "&updated=" +
          data.updated
      );
      // }, 1300);
    },
  });
}

$(document).on("click", "#btn-koreksi", function (event) {
  event.preventDefault();

  saveKoding(false)
});

function saveImportkoding() {
  saveKoding(true)
}

function saveKoding(skipKonfirmasi) {
  toggleDisable(false);
  var _data = $("#form-koreksi-diagnosa").serializeArray();
  var _dokterNama = _info?.dokter_nama;
  // toggleDisable(true);
  var pasien_id = _info?.pasien_id;
  if (pasien_id == null) {
    pasien_id = _infoPasien.pasien_id;
  }

  if (_kunjunganId == null) {
    _kunjunganId = _infoPasien.kunjungan_id;
  }

  if (_dokterNama == null) {
    _dokterNama = _infoPasien.dokter_nama;
  }

  _data.push({
    name: "dokter_nama",
    value: _dokterNama,
  });

  _data.push({
    name: "disable_edit",
    value: disableEdit ? true : false,
  });

  _data.push({
    name: "kunjungan_id",
    value: _kunjunganId,
  });
  var _tmp = {};
  $.each($(".koreksi-diagnosa"), function (key, val) {
    _tmp[key] = {
      diagnosa_asal: $(this).attr("data-icd"),
      diagnosa_id: $(this).val(),
      diagnosa_text: $(this).attr("data-asal"),
      diagnosa_kelompok: $(this).attr("data-kelompok"),
      diagnosa_inacgrouper: $(this).attr("data-ina"),
    };
  });
  _data.push({
    name: "data_koreksi",
    value: JSON.stringify(_tmp),
  });
  _data.push({
    name: "total_data",
    value: $(".koreksi-diagnosa").length,
  });
  _data.push({
    name: "dokterdpjp_id",
    value: _infoPasien.dokter_kode,
  });
  _data.push({
    name: "pasien_id",
    value: pasien_id,
  });

  $().docoForm("click", {
    skipConfirm: skipKonfirmasi,
    data: _data,
    url: $("#form-koreksi-diagnosa").attr("action"),
    success: function (res) {
      if(! skipKonfirmasi) {
        if (res.metadata.status == 200) {
          var data = res.response.data;
          setTimeout(function () {
            $(location).attr(
              "href",
              "/penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=" +
                data.id +
                "&admisi=" +
                data.admisi +
                "&updated=" +
                data.updated
            );
          }, 1300);
        }
      }
    },
    error: function (error) {
      let errorText = error?.responseJSON?.metadata?.message;
      let errorIncbgs = error?.responseJSON?.metadata?.status;
      if (errorIncbgs != undefined && errorIncbgs.length != 0) {
        new PNotify({
          title: "Perhatian",
          text: errorText,
          addclass: "alert alert-danger alert-arrow-right alert-styled-right",
          type: "error",
          buttons: {
            closer: true,
            sticker: true,
          },
          hide: true,
          history: {
            history: false,
          },
        });
      }
    },
  });

  function toggleDisable(status) {
    $(".koreksi-diagnosa").attr("disabled", status);
    $(".btn-xsm").attr("disabled", status);
    $(".check-inacbg").attr("disabled", status);
    $(".radio-icdprimer").attr("disabled", status);
  }

  $(".panel-expandable").on("click", function (e) {
    $(this).next().slideToggle();
    if ($(this).find(".rotate-180").length !== 0) {
      $(this).find(".icons-list li a").removeClass("rotate-180");
    } else {
      $(this).find(".icons-list li a").addClass("rotate-180");
    }
    if ($(this).next().filter(".info-pasien").length !== 0) {
      $(".panel-info").toggle();
    }
  });
}
