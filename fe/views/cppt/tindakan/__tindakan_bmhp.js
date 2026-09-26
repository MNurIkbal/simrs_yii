$("#depo-form").select2InfinityScroll({
  url: `${frontendUrl}/list-depo`,
});
var optionSelect = {
  tindakan: [],
  paket: [],
};
var select2List = {};
var is_spesialis = false;
$(() => {
  $(".uniform-part").uniform({
    radioClass: "choice",
  });

  if (konfig_spesialis) {
    $("#group_cyto1").addClass("hidden");
    $("#tindakan-form").attr("disabled", true);
  }
  // initiate nurse
  $("#related-tindakan-form").select2({
    placeholder: "-- Pilih Tindakan --",
    allowClear: true,
    dropdownParent: $("#tindakan-form-wrapper"),
    data: [{ id: "", text: "" }],
  });

  $(".spesialis-form").select2({
    placeholder: "-- Pilih Spesialis --",
    allowClear: true,
    dropdownParent: $("#tindakan-form-wrapper"),
    data: [{ id: "", text: "" }].concat(
      mapDropdownAsSelectPayload(
        dropdownList.spesialis,
        "spesialis_id",
        "spesialis_nama"
      )
    ),
  });

  $(".nurse-form").select2({
    placeholder: "-- Pilih Perawat --",
    allowClear: true,
    dropdownParent: $("#tindakan-form-wrapper"),
    data: [{ id: "", text: "" }].concat(
      mapDropdownAsSelectPayload(
        dropdownList.perawat,
        "pegawai_id",
        "nama_pegawai"
      )
    ),
  });
  $(".doctor-form").select2({
    placeholder: "-- Pilih Dokter --",
    allowClear: true,
    dropdownParent: $("#tindakan-form-wrapper"),
    data: [{ id: "", text: "" }].concat(
      mapDropdownAsSelectPayload(
        dropdownList.dokter,
        "pegawai_id",
        "nama_pegawai"
      )
    ),
  });
  if (document.getElementById("dokter-form")) {
    $("#dokter-form").val(dokterDpjp).trigger("change");
  }

  if (typeof userSpesialisId !== "undefined" && userSpesialisId) {
    $("#spesialis-form").val(userSpesialisId).change();
  }

  setDropDownTindakan();
  calculatePrice();
  generateMedDropdown();

  validasiClosePopup();
});

var setDropDownTindakan = (is_changed = false) => {
  // initiate tindakan & paket form
  if (is_spesialis || is_changed) {
    select2List = optionSelect;
  } else {
    select2List = dropdownList;
  }
  optionSelect = {
    tindakan: [{ id: "", text: "" }].concat(
      mapDropdownAsSelectPayload(
        select2List.tindakan,
        "daftartindakan_id",
        "daftartindakan_nama"
      )
    ),
    paket: [{ id: "", text: "" }].concat(
      mapDropdownAsSelectPayload(
        select2List.paket,
        "tipepaket_id",
        "tipepaket_nama"
      )
    ),
  };

  $("#tindakan-form").select2({
    placeholder: "-- Pilih Tindakan --",
    allowClear: true,
    data: optionSelect.tindakan,
    dropdownParent: $("#tindakan-form-wrapper"),
    templateResult: select2FormatState,
    templateSelection: select2FormatSelectedState,
  });
  is_spesialis = false;
};

var select2FormatState = (state) => {
  if (!state.id || !konfig_tindakan_harga) {
    return state.text;
  }
  var $state = $(
    "<span>" +
      state.text +
      '<div class="tindakan-harga">Rp. ' +
      docoHelper.convertToRupiah(state.harga_tariftindakan) +
      "</div>" +
      "</span>"
  );
  return $state;
};

