var asesmenData = data.asesmenkeperawatan;
$("[name='AsesmenKeperawatanForm[pernafasan]']").dependentMultipleHandler({
  formHandler: [
    {
      value: "spontan",
      id: "pernafasan_spontan--form",
    },
    {
      value: "takipnea",
      id: "pernafasan_takipnea--form",
    },
    {
      value: "gargling",
      id: "pernafasan_gargling--form",
    },
  ],
});
// $(
//   "[name='AsesmenKeperawatanForm[survey_kepala]'][type='checkbox']"
// ).dependentMultipleHandler({
//   formHandler: [
//     {
//       value: "kepala",
//       class: "kepala--dependent",
//     },
//     {
//       value: "lacerasi",
//       id: "survey_kepala_lacerasi--form",
//     },
//     {
//       value: "battle_sign",
//       id: "survey_kepala_battle_sign--form",
//     },
//     {
//       value: "lainnya",
//       id: "survey_kepala_lainnya--form",
//     },
//   ],
// });
// $(
//   "[name='AsesmenKeperawatanForm[survey_mulut]'][type='checkbox']"
// ).dependentMultipleHandler({
//   formHandler: [
//     {
//       value: "luka_dalam",
//       class: "luka_dalam--dependent",
//     },
//     {
//       value: "lainnya",
//       id: "survey_mulut_lainnya--form",
//     },
//   ],
// });
// $(
//   "[name='AsesmenKeperawatanForm[survey_extremitas]'][type='checkbox']"
// ).dependentMultipleHandler({
//   formHandler: [
//     {
//       value: "pulsasi",
//       class: "survey_extremitas_pulsasi--dependent",
//     },
//   ],
// });
// $(
//   "[name='AsesmenKeperawatanForm[survey_dada]'][type='checkbox']"
// ).dependentMultipleHandler({
//   formHandler: [
//     {
//       value: "dada",
//       class: "survey_dada--dependent",
//     },
//     {
//       value: "dada1",
//       class: "survey_dada1--dependent",
//     },
//     {
//       value: "nyeri_dada",
//       class: "survey_nyeri_dada--dependent",
//     },
//     {
//       value: "bunyi_jantung",
//       class: "survey_dada_bunyi_jantung--dependent",
//     },
//   ],
// });
// $(
//   "[name='AsesmenKeperawatanForm[survey_abdomen]'][type='checkbox']"
// ).dependentMultipleHandler({
//   formHandler: [
//     {
//       value: "memas",
//       class: "survey_memas--dependent",
//     },
//     {
//       value: "nyeri",
//       class: "survey_abdomen_nyeri--dependent",
//     },
//     {
//       value: "lainnya",
//       class: "survey_abdomen_lainnya--dependent",
//     },
//     {
//       value: "bising_usus",
//       class: "survey_abdomen_bising_usus--dependent",
//     },
//   ],
// });
$(
  "[name='AsesmenKeperawatanForm[gangguan_thermoregulasi_tipe]'][type='radio']"
).bind("change", ({ currentTarget }) => {
  $("#gangguan_thermoregulasi_nilai--form").prop(
    "disabled",
    !$(currentTarget).is(":checked")
  );
});

// $("[name='AsesmenKeperawatanForm[nilai_luka_bakar]']").bind(
//   "change",
//   ({ currentTarget }) => {
//     $("#persen_luka_bakar--form").prop(
//       "disabled",
//       !$(currentTarget).is(":checked")
//     );
//   }
// );

// $("[name='AsesmenKeperawatanForm[persen_luka_bakar]']").bind(
//   "change",
//   ({ currentTarget }) => {
//     if (parseInt($(currentTarget).val().replace(/\./g, "")) > 100) {
//       $(currentTarget).val(100);
//     }
//   }
// );

