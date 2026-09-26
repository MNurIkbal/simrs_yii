var asesmenDataHistory = data_history.asesmenkeperawatan;
$("[name='AsesmenKeperawatanHistoryForm[pernafasan]']").dependentMultipleHandler({
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

$(
  "[name='AsesmenKeperawatanHistoryForm[gangguan_thermoregulasi_tipe]'][type='radio']"
).bind("change", ({ currentTarget }) => {
  $("#gangguan_thermoregulasi_nilai--form").prop(
    "disabled",
    !$(currentTarget).is(":checked")
  );
});

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

        let tb = $('#asesmenKeperawatanHistoryForm-tinggi_badan').val() ? parseFloat($('#asesmenKeperawatanHistoryForm-tinggi_badan').val().replace(',', '.')) : null;
        let bb = $('#asesmenKeperawatanHistoryForm-berat_badan').val() ? parseFloat($('#asesmenKeperawatanHistoryForm-berat_badan').val().replace(',', '.')) : null;
        let kategori_imt = $("#asesmenKeperawatanHistoryForm-ket_imt");
        let bodymassindex_id = $("#asesmenKeperawatanHistoryForm-bodymassindex_id");
        let field_imt = $("#asesmenKeperawatanHistoryForm-imt");
        let field_bbideal = $("#asesmenKeperawatanHistoryForm-bb_ideal");
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
  $(".history_askep").find(".nutrisi-check").trigger("change");
  $(".history_askep").find(".strongkids-check").trigger("change");
  $(".history_askep").find(".phonenumber").on("keyup", ({ currentTarget }) => {
    $(currentTarget).val(
      $(currentTarget)
        .val()
        .replace(/[^0-9.]/g, "")
    );
  });
  $(".history_askep").find(".input-tag").tagsinput();
  $(".history_askep").find(".default-disabled").prop("disabled", true);
  $("[type='radio'],[type='checkbox']").not(".resiko-jatuh-form").uniform({
    radioClass: "choice",
  });

  $(".history_askep").find(".box-scale-line__btn").css("pointer-events", "none");
  $(".history_askep").find(".box-scale-line__btn").bind("click", ({ currentTarget }) => {
    const parentBoxScale = $(currentTarget).parents(".box-scale");
    parentBoxScale
      .find(".box-scale-line__point")
      .removeClass("box-scale-line--selected");
    $(currentTarget).parent().addClass("box-scale-line--selected").prop('disabled',true);
    const dataPercentage = $(currentTarget).parent().data("percentage");
    parentBoxScale
      .find(".box-scale-line__hidePercentage")
      .css("width", `${100 - parseInt(dataPercentage)}%`);

    const dataInfo = $(currentTarget).parent().data("info");
    $(currentTarget).closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
    $('.'+dataInfo).css("background", $(currentTarget).css("background-color"));
    $('#asesmenkeperawatanresikojatuhhistory-skala_nyeri').val(null);
    
    const valueScale = $(currentTarget).html();
    $('#asesmenkeperawatanresikojatuhhistory-skala_nyeri').val(valueScale+"-"+$('.'+dataInfo).html());
    return false;
  });

  $(".history_askep").find("input[type='checkbox'],input[type='radio']").bind(
    "change",
    ({ currentTarget }) => {
      const element = $(currentTarget);
      const inputName = element.prop("name");
      const otherElement = $(`input[name='${inputName}'][value='00']`);
      if (otherElement.length > 0) {
        $(".history_askep").find(`#other-${otherElement.data("fieldname")}`).prop(
          "disabled",
          !otherElement.is(":checked")
        );
        if (!otherElement.is(":checked")) {
          $(".history_askep").find(`#other-${otherElement.data("fieldname")}`).val("");
          if (
            $(".history_askep").find(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput").length > 0
          ) {
            $(".history_askep").find(`#other-${otherElement.data("fieldname")}`).tagsinput(
              "removeAll"
            );
          }
        } else {
          if (
            $(".history_askep").find(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput").length > 0
          ) {
            $(".history_askep").find(`#other-${otherElement.data("fieldname")}`)
              .parent()
              .find(".bootstrap-tagsinput input")
              .prop("disabled", true);
          }
        }
      }
    }
  );
  $(".history_askep").find("[data-dependent]").bind("change", ({ currentTarget }) => {
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
  $(".history_askep").find(".btn-triage button").bind("click", ({ currentTarget }) => {
    $(currentTarget).addClass("btn-triage--active");
    $(currentTarget)
      .parent()
      .find("button")
      .not(currentTarget)
      .removeClass("btn-triage--active");
  });
  $("#agamaFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.agama),
  });
  $("#pendidikanFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.pendidikan),
  });
  $("#pekerjaanFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.pekerjaan),
  });
  $("#caraMasukFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.caramasuk),
  });
  $("#sukuFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.suku),
  });
  $("#gcseyeFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.metodegcs.E),
  });
  $("#gcsverbalFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.metodegcs.V),
  });
  $("#gcsmotorikFormHistory").select2({
    data: [{ id: "", text: "-- Pilih --" }].concat(data_history.metodegcs.M),
  });
  $("#gcseyeFormHistory,#gcsverbalFormHistory,#gcsmotorikFormHistory").bind("change", () => {
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
    data_history.gcs.map((item) => {
      if (
        totalScoreGcs >= item.gcs_nilaimin &&
        totalScoreGcs <= item.gcs_nilaimax
      ) {
        resultGcs = `${item.text}`;
      }
    });
    $("input[name='keterangan_gcs']").val(resultGcs);
    $("#AsesmenKeperawatanHistoryForm-hasil_gcs").val(totalScoreGcs);
  });


  if ($(`input[name="AsesmenKeperawatanHistoryForm[is_nyeri]"]:checked`).val() == '1') {
    $("#nyeri-wrapper-history").show()
      $("#pilih-wrapper-history").show()
  } else {
      $("#nyeri-wrapper-history").hide()
      $("#pilih-wrapper-history").hide()
  }

  $("[name='AsesmenKeperawatanHistoryForm[is_nyeri]']").bind('change', ({ currentTarget }) => {
      if ($(currentTarget).val() == '1') {
          $("#nyeri-wrapper-history").show()
          $("#pilih-wrapper-history").show()
      } else {
          $("#nyeri-wrapper-history").hide()
          $("#pilih-wrapper-history").hide()
      }
  })
  
  if ($(`input[name="AsesmenKeperawatanHistoryForm[pilih_skala]"]:checked`).val() == 'dewasa') {
      $("#dewasa-wrapper-hitory").show()
      $("#anak-wrapper-history").hide()
  } else if ($(`input[name="AsesmenKeperawatanHistoryForm[pilih_skala]"]:checked`).val() == 'anak') {
      $("#dewasa-wrapper-history").hide()
      $("#anak-wrapper-history").show()
  } else {
      $("#dewasa-wrapper-history").hide()
      $("#anak-wrapper-history").hide()
  }

  $("[name='AsesmenKeperawatanHistoryForm[pilih_skala]']").bind('change', ({ currentTarget }) => {
      if ($(currentTarget).val() == 'dewasa') {
          $("#dewasa-wrapper-hitory").show()
          $("#anak-wrapper-history").hide()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      } else if ($(currentTarget).val() == 'anak') {
          $("#dewasa-wrapper-hitory").hide()
          $("#anak-wrapper-history").show()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      } else {
          $("#dewasa-wrapper-hitory").hide()
          $("#anak-wrapper-history").hide()
          $(".box-scale-line__btn").closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
      }
  })

  $("[name='AsesmenKeperawatanHistoryForm[detak_nadi]']").bind(
    "keyup",
    ({ currentTarget }) => {
      hitungNadi = $(currentTarget).val().replaceAll(".", "");
      if (hitungNadi <= 60) {
        $("#AsesmenKeperawatanHistoryForm-hasil_nadi").val("Bradikardia");
      } else if (hitungNadi > 60 && hitungNadi <= 100) {
        $("#AsesmenKeperawatanHistoryForm-hasil_nadi").val("Normal");
      } else if (hitungNadi > 100) {
        $("#AsesmenKeperawatanHistoryForm-hasil_nadi").val("Takikardia");
      }
    }
  );

  // dependent airway breathhing
  $("[name='AsesmenKeperawatanHistoryForm[jalur_nafas]']").bind(
    "change",
    ({ currentTarget }) => {
      $("#jalur_nafas_oksigen--form").prop("disabled", true);
      $("#jalur_nafas_oksigen--form").val("");
      if ($(currentTarget).val() == "bersih_sumbatan") {
        if ($(currentTarget).prop("checked") == true) {
          $(".bersih_sumbatan--dependent").prop("disabled", false);
        } else {
          $(".bersih_sumbatan--dependent").prop("checked", false);
          $(".bersih_sumbatan--dependent").prop("disabled", true);
        }
      } else if ($(currentTarget).val() == "oksigen") {
        $("#jalur_nafas_oksigen--form").prop("disabled", false);
      }
      $.uniform.update();
    }
  );

  var keyAsesmenData = Object.keys(asesmenDataHistory);
  if (keyAsesmenData.length > 0) {
    keyAsesmenData.map((key) => {
      var dataField = asesmenDataHistory[key];
      if (typeof dataField == "boolean") {
        dataField = dataField ? "1" : "0";
      }
      if (dataField != null && dataField.constructor != Array) {
        var inputName = `AsesmenKeperawatanHistoryForm[${key}]`;
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
          $("#asesmenKeperawatanHistoryForm-is_kapitis").prop("checked", true);
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
              ? asesmenDataHistory["dokter_nama"]
              : asesmenDataHistory["perawat_nama"],
            dataField,
            true,
            true
          );
          elementInput.append(newState).trigger("change");
        } else if (key == "nilai_nutrisi") {
          $("#score-section-history").text(dataField);
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
          $("[name='AsesmenKeperawatanHistoryForm[nilai_luka_bakar]']").val("");
          // const numberInput =
          //   asesmenDataHistory.persen_luka_bakar != null
          //     ? asesmenDataHistory.persen_luka_bakar.replace(/derajat_/g, "")
          //     : "";
          // if (numberInput != "") {
          //   $(".history_askep").find(`#nilai_luka_bakar--form`).val(dataField);
          //   $(".history_askep").find(`#nilai_luka_bakar--form`).prop("disabled", false);
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
  if (data_history.riwayat != null) {
    data_history.riwayat.map((itemRiwayat, indexRowRiwayat) => {
      $("#resiko-jatuh__table_history thead tr").append(
        `<th>${itemRiwayat.tgl_pengkajian}</th>`
      );
      $("#resiko-jatuh__table_history tbody tr").each(
        (indexTableElement, elementRow) => {
          firstElement = $(elementRow).find("td.form-column input");
          $(elementRow).find("td.form-column input").removeAttr("id");
          var blankElement = $($(elementRow).find("td.form-column")[0]);
          blankElement.clone().appendTo(blankElement.parent());
          var latestElement = $(elementRow)
            .find("td.form-column")
            .last()
            .find("input");
          var fieldName = latestElement
            .prop("name")
            .replace("AsesmenKeperawatanResikoJatuhHistory[", "")
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
        }
      );
    });
    $("#resiko-jatuh__table_history thead tr th:nth-child(3)").hide();
    $("#resiko-jatuh__table_history tbody tr td:nth-child(3)").hide();
  }

  $("#resiko-jatuh__table input[type='radio'][name*='AsesmenKeperawatanResikoJatuhHistory']"
  ).each((index, element) => {
    if (defaultValueAssigned.indexOf($(element).prop("name")) < 0) {
      $(element).prop("checked", true);
      defaultValueAssigned.push($(element).prop("name"));
    }
  });

  $(".resiko-jatuh-form").uniform({
    radioClass: "choice",
  });

  $(".history_askep").find(".nutrisi-check").bind("change", () => {
    const firstQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[nutrisi_1a]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[nutrisi_1a]"]:checked'
            ).data("score")
          )
        : 0;
    const secondQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[nutrisi_1b]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[nutrisi_1b]"]:checked'
            ).data("score")
          )
        : 0;
    const thirdQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[nutrisi_2]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[nutrisi_2]"]:checked'
            ).data("score")
          )
        : 0;
    const totalScore = firstQuestion + secondQuestion + thirdQuestion;
    $("#score-section-history").text(totalScore);
  });
  $(".history_askep").find(".strongkids-check").bind("change", () => {
    const firstQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[strongkids_kurus]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[strongkids_kurus]"]:checked'
            ).data("score")
          )
        : 0;
    const secondQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[strongkids_turunbb]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[strongkids_turunbb]"]:checked'
            ).data("score")
          )
        : 0;
    const thirdQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[strongkids_keadaan_beresiko]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[strongkids_keadaan_beresiko]"]:checked'
            ).data("score")
          )
        : 0;
    const fourthQuestion =
      typeof $(
        'input[name="AsesmenKeperawatanHistoryForm[strongkids_kondisikhusus]"]:checked'
      ).val() != "undefined"
        ? parseInt(
            $(
              'input[name="AsesmenKeperawatanHistoryForm[strongkids_kondisikhusus]"]:checked'
            ).data("score")
          )
        : 0;
    const totalScore =
      firstQuestion + secondQuestion + thirdQuestion + fourthQuestion;
    $("#strongkids-score-section-history").text(totalScore);
  });
  $('input[name="AsesmenKeperawatanHistoryForm[resusitasi]"]').bind(
    "change",
    () => {
      const _val = $(
        'input[name="AsesmenKeperawatanHistoryForm[resusitasi]"]:checked'
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
      const _elm = $(".history_askep").find(`#${$(currentTarget).attr("data-target")}`);
      if ($(currentTarget).is(":checked")) {
        _elm.prop("disabled", false);
      } else {
        _elm.val("").prop("disabled", true);
      }
    }
  );
  $('input[name="AsesmenKeperawatanHistoryForm[reaksi_pupil]"]').bind(
    "change",
    () => {
      const _elm = $(
        'input[name="AsesmenKeperawatanHistoryForm[reaksi_pupil]"]:checked'
      );
      const _val = _elm.val();
      if (!$(".append-osod").hasClass("hidden")) {
        if (_val == "os") {
          $("#pupil-os-text").prop("disabled", true);
          $("#pupil-od-text").val(null).prop("disabled", true);
        } else {
          $("#pupil-od-text").prop("disabled", true);
          $("#pupil-os-text").val(null).prop("disabled", true);
        }
      } else {
        $("#pupil-od-text").val(null).prop("disabled", true);
        $("#pupil-os-text").val(null).prop("disabled", true);
      }
    }
  );
  $('input[name="AsesmenKeperawatanHistoryForm[pupil]"]').bind("change", () => {
    const _val = $(
      'input[name="AsesmenKeperawatanHistoryForm[pupil]"]:checked'
    ).val();
    if (_val == "unisokort") {
      $(".append-osod").removeClass("hidden");
      $('input[name="AsesmenKeperawatanHistoryForm[reaksi_pupil]"]').trigger(
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
  $("#asesmenkeperawatanhistoryform-detak_nadi").trigger("keyup");
});

var checkedFieldValue = (fieldName) => {
  return typeof $(
    `input[name="AsesmenKeperawatanHistoryForm[${fieldName}]"]:checked`
  ).val() != "undefined"
    ? $(`input[name="AsesmenKeperawatanHistoryForm[${fieldName}]"]:checked`).val()
    : "";
};
var fieldValue = (fieldName) => {
  return $(`input[name="AsesmenKeperawatanHistoryForm[${fieldName}]"]`).val();
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

$(document).ready(function () {
  $(".history_askep").find(".nutrisi-check").change();
  $(".history_askep").find('.imt_field').keyup();
  $(".history_askep").find(".strongkids-check").change();
  $(".history_askep").find("select,input,textarea").prop("disabled", true);
  $(".history_askep").find(".checker,.choice").addClass("disabled");
  $(function () {
    $('[data-toggle="popover"]').popover();
  });
})