var select2FormatSelectedState = (state) => {
  if (!state.id || !konfig_tindakan_harga) {
    return state.text;
  }
  var $state = $(
    "<span>" +
      state.text +
      ' - <span class="tindakan-harga">Rp.' +
      docoHelper.convertToRupiah(state.harga_tariftindakan) +
      "</span>" +
      "</span>"
  );
  return $state;
};

$("#package-checkbox").bind("change", ({ currentTarget }) => {
  $("#tindakan-form").html("");
  $("#tindakan-form").select2({
    placeholder: $(currentTarget).is(":checked")
      ? "-- Pilih Paket --"
      : "-- Pilih Tindakan --",
    allowClear: true,
    data: $(currentTarget).is(":checked")
      ? optionSelect.paket
      : optionSelect.tindakan,
    dropdownParent: $("#tindakan-form-wrapper"),
    templateResult: select2FormatState,
    templateSelection: select2FormatSelectedState,
  });
});

var mapDropdownAsSelectPayload = (payload, key, text) => {
  var result = [];
  var isObject = payload.constructor == Object;
  if (isObject) {
    Object.keys(payload).map((keyOfPayload) => {
      var eachPayload = payload[keyOfPayload];
      result.push({
        id: eachPayload[key],
        text: eachPayload[text],
        ...eachPayload,
      });
    });
  } else {
    payload.map((eachArray) => {
      result.push({
        id: eachArray[key],
        text: eachArray[text],
        ...eachArray,
      });
    });
  }
  return result;
};

var getCurrentCytoRate = () => {
  if ($("#tindakan-form").select2("data").length > 0) {
    const selectedTindakan = $("#tindakan-form").select2("data")[0];
    return (
      parseFloat(selectedTindakan.harga_tariftindakan) *
      (parseFloat(selectedTindakan.persencyto_tindakan) / 100)
    );
  } else {
    return 0;
  }
};

var calculatePrice = () => {
  if ($("#tindakan-form").select2("data").length > 0) {
    const selectedTindakan = $("#tindakan-form").select2("data")[0];
    // first check tarif
    $("#tarif-satuan").text(
      thousandFormat(selectedTindakan.harga_tariftindakan || 0)
    );
    let totalPrice = parseFloat(selectedTindakan.harga_tariftindakan || 0);
    let cytoRate = 0;
    if ($("#cyto-checkbox").is(":checked")) {
      // add cyto percentage
      cytoRate = getCurrentCytoRate();
    }
    if (
      $("#package-checkbox").is(":checked") &&
      typeof selectedTindakan.list_tindakan != "undefined" &&
      selectedTindakan.list_tindakan.length > 0
    ) {
      $("#detail-package-section").parent().show();
      $("#detail-package-section").html("");
      $("#detail-package-section").append(`<ul>`);
      selectedTindakan.list_tindakan.map((eachTindakan) => {
        $("#detail-package-section").append(
          `<li>${eachTindakan.daftartindakan_nama}</li>`
        );
      });

      $("#detail-package-section").append(`</ul>`);
    } else {
      $("#detail-package-section").parent().hide();
    }
    totalPrice += cytoRate;
    let qtyTindakan = $("#qty-tindakan-form").val();
    $("#cyto-rate").val(thousandFormat(cytoRate));
    $("#jumlah-tarif").text(thousandFormat(totalPrice * qtyTindakan));
  } else {
    $("#tarif-satuan").text("Rp. 0");
    $("#jumlah-tarif").text("Rp. 0");
    $("#cyto-rate").val("Rp. 0");
    $("#detail-package-section").parent().hide();
  }
};

$("#package-checkbox,#tindakan-form,#qty-tindakan-form,#cyto-checkbox").bind(
  "change",
  () => {
    calculatePrice();
  }
);