$(() => {

  /*----------  Berat Badan dan IMT Start  ----------*/
    $(".imt_field").keyup(function(e){
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        let tb = $('#asesmenkeperawatanform-tinggi_badan').val() ? parseFloat($('#asesmenkeperawatanform-tinggi_badan').val().replace(',', '.')) : null;
        let bb = $('#asesmenkeperawatanform-berat_badan').val() ? parseFloat($('#asesmenkeperawatanform-berat_badan').val().replace(',', '.')) : null;
        let kategori_imt = $("#asesmenkeperawatanform-ket_imt");
        let bodymassindex_id = $("#asesmenkeperawatanform-bodymassindex_id");
        let field_imt = $("#asesmenkeperawatanform-imt");
        let field_bbideal = $("#asesmenkeperawatanform-bb_ideal");
        let bbideal = 0.0;
        let imt = 0.0;
        let imt_kategori = '';
        let bodymassindex = '';

        // hitung bmi / imt
        if (bb && tb) {
            imt = (bb / ((tb/100) * (tb/100))).toFixed(2);

            $.each(data_bmi, function( index, value ) {
                /*if ((imt >= value['bmi_minimum']) && (imt <= value['bmi_maksimum'])){
                    imt_kategori = value['bmi_defenisi'];
                    bodymassindex = value['bodymassindex_id'];
                    console.log(value);
                    return false; //break
                }*/
                if(parseFloat(imt) >= parseFloat(value.bmi_minimum) && parseFloat(imt) <= parseFloat(value.bmi_maksimum)){
                    imt_kategori = value.bmi_defenisi;
                    bodymassindex = value.bodymassindex_id;
                    return false;
                }
            });

            // hitung berat badan ideal
            if (jeniskelamin == 15){
                bbideal = parseFloat((tb - 100) - (0.1 * (tb-100))).toFixed(2);
            }else{
                bbideal = parseFloat((tb - 100) - (0.15 * (tb-100))).toFixed(2);
            }
        }

        field_imt.val(imt.toString().replace('.', ','));
        kategori_imt.val(imt_kategori);
        bodymassindex_id.val(bodymassindex);
        field_bbideal.val(bbideal.toString().replace('.', ','));
    });
    /*----------  Berat Badan dan IMT end  ----------*/

  let defaultValueAssigned = [];
  $(".nutrisi-check").trigger("change");
  $(".strongkids-check").trigger("change");
  $(".phonenumber").on("keyup", ({ currentTarget }) => {
    $(currentTarget).val(
      $(currentTarget)
        .val()
        .replace(/[^0-9.]/g, "")
    );
  });
  $(".input-tag").tagsinput();
  $(".default-disabled").prop("disabled", true);
  $("[type='radio'],[type='checkbox']").not(".resiko-jatuh-form").uniform({
    radioClass: "choice",
  });

  $(".box-scale-line__btn").bind("click", ({ currentTarget }) => {
    const parentBoxScale = $(currentTarget).parents(".box-scale");
    parentBoxScale
      .find(".box-scale-line__point")
      .removeClass("box-scale-line--selected");
    $(currentTarget).parent().addClass("box-scale-line--selected");
    const dataPercentage = $(currentTarget).parent().data("percentage");
    parentBoxScale
      .find(".box-scale-line__hidePercentage")
      .css("width", `${100 - parseInt(dataPercentage)}%`);

    const dataInfo = $(currentTarget).parent().data("info");
    $(currentTarget).closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
    $('.'+dataInfo).css("background", $(currentTarget).css("background-color"));
    $('#asesmenkeperawatanresikojatuh-skala_nyeri').val(null);

    const valueScale = $(currentTarget).html();
    $('#asesmenkeperawatanresikojatuh-skala_nyeri').val(valueScale+"-"+$('.'+dataInfo).html());
    // $('#asesmenkeperawatanresikojatuh-skala_nyeri').val(valueScale);
  });

  $("input[type='checkbox'],input[type='radio']").bind(
    "change",
    ({ currentTarget }) => {
      const element = $(currentTarget);
      const inputName = element.prop("name");
      const otherElement = $(`input[name='${inputName}'][value='00']`);
      if (otherElement.length > 0) {
        $(`#other-${otherElement.data("fieldname")}`).prop(
          "disabled",
          !otherElement.is(":checked")
        );
        if (!otherElement.is(":checked")) {
          $(`#other-${otherElement.data("fieldname")}`).val("");
          if (
            $(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput").length > 0
          ) {
            $(`#other-${otherElement.data("fieldname")}`).tagsinput(
              "removeAll"
            );
          }
        } else {
          if (
            $(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput").length > 0
          ) {
            $(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput input")
              .prop("disabled", false);
          }
        }
      }
    }
  );
  $("[data-dependent]").bind("change", ({ currentTarget }) => {
    dependentHandler(currentTarget, ({ otherElement, elementStringChild }) => {
      $(elementStringChild).prop("disabled", !otherElement.is(":checked"));
      if (!otherElement.is(":checked")) {
        if (
          $(`${elementStringChild}[type="text"]`)
            .parent()
            .find(".bootstrap-tagsinput").length > 0
        ) {
          $(`${elementStringChild}[type="text"]`).tagsinput("removeAll");
        }
        $(`${elementStringChild}[type="text"]`).val("");
        $(
          `${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`
        ).prop("checked", false);
        $(
          `${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`
        ).trigger("change");
      }

      // if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
      //     $(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput input').prop('disabled', otherElement.is(':checked'))
      // }
      $.uniform.update();
    });
  });
  $(".btn-triage button").bind("click", ({ currentTarget }) => {
    $(currentTarget).addClass("btn-triage--active");
    $(currentTarget)
      .parent()
      .find("button")
      .not(currentTarget)
      .removeClass("btn-triage--active");
  });
  $("#agamaForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.agama),
  });
  $("#pendidikanForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.pendidikan),
  });
  $("#pekerjaanForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.pekerjaan),
  });
  $("#caraMasukForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.caramasuk),
  });
  $("#sukuForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.suku),
  });
  $("#gcseyeForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.metodegcs.E),
  });
  $("#gcsverbalForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.metodegcs.V),
  });
  $("#gcsmotorikForm").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data.metodegcs.M),
  });
  $("#gcseyeForm,#gcsverbalForm,#gcsmotorikForm").bind("change", () => {
    const totalScoreEye =
      typeof $("#gcseyeForm").select2("data")[0].metodegcs_nilai != "undefined"
        ? $("#gcseyeForm").select2("data")[0].metodegcs_nilai
        : 0;
    const totalScoreVerbal =
      typeof $("#gcsverbalForm").select2("data")[0].metodegcs_nilai !=
      "undefined"
        ? $("#gcsverbalForm").select2("data")[0].metodegcs_nilai
        : 0;
    const totalScoreMotorik =
      typeof $("#gcsmotorikForm").select2("data")[0].metodegcs_nilai !=
      "undefined"
        ? $("#gcsmotorikForm").select2("data")[0].metodegcs_nilai
        : 0;
    const totalScoreGcs = totalScoreEye + totalScoreVerbal + totalScoreMotorik;
    let resultGcs = "-";
    data.gcs.map((item) => {
      if (
        totalScoreGcs >= item.gcs_nilaimin &&
        totalScoreGcs <= item.gcs_nilaimax
      ) {
        resultGcs = `${item.text}`;
      }
    });
    $("input[name='keterangan_gcs']").val(resultGcs);
    $("#asesmenkeperawatanform-hasil_gcs").val(totalScoreGcs);
  });

  // $("[name='AsesmenKeperawatanForm[is_nyeri]']").bind(
  //   "change",
  //   ({ currentTarget }) => {
  //     if ($(currentTarget).val() == "1") {
  //       $("#nyeri-wrapper").show();
  //     } else {
  //       $("#nyeri-wrapper").hide();
  //     }
  //   }
  // );

  if ($(`input[name="AsesmenKeperawatanForm[is_nyeri]"]:checked`).val() == '1') {
    $("#nyeri-wrapper").show()
      $("#pilih-wrapper").show()
  } else {
      $("#nyeri-wrapper").hide()
      $("#pilih-wrapper").hide()
  }

  $("[name='AsesmenKeperawatanForm[is_nyeri]']").bind('change', ({ currentTarget }) => {
      if ($(currentTarget).val() == '1') {
          $("#nyeri-wrapper").show()
          $("#pilih-wrapper").show()
      } else {
          $("#nyeri-wrapper").hide()
          $("#pilih-wrapper").hide()
      }
  })

  if ($(`input[name="AsesmenKeperawatanForm[pilih_skala]"]:checked`).val() == 'dewasa') {
      $("#dewasa-wrapper").show()
      $("#anak-wrapper").hide()
  } else if ($(`input[name="AsesmenKeperawatanForm[pilih_skala]"]:checked`).val() == 'anak') {
      $("#dewasa-wrapper").hide()
      $("#anak-wrapper").show()
  } else {
      $("#dewasa-wrapper").hide()
      $("#anak-wrapper").hide()
  }

  $("[name='AsesmenKeperawatanForm[pilih_skala]']").bind('change', ({ currentTarget }) => {
      if ($(currentTarget).val() == 'dewasa') {
          $("#dewasa-wrapper").show()
          $("#anak-wrapper").hide()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      } else if ($(currentTarget).val() == 'anak') {
          $("#dewasa-wrapper").hide()
          $("#anak-wrapper").show()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      } else {
          $("#dewasa-wrapper").hide()
          $("#anak-wrapper").hide()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      }
  })

  $("[name='AsesmenKeperawatanForm[detak_nadi]']").bind(
    "keyup",
    ({ currentTarget }) => {
      hitungNadi = $(currentTarget).val().replaceAll(".", "");
      if (hitungNadi <= 60) {
        $("#asesmenkeperawatanform-hasil_nadi").val("Bradikardia");
      } else if (hitungNadi > 60 && hitungNadi <= 100) {
        $("#asesmenkeperawatanform-hasil_nadi").val("Normal");
      } else if (hitungNadi > 100) {
        $("#asesmenkeperawatanform-hasil_nadi").val("Takikardia");
      }
    }
  );

  // dependent airway breathhing
  $("[name='AsesmenKeperawatanForm[jalur_nafas]']").bind(
    "change",
    ({ currentTarget }) => {
      if ($(currentTarget).val() == "bersih_sumbatan") {
        if ($(currentTarget).prop("checked") == true) {
          $(".bersih_sumbatan--dependent").prop("disabled", false);
        } else {
          $(".bersih_sumbatan--dependent").prop("checked", false);
          $(".bersih_sumbatan--dependent").prop("disabled", true);
        }
      } else if ($(currentTarget).val() == "oksigen") {
        if($(currentTarget).prop("checked") == true) {
          $("#jalur_nafas_oksigen--form").prop("disabled", false);
        } else {
          $("#jalur_nafas_oksigen--form").prop("disabled", true);
          $("#jalur_nafas_oksigen--form").val("");
        }
      }
      $.uniform.update();
    }
  );
  $("#btn-save-asesmen-perawat").bind("click", () => {
    // reset validation message
    $('div.help-block.error').html('');
    $('div.help-block.error').removeClass('error');
    $('.has-error').removeClass('has-error');

    // Check skrining gizi
    let errorMessage = ''
    const checkGizi = $(".nutrisi-check:checked").length
    const checkStrongKids = $(".strongkids-check:checked").length
    if (checkGizi == 0 && checkStrongKids == 0) {
        errorMessage = 'Skrining Gizi harus diisi!'
    }
    if (errorMessage != '') {
        docoNotification('warning', 'Silakan cek kembali form', errorMessage)
        return false
    }
    $("[type='hidden']").prop("disabled", true);
    $(
      "#asesmenkeperawatanform-hasil_gcs,#asesmenkeperawatanform-hasil_resiko_jatuh"
    ).prop("disabled", false);
    let job = $("#pekerjaanForm").select2("data");
    let serializeArray = $("#form-asesmen-keperawatan").serializeArray();
    let payload = {
      pendaftaran_id:
        typeof asesmenData.pendaftaran_id != "undefined"
          ? asesmenData.pendaftaran_id
          : null,
      is_kapitis: "0",
      resiko_jatuh: {},
      pekerjaan_nama: typeof job[0] != "undefined" ? (job[0].id ? job[0].text : "-") : "-",
      skor_gizi: parseInt($('#score-section').text())
    };
    let typeElement = "";
    let nodeElement = "";
    let payloadKey = "";
    serializeArray.map((item) => {
      typeElement = $(`input[name='${item.name}']`)
        .not("[type='hidden']")
        .prop("type");
      nodeElement =
        $(`[name='${item.name}']`).not("[type='hidden']").length > 0
          ? $(`[name='${item.name}']`)
              .not("[type='hidden']")
              .prop("tagName")
              .toLowerCase()
          : "";
      payloadKey = originFieldName(item.name);
      if (
        typeElement == "radio" ||
        typeElement == "text" ||
        nodeElement == "textarea" ||
        typeElement == "checkbox"
      ) {
        if (typeof item.value != "undefined") {
          if (typeof payload[payloadKey] != "undefined") {
            payload[payloadKey] += `${payload[payloadKey] != "" ? "," : ""}${
              item.value
            }`;
          } else {
            payload[payloadKey] = item.value;
          }
        } else {
          payload[payloadKey] = "";
        }
      } else if (nodeElement == "select") {
        if (item.name == "AsesmenKeperawatanForm[diagnosa_keperawatan][]") {
          if (
            $("#asesmenkeperawatanform-diagnosa_keperawatan").val().length
          ) {
            payload["diagnosa_keperawatan"] = $(
              "#asesmenkeperawatanform-diagnosa_keperawatan"
            ).val();
          }
        } else {
          payload[payloadKey] = $(`[name='${item.name}']`)
            .val()
            .replace(/-- Pilih --/g, "");
        }
      }

      if (payloadKey.match(/AsesmenKeperawatanResikoJatuh/g) != null) {
        payload.resiko_jatuh[
          payloadKey.replace("AsesmenKeperawatanResikoJatuh[", "")
        ] = payload[payloadKey];
        delete payload[payloadKey];
      }
    });

    if ($("[data-type='skala_nyeri'] .box-scale-line--selected").length > 0) {
      payload["skala_nyeri"] =
        parseInt(
          $("[data-type='skala_nyeri'] .box-scale-line--selected").data(
            "percentage"
          )
        ) / 10;
    } else {
      payload["skala_nyeri"] = 0;
    }
    if (
      $("[data-type='skala_nyeri_anak'] .box-scale-line--selected").length > 0
    ) {
      payload["skala_nyeri_anak"] =
        parseInt(
          $("[data-type='skala_nyeri_anak'] .box-scale-line--selected").data(
            "percentage"
          )
        ) / 10;
    } else {
      payload["skala_nyeri_anak"] = 0;
    }
    if ($(".triage-sehari .btn-triage--active").length > 0) {
      payload["kategori_triase_sehari"] = $(
        ".triage-sehari .btn-triage--active"
      ).data("value");
    }
    if ($(".triage-disaster .btn-triage--active").length > 0) {
      payload["kategori_triase_disaster"] = $(
        ".triage-disaster .btn-triage--active"
      ).data("value");
    }
    payload["hasil_resiko_jatuh"] = $(
      "#asesmenkeperawatanform-hasil_resiko_jatuh"
    ).val();
    $("[type='hidden']").prop("disabled", false);
    $(
      "#asesmenkeperawatanform-hasil_gcs,#asesmenkeperawatanform-hasil_resiko_jatuh"
    ).prop("disabled", true);
    if ($(".btn-resiko-jatuh-section .btn-group-section--active").length > 0) {
      payload["jenis_resiko_jatuh"] = $(
        ".btn-resiko-jatuh-section .btn-group-section--active"
      ).data("type");
    }
    $("input[disabled]")
      .not('.resiko-jatuh-form,.textbox-resiko-jatuh-form,[type="hidden"]')
      .each((indexFormDisabled, formDisabled) => {
        if (
          typeof payload[originFieldName($(formDisabled).prop("name"))] ==
          "undefined"
        ) {
          payload[originFieldName($(formDisabled).prop("name"))] = "";
        }
      });
    showLoader();
    $.ajax({
      url: `/ranap/pemeriksaan-rawat-inap/save-asesmen-keperawatan?id=${payload.pendaftaran_id}`,
      method: "POST",
      dataType: "json",
      contentType: "application/json",
      data: JSON.stringify(payload),
      success: () => {
        $("#patient-history-tab")
          .find(".pekerjaan-nama-text")
          .text(payload.pekerjaan_nama);
        docoNotification(
          "success",
          "Proses berhasil!",
          "Data asesmen keperawatan berhasil disimpan."
        );
        pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksaan-rawat-inap/js/_index js
        $("#tab-asesmenawal a").trigger("click");
      },
      complete: () => {
        hideLoader();
      },
    });
  });

  var keyAsesmenData = Object.keys(asesmenData);
  if (keyAsesmenData.length > 0) {
    keyAsesmenData.map((key) => {
      var dataField = asesmenData[key];
      if (typeof dataField == "boolean") {
        dataField = dataField ? "1" : "0";
      }
      if (dataField != null && dataField.constructor != Array) {
        var inputName = `AsesmenKeperawatanForm[${key}]`;
        var elementCheckbox = $(`input[name="${inputName}"][type="checkbox"]`);
        var elementRadio = $(`input[name="${inputName}"][type="radio"]`);
        var elementInput = $(`[name="${inputName}"]`);
        if (
          dataField != "" &&
          (elementInput.prop("disabled") ||
            (elementCheckbox.length > 0 && elementCheckbox.prop("disabled")) ||
            (elementRadio.length > 0 && elementRadio.prop("disabled")))
        ) {
          elementInput.prop("disabled", false);
        }
        if (key == "is_kapitis" && dataField == "1") {
          $("#asesmenkeperawatanform-is_kapitis").prop("checked", true);
        } else if (elementCheckbox.length > 0) {
          var arrayCheckboxValue = [];
          elementCheckbox.each((index, checkbox) => {
            arrayCheckboxValue.push($(checkbox).val());
          });
          var otherElementCheckbox = $(
            `input[name="${inputName}"][type="text"]`
          );
          if (dataField.split(",").length == 1) {
            $(
              `input[name="${inputName}"][type="checkbox"][value="${dataField}"]`
            ).trigger("change");
          } else {
            dataField.split(",").map((string) => {
              if (arrayCheckboxValue.indexOf(string) >= 0) {
                $(
                  `input[name="${inputName}"][type="checkbox"][value="${string}"]`
                ).trigger("click");
              } else {
                // $(`input[name="${inputName}"][type="checkbox"][value="00"]`).trigger('click')
                otherElementCheckbox.tagsinput("add", string);
              }
            });
          }
        } else if (elementRadio.length > 0) {
          var elementChecked = $(
            `input[name="${inputName}"][type="radio"][value="${dataField}"]`
          );
          if (elementChecked.length > 0) {
            elementChecked.prop("disabled", false);
            elementChecked.trigger("click");
          } else {
            $(`input[name="${inputName}"][type="radio"][value="00"]`).prop(
              "disabled",
              false
            );
            $(`input[name="${inputName}"][type="radio"][value="00"]`).trigger(
              "click"
            );
            dataField.split(",").map((string) => {
              $(`input[name="${inputName}"][type="text"]`).tagsinput(
                "add",
                string
              );
            });
          }
        } else if (key == "dokter_id" || key == "perawat_id") {
          var newState = new Option(
            key == "dokter_id"
              ? asesmenData["dokter_nama"]
              : asesmenData["perawat_nama"],
            dataField,
            true,
            true
          );
          elementInput.append(newState).trigger("change");
        } else if (key == "nilai_nutrisi") {
          $("#score-section").text(dataField);
        } else if (key == "skala_nyeri") {
          $(`[data-type="skala_nyeri"] [data-percentage="${dataField}0"]`)
            .children()
            .trigger("click");
        } else if (key == "skala_nyeri_anak") {
          $(`[data-type="skala_nyeri_anak"] [data-percentage="${dataField}0"]`)
            .children()
            .trigger("click");
        } else if (key == "kategori_triase_disaster") {
          $(`.triage-disaster [data-value="${dataField}"]`).trigger("click");
        } else if (key == "kategori_triase_sehari") {
          $(`.triage-sehari [data-value="${dataField}"]`).trigger("click");
        } else if (key == "jenis_resiko_jatuh") {
          $(`.btn-group-section[data-type="${dataField}"]`).addClass(
            "btn-group-section--active"
          );
        } else if (key == "nilai_luka_bakar") {
          $("[name='AsesmenKeperawatanForm[nilai_luka_bakar]']").val("");
          // const numberInput =
          //   asesmenData.persen_luka_bakar != null
          //     ? asesmenData.persen_luka_bakar.replace(/derajat_/g, "")
          //     : "";
          // if (numberInput != "") {
          //   $(`#nilai_luka_bakar--form`).val(dataField);
          //   $(`#nilai_luka_bakar--form`).prop("disabled", false);
          // }
        } else if (elementInput.length > 0) {
          if (elementInput.prop("tagName").toLowerCase() == "textarea") {
            elementInput.text(dataField);
          } else if (
            elementInput.parent().find(".bootstrap-tagsinput").length > 0
          ) {
            dataField.split(",").map((string) => {
              elementInput.tagsinput("add", string);
            });
          } else {
            elementInput.val(dataField);
          }
          if (elementInput.prop("tagName").toLowerCase() == "select") {
            elementInput.trigger("change");
          }
        }

        $.uniform.update();
      }
    });
    $(".doco-number").trigger("change");
  }
  // append riwayat
  if (data.riwayat != null) {
    data.riwayat.map((itemRiwayat, indexRowRiwayat) => {
      $("#resiko-jatuh__table thead tr").append(
        `<th>${itemRiwayat.tgl_pengkajian}</th>`
      );
      $("#resiko-jatuh__table tbody tr").each(
        (indexTableElement, elementRow) => {
          firstElement = $(elementRow).find("td.form-column input");
          // $(elementRow).find("td.form-column input").removeAttr("id");
          var blankElement = $($(elementRow).find("td.form-column")[0]);
          blankElement.clone().appendTo(blankElement.parent());
          var latestElement = $(elementRow)
            .find("td.form-column")
            .last()
            .find("input");
          var fieldName = latestElement
            .prop("name")
            .replace("AsesmenKeperawatanResikoJatuh[", "")
            .replace("]", "");
          latestElement.prop("name", `${fieldName}-${indexRowRiwayat}`);
          if (latestElement.prop("type") != "text") {
            latestElement
              .parents(".form-column")
              .find(
                `[name="${latestElement.prop("name")}"][value="${
                  itemRiwayat[fieldName]
                }"]`
              )
              .trigger("click");
          } else {
            latestElement.val(itemRiwayat[fieldName]);
          }
          latestElement.prop("disabled", true);
          latestElement.removeAttr("id");
        }
      );
    });
  }

  $("#resiko-jatuh__table input[type='radio'][name*='AsesmenKeperawatanResikoJatuh']"
  ).each((index, element) => {
    if (defaultValueAssigned.indexOf($(element).prop("name")) < 0) {
      $(element).prop("checked", true);
      defaultValueAssigned.push($(element).prop("name"));
    }
  });

  $(".resiko-jatuh-form").uniform({
    radioClass: "choice",
  });

  $(".nutrisi-check").bind("change", () => {
    const firstQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[nutrisi_1a]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[nutrisi_1a]"]:checked'
            ).data("score")
          )
        : 0;
    const secondQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[nutrisi_1b]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[nutrisi_1b]"]:checked'
            ).data("score")
          )
        : 0;
    const thirdQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[nutrisi_2]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[nutrisi_2]"]:checked'
            ).data("score")
          )
        : 0;
    const totalScore = firstQuestion + secondQuestion + thirdQuestion;
    $("#score-section").text(totalScore);
  });
  $(".strongkids-check").bind("change", () => {
    const firstQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[strongkids_kurus]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[strongkids_kurus]"]:checked'
            ).data("score")
          )
        : 0;
    const secondQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[strongkids_turunbb]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[strongkids_turunbb]"]:checked'
            ).data("score")
          )
        : 0;
    const thirdQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[strongkids_keadaan_beresiko]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[strongkids_keadaan_beresiko]"]:checked'
            ).data("score")
          )
        : 0;
    const fourthQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanForm[strongkids_kondisikhusus]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanForm[strongkids_kondisikhusus]"]:checked'
            ).data("score")
          )
        : 0;
    const totalScore =
      firstQuestion + secondQuestion + thirdQuestion + fourthQuestion;
    $("#strongkids-score-section").text(totalScore);
  });
  $(".strongkids-check").trigger("change");
  $('input[name="AsesmenKeperawatanForm[resusitasi]"]').bind(
    "change",
    () => {
      const _val = $(
        'input[name="AsesmenKeperawatanForm[resusitasi]"]:checked'
      ).val();
      if (_val == 1) {
        $(".resusitasi-yes").removeClass("hidden");
        if (!$(".resusitasi-no").hasClass("hidden")) {
          $(".resusitasi-no").addClass("hidden");
        }
      } else {
        $(".resusitasi-no").removeClass("hidden");
        if (!$(".resusitasi-yes").hasClass("hidden")) {
          $(".resusitasi-yes").addClass("hidden");
        }
      }
    }
  );
  $('.survey-extremitas-check[type="checkbox"]').bind(
    "change",
    ({ currentTarget }) => {
      const _elm = $(`#${$(currentTarget).attr("data-target")}`);
      if ($(currentTarget).is(":checked")) {
        _elm.prop("disabled", false);
      } else {
        _elm.val("").prop("disabled", true);
      }
    }
  );
  $('input[name="AsesmenKeperawatanForm[reaksi_pupil]"]').bind(
    "change",
    () => {
      const _elm = $(
        'input[name="AsesmenKeperawatanForm[reaksi_pupil]"]:checked'
      );
      const _val = _elm.val();
      if (!$(".append-osod").hasClass("hidden")) {
        if (_val == "os") {
          $("#pupil-os-text").prop("disabled", false);
          $("#pupil-od-text").val(null).prop("disabled", true);
        } else {
          $("#pupil-od-text").prop("disabled", false);
          $("#pupil-os-text").val(null).prop("disabled", true);
        }
      } else {
        $("#pupil-od-text").val(null).prop("disabled", true);
        $("#pupil-os-text").val(null).prop("disabled", true);
      }
    }
  );
  $('input[name="AsesmenKeperawatanForm[pupil]"]').bind("change", () => {
    const _val = $(
      'input[name="AsesmenKeperawatanForm[pupil]"]:checked'
    ).val();
    if (_val == "unisokort") {
      $(".append-osod").removeClass("hidden");
      $('input[name="AsesmenKeperawatanForm[reaksi_pupil]"]').trigger(
        "change"
      );
    } else {
      if (!$(".append-osod").hasClass("hidden")) {
        $(".append-osod").addClass("hidden");
      }
      $("#pupil-od-text").val(null).prop("disabled", true);
      $("#pupil-os-text").val(null).prop("disabled", true);
    }
  });
  $("#asesmenkeperawatanform-detak_nadi").trigger("keyup");
});

