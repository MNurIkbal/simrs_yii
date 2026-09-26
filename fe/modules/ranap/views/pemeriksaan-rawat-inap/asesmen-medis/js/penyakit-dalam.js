$(document).ready(function () {
  setPernahDirawat(true);
  $(".keteranganRiwayatAlergi").prop("readonly", true);
  $(".pembesaranKelenjarLainnya").prop("readonly", true);
  $(".kakuDudukLainnya").prop("readonly", true);
  $(".thoraxLainnya").prop("readonly", true);
  $(".rinchiLainnya").prop("readonly", true);
  $(".wheezingLainnya").prop("readonly", true);
  $(".nyeriTekananLokasi").prop("readonly", true);
  $(".udemLainnya").prop("readonly", true);
  $(".lokasiNyeri").prop("readonly", true);
  $(".intensitasNyeri").prop("readonly", true);
  $(".selectMetode, .selectDokter").select2();

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
  $("#tensi").on("input", function () {
    validateInput(this);
  });
  function toggleRujukan() {
    const value = $("input[name='PenyakitDalamForm[isRujukan]']:checked").val();
    $("#rujukan-ya, #rujukan-tidak").hide();

    if (value === "1") {
      $("#rujukan-ya").show();
      toggleRujukanYa();
    } else if (value === "0") {
      $("#rujukan-tidak").show();
      toggleRujukanTidak();
    }
  }
  function toggleRujukanYa() {
    const fields = {
      1: "#penyakitdalamform-keteranganrs",
      2: "#penyakitdalamform-keteranganpuskesmas",
      3: "#penyakitdalamform-keterangandokter",
      4: "#penyakitdalamform-keteranganlainnya",
    };
    const fieldsTidak = {
      1: "#penyakitdalamform-keterangandatangsendiri",
      2: "#penyakitdalamform-keterangandiantar",
    };

    function setReadonlyFields(activeKey) {
      if (activeKey) {
        $.each(fields, (key, selector) => {
          const input = $(selector);
          const isActive = key === activeKey;
          input.prop("readonly", !isActive);
          if (!isActive) input.val("");
        });
      } else {
        $.each(fields, (key, selector) => {
          const input = $(selector);
          input.prop("readonly", true).val("");
        });
      }
    }

    const selected = $(".rujukanDari:checked").val();
    setReadonlyFields(selected);

    $(".rujukanDari")
      .off("change")
      .on("change", function () {
        const newVal = $(".rujukanDari:checked").val();
        setReadonlyFields(newVal);
      });

    $.each(fieldsTidak, (key, selector) => {
      const input = $(selector);
      input.prop("readonly", true).val("");
    });

    $(".datangTanpaRujukan").prop("checked", false);
  }

  function toggleRujukanTidak() {
    const fields = {
      1: "#penyakitdalamform-keterangandatangsendiri",
      2: "#penyakitdalamform-keterangandiantar",
    };
    const fieldsYa = {
      1: "#penyakitdalamform-keteranganrs",
      2: "#penyakitdalamform-keteranganpuskesmas",
      3: "#penyakitdalamform-keterangandokter",
      4: "#penyakitdalamform-keteranganlainnya",
    };

    function setReadonlyFields(activeKey) {
      if (activeKey) {
        $.each(fields, (key, selector) => {
          const input = $(selector);
          const isActive = key === activeKey;
          input.prop("readonly", !isActive);
          if (!isActive) input.val("");
        });
      } else {
        $.each(fields, (key, selector) => {
          const input = $(selector);
          input.prop("readonly", true).val("");
        });
      }
    }

    const selected = $(".datangTanpaRujukan:checked").val();
    setReadonlyFields(selected);

    $(".datangTanpaRujukan")
      .off("change")
      .on("change", function () {
        const newVal = $(".datangTanpaRujukan:checked").val();
        setReadonlyFields(newVal);
      });

    $.each(fieldsYa, (key, selector) => {
      const input = $(selector);
      input.prop("readonly", true).val("");
    });

    $(".rujukanDari").prop("checked", false);
  }

  function toggleKeteranganAlergi() {
    var value = $(
      "input[name='PenyakitDalamForm[isRiwayatAlergi]']:checked"
    ).val();
    if (value === "1") {
      $(".keteranganRiwayatAlergi").prop("readonly", false);
    } else {
      $(".keteranganRiwayatAlergi").prop("readonly", true).val("");
    }
  }
  function togglePernahDirawat() {
    var value = $(
      "input[name='PenyakitDalamForm[isPernahDirawat]']:checked"
    ).val();

    if (value === "1") {
      setPernahDirawat(false);
    } else if (value === "0") {
      setPernahDirawat(true);
    }
  }
  function setPernahDirawat(isActive) {
    if (isActive) {
      $("#isPernahDirawatKapan").prop("readonly", isActive).val("");
      $("#isPernahDirawatDimana").prop("readonly", isActive).val("");
      $("#isPernahDirawatDiagnosa").prop("readonly", isActive).val("");
    } else {
      $("#isPernahDirawatKapan")
        .prop("readonly", isActive)
        .val(isPernahDirawatKapan);
      $("#isPernahDirawatDimana")
        .prop("readonly", isActive)
        .val(isPernahDirawatDimana);
      $("#isPernahDirawatDiagnosa")
        .prop("readonly", isActive)
        .val(isPernahDirawatDiagnosa);
    }
  }
  function togglePembesaranKelenjar() {
    var value = $(
      "input[name='PenyakitDalamForm[pembesaranKelenjar]']:checked"
    ).val();
    if (value === "1") {
      $(".pembesaranKelenjarLainnya").prop("readonly", false);
    } else {
      $(".pembesaranKelenjarLainnya").prop("readonly", true).val("");
    }
  }
  function toggleKakuDuduk() {
    var value = $("input[name='PenyakitDalamForm[kakuDuduk]']:checked").val();
    if (value === "1") {
      $(".kakuDudukLainnya").prop("readonly", false);
    } else {
      $(".kakuDudukLainnya").prop("readonly", true).val("");
    }
  }
  function toggleThorax() {
    var value = $("input[name='PenyakitDalamForm[thorax]']:checked").val();
    if (value === "2") {
      $(".thoraxLainnya").prop("readonly", false);
    } else {
      $(".thoraxLainnya").prop("readonly", true).val("");
    }
  }
  function toggleRinchi() {
    var value = $("input[name='PenyakitDalamForm[rinchi]']:checked").val();
    if (value === "1") {
      $(".rinchiLainnya").prop("readonly", false);
    } else {
      $(".rinchiLainnya").prop("readonly", true).val("");
    }
  }
  function toggleWheezing() {
    var value = $("input[name='PenyakitDalamForm[wheezing]']:checked").val();
    if (value === "1") {
      $(".wheezingLainnya").prop("readonly", false);
    } else {
      $(".wheezingLainnya").prop("readonly", true).val("");
    }
  }
  function toggleNyeriTekanan() {
    var value = $(
      "input[name='PenyakitDalamForm[nyeriTekanan]']:checked"
    ).val();
    if (value === "1") {
      $(".nyeriTekananLokasi").prop("readonly", false);
    } else {
      $(".nyeriTekananLokasi").prop("readonly", true).val("");
    }
  }
  function toggleUdem() {
    var value = $("input[name='PenyakitDalamForm[udem]']:checked").val();
    if (value === "1") {
      $(".udemLainnya").prop("readonly", false);
    } else {
      $(".udemLainnya").prop("readonly", true).val("");
    }
  }
  function togglePenilaianNyeri() {
    var value = $("input[name='PenyakitDalamForm[isNyeri]']:checked").val();
    if (value === "1") {
      $(".lokasiNyeri").prop("readonly", false);
      $(".intensitasNyeri").prop("disabled", false);
      $(".intensitasNyeri").prop("readonly", false);
    } else {
      $(".lokasiNyeri").prop("readonly", true).val("");
      $(".intensitasNyeri").prop("disabled", true);
      $(".intensitasNyeri").prop("readonly", true).val("");
    }
  }
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[isRujukan]']",
    toggleRujukan
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[isRiwayatAlergi]']",
    toggleKeteranganAlergi
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[isPernahDirawat]']",
    togglePernahDirawat
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[pembesaranKelenjar]']",
    togglePembesaranKelenjar
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[thorax]']",
    toggleThorax
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[kakuDuduk]']",
    toggleKakuDuduk
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[rinchi]']",
    toggleRinchi
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[wheezing]']",
    toggleWheezing
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[nyeriTekanan]']",
    toggleNyeriTekanan
  );
  $(document).on(
    "change",
    "input[name='PenyakitDalamForm[isNyeri]']",
    togglePenilaianNyeri
  );
  $(document).on("change", "input[name='PenyakitDalamForm[udem]']", toggleUdem);
  $(document).on("change", ".intensitasNyeri", function () {
    const val = parseInt(this.value, 10);
    if (val < 0 || val > 10) {
      docoNotification("error", "Peringatan!", "Nilai harus antara 0 dan 10");
      $(this).val(0);
    }
  });

  $(".riwayatPenyakitDahulu-radio")
    .change(function () {
      const isLainLainChecked = $(
        '.riwayatPenyakitDahulu-radio[value="11"]'
      ).is(":checked");

      if (isLainLainChecked) {
        $("#riwayatPenyakitDahuluLainnya").prop("readonly", false);
      } else {
        $("#riwayatPenyakitDahuluLainnya").prop("readonly", true).val("");
      }
    })
    .trigger("change");

  $(".riwayatPenyakitKeluarga-radio")
    .change(function () {
      const isLainLainChecked = $(
        '.riwayatPenyakitKeluarga-radio[value="5"]'
      ).is(":checked");

      if (isLainLainChecked) {
        $("#riwayatPenyakitKeluargaLainnya").prop("readonly", false);
      } else {
        $("#riwayatPenyakitKeluargaLainnya").prop("readonly", true).val("");
      }
    })
    .trigger("change");

  $(".riwayatPenyakitSosial-radio")
    .change(function () {
      const isLainLainChecked = $('.riwayatPenyakitSosial-radio[value="3"]').is(
        ":checked"
      );
      if (isLainLainChecked) {
        $("#riwayatPenyakitSosialLainnya").prop("readonly", false);
      } else {
        $("#riwayatPenyakitSosialLainnya").prop("readonly", true).val("");
      }
    })
    .trigger("change");

  function appendNewObat() {
    const $table = $(`.table-obat`);
    const $tbody = $table.find("tbody");
    const namaObat = $(".namaObat").val() || "";
    const dosis = $(".dosis").val() || "";
    const waktuPenggunaan = $(".waktuPenggunaan").val() || "";

    const newRow = `
            <tr>
              <td><input type="text" name="PenyakitDalamForm[namaObat][]" class="${_classForm} input-margin" value="${namaObat}"></td>
              <td><input type="text" name="PenyakitDalamForm[dosis][]" class="${_classForm} input-margin" value="${dosis}"></td>
              <td><input type="text" name="PenyakitDalamForm[waktuPenggunaan][]" class="${_classForm} input-margin" value="${waktuPenggunaan}"></td>
              <td style="text-align: center;">
                <button type="button" class="btn btn-danger deleteRowObat"><i class="fa fa-trash"></i></button>
              </td>
            </tr>
          `;

    $tbody.append(newRow);

    $(".namaObat").val("");
    $(".dosis").val("");
    $(".waktuPenggunaan").val("");

    $tbody
      .find(".deleteRowObat")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
      });
  }

  function appendExistingObat() {
    const $table = $(`.table-obat`);
    const $tbody = $table.find("tbody");
    const generateRow = (namaObat = [], dosis = [], waktuPenggunaan = []) => {
      return `
              <tr>
                  <td><input type="text" name="PenyakitDalamForm[namaObat][]" class="${_classForm} input-margin" value="${namaObat}"></td>
                  <td><input type="text" name="PenyakitDalamForm[dosis][]" class="${_classForm} input-margin" value="${dosis}"></td>
                  <td><input type="text" name="PenyakitDalamForm[waktuPenggunaan][]" class="${_classForm} input-margin" value="${waktuPenggunaan}"></td>
                  <td style="text-align: center;">
                  <button type="button" class="btn btn-danger deleteRowObat"><i class="fa fa-trash"></i></button>
                  </td>
              </tr>
              `;
    };

    const existingRows = $tbody.find("tr").length - 1;
    let count = existingRows;

    if (namaObat && namaObat.length > 0) {
      namaObat.forEach((val, index) => {
        if (val) {
          count++;
          const row = generateRow(val, dosis[index], waktuPenggunaan[index]);
          $tbody.append(row);
        }
      });
    }

    $tbody
      .find(".deleteRowObat")
      .off("click")
      .on("click", function () {
        $(this).closest("tr").remove();
      });
  }

  $("table tbody").on("click", ".addRowObat", function () {
    appendNewObat();
  });
  toggleRujukan();
  toggleKeteranganAlergi();
  togglePembesaranKelenjar();
  toggleKakuDuduk();
  toggleThorax();
  toggleRinchi();
  toggleWheezing();
  toggleNyeriTekanan();
  toggleUdem();
  togglePenilaianNyeri();
  togglePernahDirawat();
  appendExistingObat();
});