$(document).on("click", "#btn-add-tindakan", () => {
  let errorMessage = "";
  const tindakan = $("#tindakan-form").select2("data")[0];

  if (typeof tindakan == "undefined" || !tindakan.id || !tindakan.text) {
    errorMessage = "Tindakan/Paket belum dipilih.";
  } else if ($("#qty-tindakan-form").val() == 0) {
    errorMessage = "Jumlah tindakan harus lebih dari 0";
  } else if (
    $(
      $("#package-checkbox").is(":checked")
        ? `[data-paket="${tindakan.tipepaket_id}"]`
        : `[data-tindakan-id="${tindakan.daftartindakan_id}"]`
    ).length > 0
  ) {
    errorMessage = "Tindakan sudah ditambahkan pada tabel.";
  }
  if (konfig_spesialis) {
    const tindakan_spesialis = $("#spesialis-form").select2("data")[0];
    if (
      typeof tindakan_spesialis == "undefined" ||
      !tindakan_spesialis.id ||
      !tindakan_spesialis.text
    ) {
      errorMessage = "Kategori Tindakan Spesialis Belum Dipilih.";
    }
  }
  // check duplication
  if (errorMessage != "") {
    docoNotification("warning", "Silakan cek kembali form", errorMessage);
    return false;
  }
  $("#depo-form").prop("disabled", true);
  tindakan.cyto_fee = getCurrentCytoRate();
  const perawat1Data =
    $("#perawat1-form").select2("data").length > 0
      ? $("#perawat1-form").select2("data")[0]
      : { id: null, text: "-" };
  const perawat2Data =
    $("#perawat2-form").select2("data").length > 0
      ? $("#perawat2-form").select2("data")[0]
      : { id: null, text: "-" };
  const rowData = {
    tindakan,
    perawat1Data,
    perawat2Data,
    qty: $("#qty-tindakan-form").val(),
    is_cyto: $("#cyto-checkbox").is(":checked"),
    is_consent: $("#consent-checkbox").is(":checked"),
  };
  // first append row
  const latestIndex = $("#table-tindakan tbody tr").not(".empty-row").length;

  let listDetailTindakan = "";
  if (
    typeof tindakan.list_tindakan != "undefined" &&
    tindakan.list_tindakan.length > 0
  ) {
    tindakan.list_tindakan.map((eachTindakan) => {
      listDetailTindakan += `<p style="margin-bottom: 0px;"><span>&#8226;</span> ${eachTindakan.daftartindakan_nama}</p>`;
    });
  }

  const tindakanHtml = () => {
    let html =
      '<span class="font-weight-bold">' + rowData.tindakan.text + "</span>";
    let detailTindakan = listDetailTindakan
      ? '<div class="detail-tindakan">' + listDetailTindakan + "</div>"
      : "";
    return html + detailTindakan;
  };

  $("#table-tindakan tbody")
    .append(
      `
        <tr data-tindakan-id="${tindakan.daftartindakan_id}" data-paket="${
        tindakan.tipepaket_id
      }">
            <td>${latestIndex + 1}</td>
            <td>${moment().format("DD MMMM YYYY")}</td>
            <td>
                ${tindakanHtml()}
            </td>
            <td>${rowData.perawat1Data.text}</td>
            <td>${rowData.perawat2Data.text}</td>
            <td class="text-center"><input type="text" class="qty-tindakan-table doco-number form-control text-center" name="qty_tindakan_table" value="${
              rowData.qty
            }"></td>
            <td>${
              type == "RI" ? "Belum diimplementasi" : "Sudah diimplementasi"
            }</td>
            <td class="text-center">${rowData.is_cyto ? "✓" : "-"}</td>
            <td class="text-center">${rowData.is_consent ? "✓" : "-"}</td>
            <td class="text-center" style="padding-bottom: 8px !important;"><button type='button' style='margin-right: 5px' class='btn btn-danger btn-xs btn-action btn-remove-tindakan'><i class='fa fa-trash'></i></button></td>
        </tr>
    `
    )
    .on("change", "input.qty-tindakan-table", function () {
      this.setAttribute("value", $(this).val());
      var data = $(this).closest("tr").data();
      data.qty = $(this).val();
      $(this).closest("tr").data(data);
    });

  $(".btn-remove-tindakan").unbind("click");
  $(".btn-remove-tindakan").bind("click", ({ currentTarget }) => {
    const { tindakan } = $(currentTarget).parents("tr").data();
    $(
      tindakan.daftartindakan_id != null
        ? `[data-tindakan-id="${tindakan.daftartindakan_id}"]`
        : `[data-paket="${tindakan.tipepaket_id}"]`
    ).remove();
    reindexingTable("tindakan");
    reindexingTable("obat");
    flagMedStockUnavailable();

    validasiClosePopup();
  });

  const latestRow = $($("#table-tindakan tbody tr").last());
  latestRow.data(rowData);
  $("#table-tindakan .empty-row").hide();
  $("#cyto-checkbox").prop("checked", false);
  $("#consent-checkbox").prop("checked", false);
  $.uniform.update();
  $("#qty-tindakan-form").val(1);
  $("#tindakan-form,#perawat1-form,#perawat2-form").val(null).trigger("change");
  addMedicine(
    {
      is_cyto: rowData.is_cyto,
      perawat1Data,
      perawat2Data,
      ...rowData.tindakan,
    },
    rowData.tindakan.list_bmhp
  );
  // append obat
  updateDropdownTindakan();

  validasiClosePopup();
});