var checkedFieldValue = (fieldName) => {
  return typeof $(
    `input[name="AsesmenKeperawatanForm[${fieldName}]"]:checked`
  ).val() != "undefined"
    ? $(`input[name="AsesmenKeperawatanForm[${fieldName}]"]:checked`).val()
    : "";
};
var fieldValue = (fieldName) => {
  return $(`input[name="AsesmenKeperawatanForm[${fieldName}]"]`).val();
};

var dependentHandler = (currentTarget, callback) => {
  const element = $(currentTarget);
  const inputName = element.prop("name");
  let dataDependent = {};
  try {
    dataDependent = element.data("dependent");
  } catch (error) {
    dataDependent = {};
  }
  if (
    typeof dataDependent.id != "undefined" ||
    typeof dataDependent.class != "undefined"
  ) {
    let elementStringChild =
      typeof dataDependent.id != "undefined" && dataDependent.id != ""
        ? `#${dataDependent.id}`
        : "";
    if (
      typeof dataDependent.class != "undefined" &&
      dataDependent.class != ""
    ) {
      elementStringChild = `${
        elementStringChild != "" ? `${elementStringChild},` : ""
      }.${dataDependent.class}`;
    }
    dataDependent.id =
      typeof dataDependent.id != "undefined" ? dataDependent.id : "";
    dataDependent.class =
      typeof dataDependent.class != "undefined" ? dataDependent.class : "";
    dataDependent.onValue =
      typeof dataDependent.onValue != "undefined" ? dataDependent.onValue : "1";
    // let otherField
    if (elementStringChild != "") {
      let activeOn = dataDependent.onValue;
      const otherElement = $(`input[name='${inputName}'][value='${activeOn}']`);
      callback({
        inputName,
        activeOn,
        otherElement,
        elementStringChild,
        element,
        dataDependent,
        trueValue: otherElement.is(":checked"),
      });
    }
  }
};

