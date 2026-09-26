var _modelForm = "KesehatanAnakForm";

function deleteRow(element) {
  $(element).closest("tr").remove();
}

function dateInitialize(element) {
  $(element).datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayHighlight: true,
    orientation: "bottom",
  });
}

function capitalizeString(word) {
  if (!word) return "";
  return word.charAt(0).toUpperCase() + word.slice(1);
}

$(document).ready(function () {
  $(".sampaiUmur").prop("readonly", true);
  $.fn.modal.Constructor.prototype.enforceFocus = function () {};
  $("td").each(function () {
    var colDiv = $(this).find(".col-sm-7");
    if (colDiv.length && $(this).closest("table").is(".table-ortu")) {
      colDiv.removeClass("col-sm-7").addClass("col-sm-12");
    }
  });
  // lingkar kepala
  $("table tbody").on("click", ".addRowLingkarKepala", function () {
    appendNewLingkarTubuh("kepala");
  });

  if (_lingkarKepalaTanggal) {
    appendExistingLingkar(
      "kepala",
      _lingkarKepalaTanggal,
      _lingkarKepalaUkuran
    );
  }
  // end lingkar kepala

  // lingkar dada
  $("table tbody").on("click", ".addRowLingkarDada", function () {
    appendNewLingkarTubuh("dada");
  });

  if (_lingkarDadaTanggal) {
    appendExistingLingkar("dada", _lingkarDadaTanggal, _lingkarDadaUkuran);
  }
  // end lingkar dada

  // lingkar perut
  $("table tbody").on("click", ".addRowLingkarPerut", function () {
    appendNewLingkarTubuh("perut");
  });

  if (_lingkarPerutTanggal) {
    appendExistingLingkar("perut", _lingkarPerutTanggal, _lingkarPerutUkuran);
  }
  // end lingkar perut

  // table anak
  $("table tbody").on("click", ".addRowAnak", function () {
    appendNewAnak();
  });

  if (_sex) {
    appendExistingAnak();
  }

  // diagnosa
  $("table tbody").on("click", ".addRowDiagnosa", function () {
    appendNewDiagnosa();
  });

  if (_diagnosaNama) {
    appendExistingDiagnosa();
  }

  $("#kesehatananakform-diagnosanama").on("change", function () {
    let diagnosaKodeText = "";
    const selectedText = $(this).find("option:selected").text();
    const selectedTextArray = selectedText.split(" - ");
    const selectedTextValue = selectedTextArray[0];
    if (selectedTextArray.length == 2) {
      diagnosaKodeText = selectedTextValue;
    }

    $(".diagnosaKode").text(diagnosaKodeText);
  });

  function appendExistingAnak() {
    const $table = $(`.table-anak`);
    const $tbody = $table.find("tbody");

    const generateRow = (
      no,
      sex = [],
      umur = [],
      sehatSakit = [],
      karena = []
    ) => {
      return `
            <tr>
              <td class="${_classCenter}">${no}</td>
              <td><input type="text" name="${_modelForm}[sex][]" class="${_classForm} input-margin" value="${sex}"></td>
              <td><input type="text" name="${_modelForm}[umur][]" class="${_classForm} input-margin" value="${umur}"></td>
              <td><input type="text" name="${_modelForm}[sehatSakit][]" class="${_classForm} input-margin" value="${sehatSakit}"></td>
              <td><input type="text" name="${_modelForm}[karena][]" class="${_classForm} input-margin" value="${karena}"></td>
              <td style="text-align: center;">
                <button type="button" class="btn btn-danger deleteRowAnak"><i class="fa fa-trash"></i></button>
              </td>
            </tr>
          `;
    };

    const existingRows = $tbody.find("tr").length - 1;
    let count = existingRows;

    if (_sex && _sex.length > 0) {
      _sex.forEach((val, index) => {
        if (val) {
          count++;
          const row = generateRow(
            count,
            val,
            _umur[index],
            _sehatSakit[index],
            _karena[index]
          );
          $tbody.append(row);
        }
      });
    }

    $tbody
      .find(".deleteRowAnak")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
        reindexRows();
      });
  }

  function appendNewAnak() {
    const $table = $(".table-anak");
    const $tbody = $table.find("tbody");

    const sex = $(".sex").val() || "";
    const umur = $(".umur").val() || "";
    const sehatSakit = $(".sehatSakit").val() || "";
    const karena = $(".karena").val() || "";
    const rowCount = $tbody.find("tr").length;

    const newRow = `
      <tr>
        <td class="${_classCenter}">${rowCount}</td>
        <td><input type="text" name="${_modelForm}[sex][]" class="${_classForm} input-margin" value="${sex}"></td>
        <td><input type="text" name="${_modelForm}[umur][]" class="${_classForm} input-margin" value="${umur}"></td>
        <td><input type="text" name="${_modelForm}[sehatSakit][]" class="${_classForm} input-margin" value="${sehatSakit}"></td>
        <td><input type="text" name="${_modelForm}[karena][]" class="${_classForm} input-margin" value="${karena}"></td>
        <td style="text-align: center;">
          <button type="button" class="btn btn-danger deleteRowAnak"><i class="fa fa-trash"></i></button>
        </td>
      </tr>
    `;

    $tbody.append(newRow);
    $(".sex, .umur, .sehatSakit, .karena").val("");
    $tbody
      .find(".deleteRowAnak")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
        reindexRows();
      });
  }

  function appendNewLingkarTubuh(element) {
    const $table = $(`.table-lingkar-${element}`);
    const $tbody = $table.find("tbody");
    const _element = capitalizeString(element);
    const tanggalName = `lingkar${_element}Tanggal`;
    const ukuranName = `lingkar${_element}Ukuran`;

    const tanggal = $(`.${tanggalName}`).val() || "";
    const ukuran = $(`.${ukuranName}`).val() || "";

    const newRow = `
      <tr>
        <td><input type="text" name="${_modelForm}[${tanggalName}][]" class="${_classForm} ${tanggalName} input-margin" value="${tanggal}"></td>
        <td><input type="text" name="${_modelForm}[${ukuranName}][]" class="${_classForm} input-margin" value="${ukuran}"></td>
        <td style="text-align: center;">
          <button type="button" class="btn btn-danger deleteRowLingkar"><i class="fa fa-trash"></i></button>
        </td>
      </tr>
    `;

    $tbody.append(newRow);

    $(`#${tanggalName}_0`).val("");
    $(`.${ukuranName}`).val("");

    dateInitialize($tbody.find(`.${tanggalName}`));

    $tbody
      .find(".deleteRowLingkar")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
      });
  }

  function appendExistingLingkar(element, tanggal, ukuran) {
    const $table = $(`.table-lingkar-${element}`);
    const $tbody = $table.find("tbody");
    const _element = capitalizeString(element);
    const tanggalName = `lingkar${_element}Tanggal`;
    const ukuranName = `lingkar${_element}Ukuran`;

    const generateRow = (tanggal = [], ukuran = []) => {
      return `
        <tr>
            <td><input type="text" name="${_modelForm}[${tanggalName}][]" class="${_classForm} ${tanggalName} input-margin" value="${tanggal}"></td>
            <td><input type="text" name="${_modelForm}[${ukuranName}][]" class="${_classForm} input-margin" value="${ukuran}"></td>
            <td style="text-align: center;">
            <button type="button" class="btn btn-danger deleteRowLingkar"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
        `;
    };

    const existingRows = $tbody.find("tr").length - 1;
    let count = existingRows;

    if (tanggal && tanggal.length > 0) {
      tanggal.forEach((val, index) => {
        if (val) {
          count++;
          const row = generateRow(val, ukuran[index]);
          $tbody.append(row);
        }
      });
    }

    dateInitialize($tbody.find(`.${tanggalName}`));

    $tbody
      .find(".deleteRowLingkar")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
        const rows = $tbody.find("tr");
        rows.each(function (index) {
          if (index === 0) return;
          $(this).find("td:first").text(index);
        });
      });
  }

  function appendNewDiagnosa() {
    const $table = $(".table-diagnosa");
    const $tbody = $table.find("tbody");
    const _diagnosa = $("#kesehatananakform-diagnosanama").val();
    const selectedTextArray = _diagnosa.split(" - ");
    const selectedTextValue = selectedTextArray[0];
    const selectedTextLabel = selectedTextArray[1];

    const newRow = `
      <tr>
        <td>
            <span class="namaDiagnosa input-margin">${selectedTextLabel}</span>
            <input
                type="hidden"
                name="${_modelForm}[diagnosaNama][]"
                value="${selectedTextLabel}">
        </td>
        <td>
            <span class="kodeDiagnosa input-margin">${selectedTextValue}</span>
            <input
                type="hidden"
                name="${_modelForm}[diagnosaKode][]"
                value="${selectedTextValue}">
        </td>
        <td style="text-align: center;">
          <button type="button" class="btn btn-danger deleteRowDiagnosa" style="margin-bottom: 10px;">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    `;

    $tbody.append(newRow);
    $table
      .find("tbody")
      .find("#kesehatananakform-diagnosanama")
      .val(null)
      .trigger("change");

    $tbody
      .find(".deleteRowDiagnosa")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
      });
  }

  function appendExistingDiagnosa() {
    const $table = $(`.table-diagnosa`);
    const $tbody = $table.find("tbody");
    const generateRow = (diagnosaNama = [], diagnosaKode = []) => {
      return `
        <tr>
            <td>
                <span class="namaDiagnosa input-margin">${diagnosaNama}</span>
                <input
                    type="hidden"
                    name="${_modelForm}[diagnosaNama][]"
                    value="${diagnosaNama}">
            </td>
            <td>
                <span class="kodeDiagnosa input-margin">${diagnosaKode}</span>
                <input
                    type="hidden"
                    name="${_modelForm}[diagnosaKode][]"
                    value="${diagnosaKode}">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger deleteRowDiagnosa" style="margin-bottom: 10px;">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
        `;
    };

    if (_diagnosaNama && _diagnosaNama.length > 0) {
      const filteredDiagnosa = _diagnosaNama.filter(
        (nama) => nama.trim() !== ""
      );

      filteredDiagnosa.forEach((val, index) => {
        const row = generateRow(val, _diagnosaKode[index]);
        $tbody.append(row);
      });
    }

    $tbody
      .find(".deleteRowDiagnosa")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
      });
  }

  function reindexRows() {
    $(".table-anak tbody tr").each(function (index) {
      if (index === 0) return;
      $(this).find("td:first").text(index);
    });
  }
  function toggleAsi() {
    var value = $("input[name='KesehatanAnakForm[asi]']:checked").val();
    if (value === "1") {
      $(".sampaiUmur").prop("readonly", false);
    } else {
      $(".sampaiUmur").prop("readonly", true).val("");
    }
  }
  $(document).on("change", "input[name='KesehatanAnakForm[asi]']", toggleAsi);
  toggleAsi();
});