var addMedicine = (header, meds) => {
  $("#depo-form").prop("disabled", true);
  if (typeof header.list_tindakan != "undefined") {
    delete header.list_tindakan;
  }
  if (typeof header.list_bmhp != "undefined") {
    delete header.list_bmhp;
  }
  $("#table-obat .empty-row").hide();
  meds.map((med) => {
    var latestIndex = $("#table-obat tbody tr").not(".empty-row").length;

    if (typeof med.qty_konversi != "undefined") {
      med.qty_oa = med.qty_konversi;
      med.qty = med.qty_konversi;
    }
    med.is_ditagihkan =
      typeof med.is_ditagihkan != "undefined" && med.is_ditagihkan ? 1 : 0;
    $("#table-obat tbody").append(`
            <tr data-med-id="${med.obatalkes_id}" ${
      typeof header.daftartindakan_id != "undefined"
        ? header.daftartindakan_id != null
          ? `data-tindakan-id="${header.daftartindakan_id}"`
          : `data-paket="${header.tipepaket_id}"`
        : ""
    }>
                <td>${latestIndex + 1}</td>
                <td>${moment().format("DD-MMMM-YYYY")}</td>
                <td>${
                  typeof header.tipepaket_nama != "undefined" &&
                  header.tipepaket_nama != null
                    ? header.tipepaket_nama
                    : header.daftartindakan_nama != null
                    ? header.daftartindakan_nama
                    : "-"
                }</td>
                <td>${med.obatalkes_nama}</td>
                <td>${header.perawat1Data.text}</td>
                <td>${header.perawat2Data.text}</td>
                <td>
                    <div class="form-group">
                        <input type="text" class="qty-obat-table doco-decimal-wcomma form-control text-center" name="qty_obat_table" value="${
                          med.qty_oa
                        }">
                    </div>
                </td>
                <td>${med.is_ditagihkan == 1 ? "✓" : "-"}</td>
                <td>${
                  type == "RI" ? "Belum diimplementasi" : "Sudah diimplementasi"
                }</td>
                <td class="text-center" style="padding-bottom: 8px !important;"><button type='button' style='margin-right: 5px' class='btn btn-danger btn-xs btn-action btn-remove-obat'><i class='fa fa-trash'></i></button></td>
            </tr>
        `);
    $("#table-obat tbody tr")
      .last()
      .data({
        ...header,
        ...med,
      });
  });
  $(".qty-obat-table").unbind("change");
  $(".qty-obat-table").bind("change", ({ currentTarget }) => {
    flagMedStockUnavailable();
  });
  $(".btn-remove-obat").unbind("click");
  $(".btn-remove-obat").bind("click", ({ currentTarget }) => {
    // const data = $(currentTarget).parents('tr').data()
    $($(currentTarget).parents("tr")).remove();
    reindexingTable("obat");
    flagMedStockUnavailable();

    validasiClosePopup();
  });
  flagMedStockUnavailable();
};
$("#depo-form").bind("change", () => {
  showLoader();
  $.ajax({
    url: `${frontendUrl}/list-tindakan-paket`,
    data: {
      pendaftaran_id: pendaftaranId,
      depo_id: $("#depo-form").val(),
    },
    success: (res) => {
      optionSelect = res.data;
      $("#package-checkbox").trigger("change");
      setDropDownTindakan(true);
    },
  });
});
$('[name="jenis_obat"],#depo-form').bind("change", () => {
  $("#obat-form option").remove();
  $("#obat-form").trigger("change");
  generateMedDropdown();
});