var originFieldName = (rawFieldName) => {
  return rawFieldName
    .replace("AsesmenKeperawatanForm[", "")
    .replace("]", "");
};

$(document).ready(function () {
    $(".nutrisi-check").change();
    if ( $('#asesmenkeperawatanform-tinggi_badan').val() != '')  {
        $('#asesmenkeperawatanform-tinggi_badan').val( $('#asesmenkeperawatanform-tinggi_badan').val().replace('.', ',') )
    }
    if ( $('#asesmenkeperawatanform-berat_badan').val() != '')  {
        $('#asesmenkeperawatanform-berat_badan').val( $('#asesmenkeperawatanform-berat_badan').val().replace('.', ',') )
    }
    $('.imt_field').keyup();
    $('#btn-print-asesmen-perawat').on('click', function (e) {
        e.preventDefault();
        var url = "cetak-askep-rd?id=" + pendaftaran_id;
        window.open(url, '_blank');
    });
    $(".btn-verifikasi-gizi").not('.disabled').on("click", function (event) {
      event.preventDefault();
      $(this).docoForm('click', {
        url: '/ranap/pemeriksaan-rawat-inap/verifikasi-skrining-gizi?id=' + pendaftaran_id,
        dataType: 'html',
        success: function (data) {
          location.reload();
        }
      });
    });

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

    function validateFormatTensi(input) {
      input.parent('.input-group').siblings('div.help-block').removeClass('error');
      input.parent('.input-group').siblings('div.help-block').html('');
      let patern = /^\d{1,3}\/\d{1,3}$/g;
      let check = patern.test(input.val());
      if (!check && input.val() != '') {
        input.parent('.input-group').addClass('has-error');
        input.parent('.input-group').siblings('div.help-block').html('<i class="fa fa-exclamation-circle"></i>Format tidak sesuai.');
        input.parent('.input-group').siblings('div.help-block').addClass('error');
        return false;
      } else {
        input.parent('.input-group').removeClass('has-error');
        return true;
      }

    }

    function validateAlloText(input) {
      input.siblings('div.help-block').removeClass('error');
      input.siblings('div.help-block').html('');
      input.parent('.input-group').removeClass('has-error');
      if ($('[name="AsesmenKeperawatanForm[allo_or_auto]"]:checked').val() == 1 && input.val() == '') {
        input.parent('.input-group').addClass('has-error');
        setTimeout(function(){
          input.siblings('div.help-block').html('<i class="fa fa-exclamation-circle"></i>Tidak boleh kosong.');
          input.siblings('div.help-block').addClass('error');
        },100) //settimeout to prevent disappear
      }
    }

    $("#tensi").on("input", function () {
      validateInput(this);
    });

    $('#tensi').on('blur', function () {
      validateFormatTensi($(this));
    });

    $('[name="AsesmenKeperawatanForm[allo_or_auto]"]').on('change', function(){
      let input = $('[name="AsesmenKeperawatanForm[asesmen_allo_text]"]');
      validateAlloText(input);
    })

    $('[name="AsesmenKeperawatanForm[asesmen_allo_text]"]').on('blur', function() {
      validateAlloText($(this));
    });
    
    if (typeof(data.asesmenkeperawatan.additional_data) != "undefined" && data.asesmenkeperawatan.additional_data !== null) {
      var additionalData = JSON.parse(data.asesmenkeperawatan.additional_data);
      var asesmenAuto = additionalData.asesmen_auto;
      var asesmenAllo = additionalData.asesmen_allo;
      if ((asesmenAuto == undefined || asesmenAuto == '' || asesmenAuto == null) && 
          (asesmenAllo == undefined || asesmenAllo == '' || asesmenAllo == null)) {
        $('#allo_or_auto-0').prop('checked', false);
        $('#allo_or_auto-1').prop('checked', false);
      } else if (asesmenAuto == undefined || asesmenAuto == '' || asesmenAuto == null) {
        $('#allo_or_auto-1').prop('checked', true);
        $('#allo_or_auto-1').trigger('change');
      } else if (asesmenAllo == undefined || asesmenAllo == '' || asesmenAllo == null) {
        $('#allo_or_auto-0').prop('checked', true);
        $('#allo_or_auto-0').trigger('change');
      }
    }

    pageFormId = $("#form-asesmen-keperawatan :not([readonly]):not('.disable-get-change')"); // get form id page | declare di pemeriksaan-rawat-inap/js/index js
    pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksaan-rawat-inap/js/_index js

    $(document).on('input', '.skor-input', hitungSkor);

    function hitungSkor() {
      let total = 0;
      let jumlahIsi = 0;
    
      $('.skor-input').each(function() {
        $(this).on('focus', function() {
          $(this).data('old-value', $(this).val());
        });
        let val = parseInt($(this).val());
        if (!isNaN(val)) {
          if (val < 0) {
            $(this).val($(this).data('old-value'));
          }else{
            total += val;
            jumlahIsi++;
          }
        }
      });
    
      let kategori = '';
      let catatan = '';
    
      if (jumlahIsi > 0) {
        $('#total-skor').val(total);
        let catatan_a = 'Laporkan ke DPJP untuk konsultasi dengan Dokter Rehabilitasi Medis.'
        let catatan_b = 'Evaluasi setiap 2 hari atau bila ada perubahan faktor ketergantungan.'
        if (total >= 0 && total <= 24) {
          kategori = 'Ketergantungan Total';
          catatan = catatan_a;
        } else if (total >= 25 && total <= 49) {
          kategori = 'Ketergantungan Berat';
          catatan = catatan_a;
        } else if (total >= 50 && total <= 74) {
          kategori = 'Ketergantungan Sedang';
          catatan = catatan_a;
        } else if (total >= 75 && total <= 90) {
          kategori = 'Ketergantungan Ringan';
          catatan = catatan_b;
        } else if (total >= 91 && total <= 99) {
          kategori = 'Ketergantungan Minimal';
          catatan = catatan_b;
        }
      } else {
        // semua input kosong → paksa clear
        $('#total-skor').val('');
      }
    
      // assign hasil
      $('#kategori').val(kategori);
      $('#catatan').val(catatan);
    }
    
    $(function () {
      $('[data-toggle="popover"]').popover();
    });
})