$("#spesialis-form").bind("change", () => {
  showLoader();
  $.ajax({
    url: `${frontendUrl}/tindakan-spesialis`,
    data: {
      penjamin_id: penjaminId,
      kelaspelayanan_id: kelasPelayananId,
      ruangan_id: $("#depo-form").val(),
      spesialis_id: $("#spesialis-form").val(),
    },
    success: (res) => {
      optionSelect = res.data;
      is_spesialis = $("#spesialis-form").val() != "" ? true : false;
      optionSelect = {
        tindakan: [{ id: "", text: "" }].concat(
          mapDropdownAsSelectPayload(
            optionSelect.tindakan,
            "daftartindakan_id",
            "daftartindakan_nama"
          )
        ),
        paket: [{ id: "", text: "" }].concat(
          mapDropdownAsSelectPayload(
            optionSelect.paket,
            "tipepaket_id",
            "tipepaket_nama"
          )
        ),
      };
      $("#tindakan-form").attr("disabled", false);
      $("#package-checkbox").trigger("change");
      setDropDownTindakan(true);
    },
  });
});
var generateMedDropdown = () => {
  $("#obat-form").html("");
  $("#obat-form").select2InfinityScroll({
    url: `${frontendUrl}/list-bmhp`,
    callbackData: (param) => {
      return {
        ...param,
        penjamin_id: penjaminId,
        jenis: $('[name="jenis_obat"]:checked').val(),
        kelaspelayanan_id: kelasPelayananId,
        ruangan_id: $("#depo-form").val(),
        instalasi_id: instalasiId,
      };
    },
    callbackProccess: (response) => {
      let results = [];
      response.results.map((item) => {
        results.push({
          id: item.obatalkes_id,
          text: item.obatalkes_nama,
          ...item,
        });
      });
      delete response.results;
      return {
        ...response,
        results,
      };
    },
  });
};
$("#obat-form").bind("change", () => {
  // update stok on layout
  const medData =
    $("#obat-form").select2("data").length > 0
      ? $("#obat-form").select2("data")[0]
      : null;
  $("#stock-obat").text(medData != null ? medData.qty_tersedia : "0");
  if (medData != undefined) {
    validateMedStock();
  }
});
$("#qty-med-form").on("keyup", () => {
  validateMedStock();
});
var validateMedStock = () => {
  const medData =
    $("#obat-form").select2("data").length > 0
      ? $("#obat-form").select2("data")[0]
      : null;
  const relatedTindakanData = $("#related-tindakan-form").val();
  // qty_tersedia
  let qtyRaw = $("#qty-med-form").val();
  if (qtyRaw == "") {
    qtyRaw = "0";
    $("#qty-med-form").val(qtyRaw);
  }
  let qtyTotal = parseFloat(qtyRaw.replace(/,/g, "."));
  let errorBucket = {
    element: "",
    message: "",
  };
  if (medData == undefined) {
    // add form error
    errorBucket.element = "#obat-parent";
    errorBucket.message = "Obat belum dipilih";
  }

  // if ( relatedTindakanData == '' ) {
  //     errorBucket.element = '#related-tindakan-parent'
  //     errorBucket.message = 'Tindakan Tidak Boleh Kosong'
  // }
  // else if (qtyTotal > medData.qty_tersedia) {
  // $("#qty-med-form").val(medData.qty_tersedia)
  // qtyTotal = medData.qty_tersedia
  // errorBucket.element = '#qty-parent'
  // errorBucket.message = 'Jumlah tidak boleh melebihi stok'
  // }
  if (errorBucket.message != "") {
    if (errorBucket.element != "") {
      if (!$(errorBucket.element).hasClass("has-error")) {
        $(errorBucket.element).addClass("has-error");
        $(errorBucket.element).append(
          `<span class="help-block error"><i class="fa fa-exclamation-circle"></i>${errorBucket.message}</span>`
        );
      }
    } else {
      docoNotification(
        "warning",
        "Silakan cek kembali form",
        errorBucket.message
      );
    }
    return false;
  }
  // count total tarif
  $("#med-price").val(
    thousandFormat((medData.hargaygdipakai * qtyTotal).toFixed(2))
  );
  const headerMeds = {
    perawat1Data:
      $("#perawat1bmhp-form").select2("data").length == 0
        ? [{ id: null, text: "-" }]
        : $("#perawat1bmhp-form").select2("data")[0],
    perawat2Data:
      $("#perawat2bmhp-form").select2("data").length == 0
        ? [{ id: null, text: "-" }]
        : $("#perawat2bmhp-form").select2("data")[0],
    ...($("#related-tindakan-form").val() != ""
      ? $("#related-tindakan-form").select2("data")[0]
      : {}),
  };
  return {
    header: headerMeds,
    meds: [
      {
        qty_oa: qtyRaw,
        qty: qtyTotal,
        stok: medData.qty_tersedia,
        is_ditagihkan: $("#tagihkan-checkbox").prop("checked") ? 1 : 0,
        ...medData,
      },
    ],
  };
};

$("#btn-add-med").bind("click", () => {
  const payloadMed = validateMedStock();
  if (payloadMed) {
    addMedicine(payloadMed.header, payloadMed.meds);
    $("#perawat1bmhp-form,#perawat2bmhp-form,#related-tindakan-form,#obat-form")
      .val(null)
      .trigger("change");
    $("#qty-med-form").val(1);
    $("#med-price").val("Rp. 0");

    validasiClosePopup();
  }
});

var updateDropdownTindakan = () => {
  let result = [];
  $("#table-tindakan tbody tr")
    .not(".empty-row")
    .each((index, element) => {
      var elementRow = $(element).data();
      result.push(elementRow.tindakan);
    });
  $("#related-tindakan-form").html("");
  $("#related-tindakan-form").select2({
    placeholder: "-- Pilih Tindakan --",
    allowClear: true,
    data: [{ id: "", text: "" }].concat(result),
    dropdownParent: $("#tindakan-form-wrapper"),
    templateResult: select2FormatState,
    templateSelection: select2FormatSelectedState,
  });
  $("#related-tindakan-form").val(null).trigger("change");
};

var flagMedStockUnavailable = () => {
  var stockUsed = {};
  $("#table-obat tbody tr")
    .not(".empty-row")
    .each((indexMed, elementMed) => {
      var qty = parseFloat(
        $(elementMed).find(".qty-obat-table").val().replace(/,/g, ".")
      );
      var { stok, obatalkes_id } = $(elementMed).data();
      if (typeof stockUsed[obatalkes_id] == "undefined") {
        stockUsed[obatalkes_id] = 0;
      }
      stockUsed[obatalkes_id] += qty;
      if (stok < stockUsed[obatalkes_id]) {
        $(`[data-med-id="${obatalkes_id}"]`).addClass("unavailable-stock");
      } else {
        $(`[data-med-id="${obatalkes_id}"]`).removeClass("unavailable-stock");
      }
    });
};

var reindexingTable = (type) => {
  if ($(`#table-${type} tbody tr`).not(".empty-row").length == 0) {
    $(`#table-${type} tbody .empty-row`).show();
  } else {
    $(`#table-${type} tbody tr`)
      .not(".empty-row")
      .each((indexMed, rowMed) => {
        $($(rowMed).find("td")[0]).text(indexMed + 1);
      });
  }
};

$("#btn-save").bind("click", () => {
  if (document.getElementById("dokter-form")) {
    // Validate dokter
    let msgErr = "";
    const dokter = $("#dokter-form").select2("data")[0];
    if (typeof dokter == "undefined" || !dokter.id || !dokter.text) {
      msgErr = "Dokter belum dipilih.";
    }
    // check duplication
    if (msgErr != "") {
      docoNotification("warning", "Silakan cek kembali form", msgErr);
      return false;
    }
  }
  // mapping data
  let tindakan = [];
  let obat = [];
  let hasCathlab = false;
  const header = {
    ...($("[name='dokter_id']").length != 0
      ? { dokterpenanggungjawab_id: $("[name='dokter_id']").val() }
      : {}),
    // perawat1_id: $("#perawat1-form").val(),
    // perawat2_id: $("#perawat2-form").val(),
    depo_id: $("#depo-form").val(),
  };
  // mapping data tindakan
  $("#table-tindakan tbody tr")
    .not(".empty-row")
    .each((indexTindakan, elementTindakan) => {
      var rowTindakan = $(elementTindakan).data();
      if (
        !hasCathlab &&
        rowTindakan.tindakan.kelompoktindakan_id == kelTindakanCathlab
      ) {
        hasCathlab = true;
      }
      tindakan.push({
        daftartindakan_id: rowTindakan.tindakan.daftartindakan_id,
        tipepaket_id: rowTindakan.tindakan.tipepaket_id,
        perawat1_id: rowTindakan.perawat1Data.id,
        perawat2_id: rowTindakan.perawat2Data.id,
        cyto_tindakan: rowTindakan.is_cyto,
        consent_tindakan: rowTindakan.is_consent,
        cyto_fee: rowTindakan.tindakan.cyto_fee,
        fee: parseFloat(rowTindakan.tindakan.harga_tariftindakan),
        qty_tindakan: rowTindakan.qty,
        is_paket: rowTindakan.tindakan.tipepaket_id != null ? 1 : 0,
        kelompoktindakan_id: rowTindakan.tindakan.kelompoktindakan_id,
        is_konsultasi: rowTindakan.tindakan.is_konsultasi,
      });
    });
  // mapping data tindakan
  let qtyValid = true;
  $("#table-obat tbody tr")
    .not(".empty-row")
    .each((indexTindakan, elementObat) => {
      var rowData = $(elementObat).data();
      var qtyOa = parseFloat(
        $(elementObat).find(".qty-obat-table").val().replace(/,/g, ".")
      );
      if (qtyOa == 0) {
        $(elementObat)
          .find(".qty-obat-table")
          .parents(".form-group")
          .addClass("has-error")
          .append(`<p class="help-block error">Qty harus lebih dari 0</p>`);
        qtyValid = false;
      }
      obat.push({
        ...(typeof rowData.tipepaket_id != "undefined"
          ? rowData.tipepaket_id != null
            ? { tipepaket_id: rowData.tipepaket_id }
            : { daftartindakan_id: rowData.daftartindakan_id }
          : {}),
        obatalkes_id: rowData.obatalkes_id,
        perawat1_id: rowData.perawat1Data.id,
        perawat2_id: rowData.perawat2Data.id,
        qty_oa: qtyOa,
        is_ditagihkan: rowData.is_ditagihkan,
        tindakan_is_cyto:
          typeof rowData.is_cyto != "undefined" && rowData.is_cyto ? 1 : 0,
        tindakan_is_paket: rowData.tipepaket_id != null ? 1 : 0,
        dokterpenanggungjawab_id: $("[name='dokter_id']").val(),
      });
    });

  if (
    $("#table-obat tbody tr").not(".empty-row").length == 0 &&
    $("#table-tindakan tbody tr").not(".empty-row").length == 0
  ) {
    docoNotification(
      "warning",
      "Perhatian !!!",
      "Tidak ada Item BMHP yang di Inputkan"
    );
    return false;
  }

  let validasiStok = $("#table-obat tbody").find(".unavailable-stock").length;
  if (validasiStok > 0) {
    docoNotification(
      "warning",
      "Silakan cek kembali Stok Obat",
      "Stok obat kosong"
    );
    return false;
  }

  if (qtyValid) {
    const payloadData = {
      pendaftaran_id: pendaftaranId,
      cppt_id: cpptIdTindakanBmhp,
      is_puasa: $("#fasting-checkbox").is(":checked"),
      catatan: $("#catatan-form").val(),
      header,
      tindakan,
      obat,
      use_default_dpjp: useDefaultDpjp,
    };

    let saveBmhpParams = new URLSearchParams({
      id: typeof pendaftaranId != "undefined" ? pendaftaranId : "",
      konsulpoli_id: typeof konsulpoli_id != "undefined" ? konsulpoli_id : "",
    }).toString();

    $.ajax({
      url: `${frontendUrl}/save-tindakan-bmhp?${saveBmhpParams}`,
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify(payloadData),
      success: (response) => {
        const { cppt_id } = response.data;
        docoNotification(
          "success",
          "Sukses",
          "Tindakan dan Obat BMHP berhasil disimpan"
        );
        $("#tindakan-form-wrapper").parents(".modal").modal("hide");
        if (typeof tableCppt != "undefined") {
          tableCppt.draw();
        } else if (typeof table_dpjp != "undefined") {
          table_dpjp.draw();
        }
        if (
          typeof last_cppt != "undefined" &&
          (cppt_id != "" || cppt_id != "undefined")
        ) {
          last_cppt = cppt_id;
        }

        if (hasCathlab) {
          $(".cathlab-tabs").removeClass("hidden");
        }
      },
    });
  } else {
    docoNotification(
      "warning",
      "Silakan cek kembali form",
      "Form tabel obat tidak valid"
    );
  }
});

/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
  var tableTindakan = $("#table-tindakan tbody tr").not(".empty-row").length;
  var tableObat = $("#table-obat tbody tr").not(".empty-row").length;

  if (tableTindakan > 0 || tableObat > 0) {
    var isUpdate = true;
  } else {
    var isUpdate = false;
  }

  if (isUpdate == true) {
    $(".close-modal-jadwal").attr("data-dismiss-confirmation", "modal");
    $(".close-modal-jadwal").removeAttr("data-dismiss");
  } else {
    $(".close-modal-jadwal").removeAttr("data-dismiss-confirmation");
    $(".close-modal-jadwal").attr("data-dismiss", "modal");
  }
}

$("#btn-history-tindakan").unbind();
$("#btn-history-tindakan").bind("click", () => {
  const { href, width } = $("#btn-history-tindakan").data();
  showLoader("Memuat Halaman...");
  $("#modal_riwayat").find(".modal-dialog").css("width", width);
  $("#modal_riwayat .modal-content").docoLoad({
    url: href,
    dataType: "html",
    success: function (data) {
      hideLoader();
      $("#modal_riwayat .modal-content").parents(".modal").modal("show");
    },
    error: function () {
      hideLoader();
    },
  });
});
