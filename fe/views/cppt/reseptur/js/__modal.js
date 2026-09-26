var countTemplate = 0;
relatedResepTempId = 0;
relatedResepTempName = "";
auto_rke = 1;
dataSigna = [];
dataHariOptions = [];
listTempObatTidakTersedia = [];

for (var i = 1; i <= 31; i++) {
  let dataOptions = {
    id: i,
    text: i,
  };
  dataHariOptions.push(
    new Option(dataOptions.text, dataOptions.id, false, false)
  );
}

var _configObatAlkesSelect = {
  placeholder: "Pilih Obat ... ",
  parent: ".tabel-reseptur-container",
  _api: "/rajal/allow/list-obat-alkes-depo",
  ajax: {
    delay: 800,
    data: function (params) {
      var non_racikan = 0;
      var group_jenisobat = null;
      if ($(this).attr("id") == "obatalkes_id") {
        non_racikan = 1;
        group_jenisobat = $(
          "#generalresepturnrdetailform-group_jenisobat input:checked"
        ).val();
      }
      return {
        q: params.term,
        page: params.page || 1,
        ruangan_id: $("#select_ruangan").val(),
        penjamin_id: $("#penjamin_id").val(),
        group_jenisobat: group_jenisobat,
        kelaspelayanan_id: kelaspelayanan_id,
        kelastagihan_id: kelastagihan_id,
        is_others: is_others,
        non_racikan: non_racikan,
        groupJenisobat: groupJenisobat,
      };
    },
    results: function (data, params) {
      var more = params.page * 30 < data.total_count;
      return { results: data.items, more: more };
    },
    processResults: function (res, params) {
      params.page = params.page || 1;
      var arr = [];
      var limit = typeof res.limit == "undefined" ? 10 : res.limit;
      $.each(res.data_stok, function (index, value) {
        if (index < limit) {
          var _disabled = allowZeroStock
            ? false
            : value.qty_tersedia <= 0
            ? true
            : false;

          if (value.obatalkes_id == 0) {
            _disabled = false;
          }

          arr.push({
            id: value.obatalkes_id,
            text: value.obatalkes_nama,
            disabled: _disabled,
          });

          let data = [];
          let response = res.data_stok;
          for (var i in response) {
            data.push({
              id: response[i].obatalkes_id,
              text: response[i].obatalkes_nama,
            });
            apotek.list_stok[response[i].obatalkes_id] = response[i];
          }
        }
      });
      return {
        results: arr,
        pagination: {
          more: res.data_stok.length > limit,
        },
      };
    },
  },
};

var _configObatAlkesSelectNew = {
  placeholder: "Pilih Obat ... ",
  parent: ".tabel-reseptur-container",
  _api: "/rajal/allow/get-data-select2", // get data
  ajax: {
    delay: 800,
    data: function (params) {
      var non_racikan = 0;
      var group_jenisobat = null;
      if ($(this).attr("id") == "obatalkes_id") {
        non_racikan = 1;
        group_jenisobat = $(
          "#generalresepturnrdetailform-group_jenisobat input:checked"
        ).val();
      }
      return {
        payload: {
          ...params,
          ruangan_id: $("#select_ruangan").val(),
          penjamin_id: $("#penjamin_id").val(),
          group_jenisobat: group_jenisobat,
          kelaspelayanan_id: kelaspelayanan_id,
          kelastagihan_id: kelastagihan_id,
          is_others: is_others,
          non_racikan: non_racikan,
          groupJenisobat: groupJenisobat,
          instalasi_id: instalasiId,
        },
      };
    },
    results: function (data, params) {
      var more = params.page * 30 < data.total_count;
      return { results: data.items, more: more };
    },
    processResults: function (res, params) {
      params.page = params.page || 1;
      var arr = [];
      var limit = typeof res.limit == "undefined" ? 10 : res.limit;
      $.each(res.data, function (index, value) {
        if (index < limit) {
          arr.push({
            id: value.id,
            text: value.text,
            // disabled: _disabled
          });
        }
      });
      return {
        results: arr,
        pagination: {
          more: true,
        },
      };
    },
  },
};

var _configSignaReseptur = {
  placeholder: "Pilih Signa ...",
  parent: ".tabel-reseptur-container",
  _api: "/rajal/allow/get-list-signa",
  ajax: {
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
      };
    },
    results: (data, params) => {
      var more = params.page * 30 < data.total_count;
      return { results: data.items, more: more };
    },
    processResults: function (res, params) {
      params.page = params.page || 1;
      var arr = [];
      $.each(res.data.signa, function (index, value) {
        if (index < res.data.limit) {
          arr.push({
            id: value.signa_id,
            text: `${value.signa_kode != null ? value.signa_kode : ""} ${
              value.signa_nama
            }`,
            html: `${
              value.signa_kode != null ? `<b>${value.signa_kode}</b>` : ""
            } ${value.signa_nama}`,
          });
          dataSigna[value.signa_id] = value;
        }
      });
      return {
        results: arr,
        pagination: {
          more: res.data.signa.length > res.data.limit,
        },
      };
    },
  },
};

$(() => {
  $("#generalresepturform-pegawai_id").docoPaginationSelec2(
    (config = {
      placeholder: "Pilih",
      _api: $("#generalresepturform-pegawai_id").data("api"),
      ajax: {
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1,
            attr: {
              ruangan_id: ruanganreseptur_id,
            },
          };
        },
        results: function (data, params) {
          var more = params.page * 30 < data.total_count;
          return { results: data.items, more: more };
        },
        processResults: function (res, params) {
          params.page = params.page || 1;
          var arr = [];
          $.each(res.data, function (index, value) {
            arr.push({
              id: value.pegawai_id,
              text: value.nama_pegawai,
            });
          });
          return {
            results: arr,
            pagination: {
              more: res.data.length >= 10,
            },
          };
        },
      },
    })
  );
  $("#generalresepturform-pegawai_id").on("select2:select", function (e) {
    loadTemplate();
  });

  $(document).on("select2:close", ".select2-selffocus", function () {
    $(this).show(); // mengatasi select2 kraje yang select2nya hidden
    $(this).focus();
  });

  $('#order-reseptur-form input[type="checkbox"]').on(
    "keypress",
    function (event) {
      if (event.which === 13) {
        this.checked = !this.checked;
      }
    }
  );

  $(document).ready(function () {
    // konfig validasi stok obat alkes
    if (konfigStokObatAlkes == "true") {
      $("#div_stok_tersedia_racikan").show();
      $("#div_stok_tersedia").show();
    } else {
      $("#div_stok_tersedia_racikan").hide();
      $("#div_stok_tersedia").hide();
    }

    $(".modal-body")
      .find("#select_ruangan")
      .select2({
        dropdownParent: $("#order-reseptur-form"),
      });
    appendObat();
    autoRke(auto_rke);

    $(".bb_tb").on("change", () => {
      var bb = parseFloat(docoHelper.convertToAngka($("#berat_badan").val()));
      var tb = parseFloat(docoHelper.convertToAngka($("#tinggi_badan").val()));

      if (bb != "" && tb != "") {
        var mosteller = Math.sqrt((Number(bb) * Number(tb)) / 3600);

        $("#luas_tubuh").val(docoHelper.convertToRupiah(mosteller.toFixed(2)));
      }
    });

    $(".bb_tb").trigger("change");

    $('input[name="kategori_resep"]').on("change", function () {
      $('input[name="kategori_resep"]').not(this).prop("checked", false);

      let value = $(this).val();
      if (parseInt(value) == kategori_udd) {
        if ($(this).prop("checked") == true) {
          confirmationDialog(
            "Apakah anda yakin memilih Resep UDD? Resep ini akan otomatis terbentuk dihari esok",
            (isAccept) => {
              if (!isAccept) {
                $(this).prop("checked", false);
              }
            }
          );
        }
      }
    });
  });
});

function autoRke(count) {
  $("#rke").val(count);
}
function appendObat(data = []) {
  var no = 0;
  var newRow = "";

  $.each(data, function (x, y) {
    let checkKronis = y.is_kronis
      ? "<span class='text-is-kronis' style='font-weight: bold; font-size: 12pt;' data-value='" +
        y.is_kronis +
        "'>&check;</span>"
      : "";
    let checkboxKronis =
      "<div class='form-group'><input type='checkbox' class='input-is-kronis form-control'></div>";

    if (typeof data[x] !== "undefined" && data !== "") {
      var jumlah_harga = y.hargasatuan_reseptur * y.qty_reseptur;
      no++;
      newRow += "<tr data-obatalkes=" + y.obatalkes_id + ">";
      let btnEdit =
        '<button class="btn btn-sm btn-primary edit-obat" type=\'button\' data-pos="' +
        x +
        '"><i class="fa fa-pencil"></i></button>';
      let btnConfirmEdit =
        '<button class="btn btn-sm btn-success confirm-edit-obat" type=\'button\' data-pos="' +
        x +
        '"><i class="fa fa-check"></i></button>';
      let btnCancelEdit =
        '<button class="btn btn-sm btn-danger close-edit-obat" type=\'button\' data-pos="' +
        x +
        '"><i class="fa fa-close"></i></button>';
      let catatanInput =
        "<div class='form-group'><textarea class='input-catatan form-control' placeholder='Catatan'></textarea></div>";
      let qtyInput =
        "<div class='form-group' style='min-width: 50px;'><input type='text' class='input-qty form-control doco-decimal' placeholder='Qty'></div>";
      let kebutuhanInput =
        "<div class='form-group' style='min-width: 50px;'><input type='text' class='input-kebutuhan form-control doco-decimal' placeholder='Kebutuhan'></div>";
      let divRacikan =
        "<div class='input-div-racikan form-group hidden'>" +
        "<input type='text' class='input-nama-racikan form-control' placeholder='Nama racikan'>" +
        "<input type='text' class='input-qty-racikan form-control doco-decimal' placeholder='Qty racikan'>" +
        "<select class='form-control input-satuan-racikan'></select></div>";
      let signaInput =
        "<div class='form-group'><select class='form-control input-signa docoSelect2SignaFormatOnly'></select></div>";
      let hariInput =
        "<div class='form-group' style='min-width: 50px;'><select class='form-control input-hari'></select></div>";
      let namaObatInput =
        "<div class='form-group'><select class='form-control input-obat'></select></div>";
      let stokTersedia =
        "<div class='form-group' style='min-width: 50px;'><input type='text' class='input-stok form-control doco-decimal' placeholder='Stok Tersedia' readonly></div>";
      if (y.racikan_id != "OR") {
        if (y.detail_type == "racikan_freetext") {
          let pilihanRacikan =
            "<select class='input-racikan'><option value='NR' selected>Non Racikan</option></select>";
          let monRacikanTextArea =
            "<div class='form-group'><textarea class='input-racikan-freetext form-control' placeholder='Catatan'></textarea></div>";
          newRow +=
            "<td><input type='checkbox' class='check-template' value='" +
            y.racikan_text +
            "'data-racikan-id='" +
            y.racikan_id +
            "'></td>";
          newRow +=
            '<td align="right">' +
            "<div class='text-list-resep'><button class=\"btn btn-sm btn-danger delete-obat\" type='button' data-pos=\"" +
            x +
            '"><i class="fa fa-trash"></i></button>' +
            btnEdit +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            btnConfirmEdit +
            btnCancelEdit +
            "</div>" +
            "</td>";
          newRow +=
            "<td style='display:none;'><input type='hidden' class='list_obat' value='" +
            y.obatalkes_id +
            "'></td>";
          newRow += '<td class="numbering">' + no + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>Racikan</div>" +
            "<div class='input-list-resep hidden'>" +
            pilihanRacikan +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='text-rke'>" + (y.rke == null ? "-" : y.rke) + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>" +
            y.obatalkes_nama +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            y.obatalkes_nama +
            "</div>" +
            "</td>";
          newRow +=
            "<td colspan=\"6\" style='white-space:pre'>" +
            "<div class='text-list-resep'>" +
            y.racikan_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            monRacikanTextArea +
            "</div>" +
            "</td>";
        } else {
          let pilihanRacikan =
            "<select class='input-racikan'><option value='NR' selected>Non Racikan</option></select>";
          let kebutuhanInput =
            "<div class='form-group'><input type='text' class='input-kebutuhan form-control doco-decimal' placeholder='Kebutuhan'></div>";

          newRow +=
            "<td><input type='checkbox' class='check-template' value='" +
            y.obatalkes_id +
            "' data-rke='" +
            y.rke +
            "' data-racikan-id='" +
            y.racikan_id +
            "'></td>";
          newRow +=
            '<td align="right">' +
            "<div class='text-list-resep'><button class=\"btn btn-sm btn-danger delete-obat\" type='button' data-pos=\"" +
            x +
            '"><i class="fa fa-trash"></i></button>' +
            btnEdit +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            btnConfirmEdit +
            btnCancelEdit +
            "</div>" +
            "</td>";
          newRow +=
            "<td style='display:none;'><input type='hidden' class='list_obat' value='" +
            y.obatalkes_id +
            "'></td>";
          newRow += '<td class="numbering">' + no + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>Non-Racikan</div>" +
            "<div class='input-list-resep hidden'>" +
            pilihanRacikan +
            divRacikan +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='text-rke'>" + (y.rke == null ? "-" : y.rke) + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>" +
            y.obatalkes_nama +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            namaObatInput +
            "</div>" +
            "</td>";
          newRow += "<td style='display:none'>" + y.satuaninput_text + "</td>";
          newRow +=
            '<td class="signa">' +
            "<div class='text-list-resep'>" +
            y.signa +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            signaInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="hari">' +
            "<div class='text-list-resep'>" +
            (y.hari != null ? y.hari : " - ") +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            hariInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="qty" align="right">' +
            "<div class='text-list-resep'>" +
            y.qty_reseptur +
            " " +
            y.satuaninput_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            qtyInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="stok-tersedia" align="right">' +
            "<div class='text-list-resep'>" +
            y.stok_sisa +
            " " +
            y.satuaninput_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            stokTersedia +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="kebutuhan" align="right">' +
            "<div class='text-list-resep'>" +
            (y.kebutuhan != null ? y.kebutuhan : "-") +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            kebutuhanInput +
            "</div>" +
            "</td>";
          newRow +=
            "<td align=\"right\" style='display:none'>" +
            docoHelper.convertToRupiah(y.hargasatuan_reseptur) +
            "</td>";
          newRow +=
            '<td class="jumlah_harga" style=\'display:none\' align="right">' +
            docoHelper.convertToRupiah(jumlah_harga) +
            "</td>";
          newRow +=
            '<td class="satuan" align="left">' +
            "<div class='text-list-resep' style='white-space: break-spaces;'>" +
            y.etiket +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            catatanInput +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='flag-kronis'>" +
            "<div class='text-list-resep' align='center'>" +
            checkKronis +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            checkboxKronis +
            "</div>" +
            "</td>";
        }
      } else {
        if (y.detail_type == "racikan_freetext") {
          let pilihanRacikan =
            "<select class='input-racikan'><option value='OR' selected>Racikan</option></select>";
          let racikanTextArea =
            "<div class='form-group'><textarea class='input-racikan-freetext form-control' placeholder='Catatan'></textarea></div>";
          newRow +=
            "<td><input type='checkbox' class='check-template' value='" +
            y.racikan_text +
            "' data-rke='" +
            y.rke +
            "' data-racikan-id='" +
            y.racikan_id +
            "'></td>";
          newRow +=
            '<td align="right">' +
            "<div class='text-list-resep'><button class=\"btn btn-sm btn-danger delete-obat\" type='button' data-pos=\"" +
            x +
            '"><i class="fa fa-trash"></i></button>' +
            btnEdit +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            btnConfirmEdit +
            btnCancelEdit +
            "</div>" +
            "</td>";
          newRow +=
            "<td style='display:none;'><input type='hidden' class='list_obat' value='" +
            y.obatalkes_id +
            "'></td>";
          newRow += '<td class="numbering">' + no + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>Racikan</div>" +
            "<div class='input-list-resep hidden'>" +
            pilihanRacikan +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='text-rke'>" + (y.rke == null ? "-" : y.rke) + "</td>";
          newRow +=
            "<td colspan=\"6\" style='white-space:pre'>" +
            "<div class='text-list-resep'>" +
            y.racikan_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            racikanTextArea +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='flag-kronis'>" +
            "<div class='text-list-resep' align='center'></div>";
          ("</td>");
        } else {
          let pilihanRacikan =
            "<select class='input-racikan'><option value='OR' selected>Racikan</option><option value='NR'>Non-Racikan</option></select>";

          newRow +=
            "<td><input type='checkbox' class='check-template' value='" +
            y.obatalkes_id +
            "' data-rke='" +
            y.rke +
            "' data-racikan-id='" +
            y.racikan_id +
            "'></td>";
          newRow +=
            '<td align="right">' +
            "<div class='text-list-resep'><button class=\"btn btn-sm btn-danger delete-obat\" type='button' data-pos=\"" +
            x +
            '"><i class="fa fa-trash"></i></button>' +
            btnEdit +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            btnConfirmEdit +
            btnCancelEdit +
            "</div>" +
            "</td>";
          newRow +=
            "<td style='display:none;'><input type='hidden' class='list_obat' value='" +
            y.obatalkes_id +
            "'></td>";
          newRow += '<td class="numbering">' + no + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>Racikan <br>" +
            y.nama_racikan +
            " </br> " +
            y.qty_racikan +
            " " +
            y.satuan_racikan_nama +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            pilihanRacikan +
            divRacikan +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='text-rke'>" + (y.rke == null ? "-" : y.rke) + "</td>";
          newRow +=
            "<td>" +
            "<div class='text-list-resep'>" +
            y.obatalkes_nama +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            namaObatInput +
            "</div>" +
            "</td>";
          newRow += "<td style='display:none'>" + y.satuaninput_text + "</td>";
          newRow +=
            '<td class="signa">' +
            "<div class='text-list-resep'>" +
            y.signa +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            signaInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="hari">' +
            "<div class='text-list-resep'>" +
            (y.hari != null ? y.hari : " - ") +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            hariInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="qty" align="right">' +
            "<div class='text-list-resep'>" +
            y.qty_reseptur +
            " " +
            y.satuaninput_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            qtyInput +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="stok-tersedia" align="right">' +
            "<div class='text-list-resep'>" +
            y.stok_sisa +
            " " +
            y.satuaninput_text +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            stokTersedia +
            "</div>" +
            "</td>";
          newRow +=
            '<td class="kebutuhan" align="right">' +
            "<div class='text-list-resep'>" +
            (y.kebutuhan != null ? y.kebutuhan : "-") +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            kebutuhanInput +
            "</div>" +
            "</td>";
          newRow +=
            "<td align=\"right\" style='display:none'>" +
            docoHelper.convertToRupiah(y.hargasatuan_reseptur) +
            "</td>";
          newRow +=
            '<td class="jumlah_harga" style=\'display:none\' align="right">' +
            docoHelper.convertToRupiah(jumlah_harga) +
            "</td>";
          newRow +=
            '<td class="satuan" align="left">' +
            "<div class='text-list-resep' style='white-space: break-spaces;'>" +
            y.etiket +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            catatanInput +
            "</div>" +
            "</td>";
          newRow +=
            "<td class='flag-kronis'>" +
            "<div class='text-list-resep' align='center'>" +
            checkKronis +
            "</div>" +
            "<div class='input-list-resep hidden'>" +
            checkboxKronis +
            "</div>" +
            "</td>";
        }
      }
      newRow += "</tr>";
    }
  });

  if (newRow === "") {
    newRow += "<tr>";
    newRow +=
      '<td colspan="11" id="data-null" class="text-center">Belum ada data yang diinputkan</td>';
    newRow += "</tr>";
  }

  $("#list-temp-obat").html("");
  $("#list-temp-obat").prepend(newRow);
  $(".input-is-kronis").uniform();

  $(".delete-obat").unbind();
  $(".delete-obat").bind("click", function ({ delegateTarget }) {
    var pos = $(delegateTarget).data("pos");
    list_temp_obat.splice(pos, 1);
    var delete_rke = 0;
    for (var i = 0, l = list_temp_obat.length; i < l; i++) {
      if (list_temp_obat[i].rke != null) {
        delete_rke++;
        list_temp_obat[i].rke = list_temp_obat[i].rke;
      }
    }
    appendObat(list_temp_obat);
    rke =
      list_temp_obat[list_temp_obat.length - 1].rke != null
        ? list_temp_obat[list_temp_obat.length - 1].rke
        : 0;
    auto_rke = parseInt(rke) + 1;
    autoRke(auto_rke);
  });

  $(".check-template").bind("change", function ({ delegateTarget }) {
    let listObat = $(".check-template:checked");
    if (listObat.length > 0) {
      if (
        relatedResepTempId != 0 &&
        countTemplate == 1 &&
        pegawai_id == $("#generalresepturform-pegawai_id").val()
      ) {
        $("#btn-update-template").removeClass("disabled");
        $("#btn-update-template").prop("disabled", false);
      }
      $("#btn-popup-template").removeClass("disabled");
      $("#btn-popup-template").prop("disabled", false);
    } else {
      $("#btn-popup-template, #btn-update-template").addClass("disabled");
      $("#btn-popup-template, #btn-update-template").attr("disabled", true);
    }
  });

  $(".udd-checkbox").bind("change", udd_checked);
  $(".edit-obat").bind("click", function ({ delegateTarget }) {
    editObat(delegateTarget);
  });
  $(".confirm-edit-obat").bind("click", function ({ delegateTarget }) {
    confirmObat(delegateTarget);
  });
  $(".close-edit-obat").bind("click", function ({ delegateTarget }) {
    closeObat(delegateTarget);
  });
  $(".input-racikan").select2({
    dropdownParent: $("#div-tabel-reseptur"),
  });
  $(".input-racikan").bind("change", function ({ delegateTarget }) {
    let input = $(delegateTarget);
    let div = $(delegateTarget).closest("tr").find(".input-div-racikan");
    // if(input.val() == "NR") {
    //     div.addClass('hidden')
    // } else {
    //     div.removeClass('hidden')
    // }
  });

  // $('.input-hari').empty();

  $(".input-hari").select2({
    placeholder: "Hari",
    dropdownParent: $("#div-tabel-reseptur"),
  });

  $(".input-hari").append(dataHariOptions).change();
  $(".input-hari").bind("select2:select", function ({ delegateTarget }) {
    let index = $(this).parents("tr").index();
    let thisSigna = dataSigna[$(".input-signa").eq(index).val()];
    thisSigna = thisSigna != undefined ? thisSigna : list_temp_obat[index]; // check if first initiate (if dataSigna empty)
    let qtyObat =
      thisSigna != null
        ? thisSigna.qty_obat != null
          ? thisSigna.qty_obat
          : thisSigna.qty_obat_signa
        : 0;
    let kebutuhan =
      $(".input-kebutuhan").eq(index).val() != null
        ? $(".input-kebutuhan").eq(index).val() != ""
          ? $(".input-kebutuhan").eq(index).val()
          : 1
        : 1;
    let iterasi =
      thisSigna != null
        ? thisSigna.iterasi != null
          ? thisSigna.iterasi
          : 1
        : 1;
    let hari =
      $(".input-hari").eq(index).val() != null
        ? $(".input-hari").eq(index).val()
        : 1;
    let result = parseFloat(qtyObat * iterasi * kebutuhan * hari).toFixed(2);
    result = !isNaN(result) ? result : 0;
    if (!isSignaFreetext(thisSigna)) {
      $(".qty").eq(index).find(".input-qty").val(result).change();
    }
  });

  $(".input-is-kronis").bind("change", function ({ delegateTarget }) {
    let index = $(this).parents("tr").index();
    if ($(".input-is-kronis").eq(index).is(":checked")) {
      if (enable_split_kronis) {
        $(".input-hari").eq(index).val(hari_resep_kronis).change();
      }
    }
    let thisSigna = dataSigna[$(".input-signa").eq(index).val()];
    thisSigna = thisSigna != undefined ? thisSigna : list_temp_obat[index]; // check if first initiate (if dataSigna empty)
    let thisTempObat = list_temp_obat[index];
    let qtyObat =
      thisSigna != null
        ? thisSigna.qty_obat != null
          ? thisSigna.qty_obat
          : thisSigna.qty_obat_signa
        : 0;
    let iterasi =
      thisSigna != null
        ? thisSigna.iterasi != null
          ? thisSigna.iterasi
          : 1
        : 1;
    let kebutuhan =
      $(".input-kebutuhan").eq(index).val() != null
        ? $(".input-kebutuhan").eq(index).val() != ""
          ? $(".input-kebutuhan").eq(index).val()
          : 1
        : null;
    let hari =
      $(".input-hari").eq(index).val() != null
        ? parseFloat($(".input-hari").eq(index).val())
        : 1;
    let result = parseFloat(qtyObat * iterasi * hari * kebutuhan).toFixed(2);
    result = !isNaN(result) ? result : 0;
    if (
      !isSignaFreetext(thisSigna) &&
      $(".input-hari").eq(index).val() != null &&
      $(".input-hari").eq(index).val() != ""
    ) {
      $(".qty").eq(index).find(".input-qty").val(result).change();
    }
  });

  $(".input-racikan").bind("change", function ({ delegateTarget }) {
    let input = $(delegateTarget);
    let div = $(delegateTarget).closest("tr").find(".input-div-racikan");
    let index = $(delegateTarget).parents("tr").index();
    if (input.val() == "NR") {
      $(".kebutuhan").eq(index).find(".input-kebutuhan").val(null).change();
      $(".kebutuhan")
        .eq(index)
        .find(".input-kebutuhan")
        .attr("readonly", true)
        .change();
    } else {
      $(".kebutuhan").eq(index).find(".input-kebutuhan").val(1).change();
      $(".kebutuhan")
        .eq(index)
        .find(".input-kebutuhan")
        .attr("readonly", false)
        .change();
    }
  });

  $(".input-satuan-racikan").docoPaginationSelec2({
    placeholder: "Pilih",
    parent: ".tabel-reseptur-container",
    _api: "/rajal/allow/get-master-unit",
    ajax: {
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
          allowEmpty: false,
        };
      },
      results: (data, params) => {
        var more = params.page * 30 < data.total_count;
        return { results: data.items, more: more };
      },
      processResults: function (res, params) {
        params.page = params.page || 1;
        var arr = [];
        $.each(res.data.satuan, function (index, value) {
          if (index < res.data.limit) {
            arr.push({
              id: value.satuanunit_id,
              text: value.satuanunit_nama,
            });
          }
        });
        return {
          results: arr,
          pagination: {
            more: res.data.satuan.length > res.data.limit,
          },
        };
      },
    },
  });

  $(".input-signa").docoPaginationSelec2(_configSignaReseptur);

  $(".input-signa").bind("select2:select", function ({ delegateTarget }) {
    let index = $(this).parents("tr").index();
    let thisSigna = dataSigna[$(this).val()];
    let thisTempObat = list_temp_obat[index];
    let qtyObat = thisSigna != null ? thisSigna.qty_obat : 0;
    let iterasi =
      thisSigna != null
        ? thisSigna.iterasi != null
          ? thisSigna.iterasi
          : 1
        : 1;
    let hari =
      $(".input-hari").eq(index).val() != null
        ? $(".input-hari").eq(index).val()
        : 1;
    let kebutuhan =
      $(".input-kebutuhan").eq(index).val() != null
        ? $(".input-kebutuhan").eq(index).val() != ""
          ? $(".input-kebutuhan").eq(index).val()
          : 1
        : 1;
    let result = parseFloat(qtyObat * iterasi * kebutuhan * hari).toFixed(2);
    result = !isNaN(result) ? result : 0;
    if (!isSignaFreetext(thisSigna)) {
      $(".input-qty").eq(index).prop("readonly", false);
      $(".qty").eq(index).find(".input-qty").val(result).change();
      if ($(this).closest("tr").find(".input-racikan").val() == "NR") {
        $(".kebutuhan")
          .eq(index)
          .find(".input-kebutuhan")
          .attr("readonly", true);
      } else {
        $(".kebutuhan")
          .eq(index)
          .find(".input-kebutuhan")
          .val(kebutuhan)
          .change();
        $(".kebutuhan")
          .eq(index)
          .find(".input-kebutuhan")
          .attr("readonly", false);
      }
    } else {
      $(".input-qty").eq(index).prop("readonly", false);
    }
  });

  $(".input-obat")
    .docoPaginationSelec2(_configObatAlkesSelectNew)
    .on("change", function (e) {
      let id = $(this).val();
      let inputObat = $(this);
      var group_jenisobat = null;
      if ($(this).attr("id") == "obatalkes_id") {
        non_racikan = 1;
        group_jenisobat = $(
          "#generalresepturnrdetailform-group_jenisobat input:checked"
        ).val();
      }

      if (id) {
        $.ajax({
          data: {
            obatalkes_id: id,
            ruangan_id: $("#select_ruangan").val(),
            penjamin_id: $("#penjamin_id").val(),
            group_jenisobat: group_jenisobat,
            kelaspelayanan_id: kelaspelayanan_id,
            kelastagihan_id: kelastagihan_id,
            is_others: is_others,
            non_racikan:
              inputObat.closest("tr").find(".input-racikan").val() == "NR",
            groupJenisobat: groupJenisobat,
          },
          url: "/rajal/allow/list-obat-alkes-depo",
          dataType: "json",
          success: function (results) {
            selected = results?.data_stok[0];
            let qty = inputObat.closest("tr").find(".input-qty").val();
            let embalanse =
              typeof dataEmbalase !== "undefined" &&
              inputObat.closest("tr").find(".input-racikan").val() == "NR"
                ? Number(dataEmbalase.embalase_nonracikan)
                : 0;

            if (typeof selected !== "undefined") {
              // set data apotek
              apotek.list_stok[selected.obatalkes_id] = selected;

              let jumlah_harga = selected.hargaygdipakai * qty + embalanse;
              inputObat
                .closest("tr")
                .find(".jumlah_harga")
                .html(docoHelper.convertToRupiah(jumlah_harga));
              if (konfigStokObatAlkes == "true") {
                inputObat
                  .closest("tr")
                  .find(".input-stok")
                  .val(selected.qty_tersedia);
              }
            }
          },
          error: function (data) {
            return false;
          },
        });
      }
    });

  $(".input-qty").on("change", function (e) {
    let qty = $(this).val();
    let id = $(this).closest("tr").find(".input-obat").val();
    let selected = apotek.list_stok[id];
    let embalanse =
      typeof dataEmbalase !== "undefined" &&
      $(this).closest("tr").find(".input-racikan").val() == "NR"
        ? Number(dataEmbalase.embalase_nonracikan)
        : 0;
    if (typeof selected !== "undefined") {
      let jumlah_harga = selected.hargaygdipakai * qty + embalanse;
      $(this)
        .closest("tr")
        .find(".jumlah_harga")
        .html(docoHelper.convertToRupiah(jumlah_harga));
    }

    if (
      !e.isTrigger &&
      $(this).closest("tr").find(".input-racikan").val() == "OR"
    ) {
      let index = $(this).parents("tr").index();
      let thisSigna = dataSigna[$(".input-signa").eq(index).val()];
      thisSigna = thisSigna != undefined ? thisSigna : list_temp_obat[index]; // check if first initiate (if dataSigna empty)
      let qtyObat =
        thisSigna != null
          ? thisSigna.qty_obat != null
            ? thisSigna.qty_obat
            : thisSigna.qty_obat_signa
          : 0;
      let iterasi =
        thisSigna != null
          ? thisSigna.iterasi != null
            ? thisSigna.iterasi
            : 1
          : 1;
      let hari =
        $(".input-hari").eq(index).val() != null
          ? $(".input-hari").eq(index).val()
          : 1;
      let kebutuhan = parseFloat(qty / (qtyObat * iterasi * hari)).toFixed(2);
      kebutuhan = !isNaN(kebutuhan) ? kebutuhan : 0;
      $(".kebutuhan")
        .eq(index)
        .find(".input-kebutuhan")
        .val(kebutuhan)
        .change();
    }
  });

  $(".input-kebutuhan").on("change", function (e) {
    if (!e.isTrigger) {
      let index = $(this).parents("tr").index();
      let thisSigna = dataSigna[$(".input-signa").eq(index).val()];
      thisSigna = thisSigna != undefined ? thisSigna : list_temp_obat[index]; // check if first initiate (if dataSigna empty)
      let qtyObat =
        thisSigna != null
          ? thisSigna.qty_obat != null
            ? thisSigna.qty_obat
            : thisSigna.qty_obat_signa
          : 0;
      let iterasi =
        thisSigna != null
          ? thisSigna.iterasi != null
            ? thisSigna.iterasi
            : 1
          : 1;
      let hari =
        $(".input-hari").eq(index).val() != null
          ? $(".input-hari").eq(index).val()
          : 1;
      let kebutuhan =
        $(".input-kebutuhan").eq(index).val() != null
          ? $(".input-kebutuhan").eq(index).val() != ""
            ? $(".input-kebutuhan").eq(index).val()
            : 1
          : 1;
      let result = parseFloat(qtyObat * iterasi * kebutuhan * hari).toFixed(2);
      result = !isNaN(result) ? result : 0;
      if (!isSignaFreetext(thisSigna)) {
        $(".qty").eq(index).find(".input-qty").val(result).change();
      }
    }
  });

  validasiClosePopup();
}

function simpanResepturValidasiKonfig(thisSimpan) {
  var validasi_kategori_resep = validasiKategoriResepRanap();

  if (!validasi_kategori_resep) {
    simpanReseptur(thisSimpan);
  }
}

function validasiKategoriResepRanap() {
  var has_error = 0;

  if (is_ranap && cek_kategori_resep) {
    /* KONDISI VALIDASI KATEGORI RESEP RAWAT INAP */
    var kategori_resep = $(".kategori_resep:checked").val();

    // if(kategori_resep == null) {
    //     docoNotification("error", "Terjadi Kesalahan", "Kategori Resep wajib di isi.");
    //     has_error = 1;
    // } else {
    //     has_error = 0;
    // }
  } else {
    /* KONDISI RESEP RAWAT JALAN DAN RAWAT DARURAT */
    has_error = 0;
  }

  return has_error;
}

function simpanReseptur(thisSimpan) {
  let data_depo = $("#select_ruangan").val();
  let data_iter = $("#iter").val();
  let diagnosa_id = $("#resepturform-diagnosa_id").val();
  let catatan = $("#catatan_reseptur").val();
  let is_hamil = $("input[name='ResepturForm[is_hamil]']:checked").val();
  let berat_badan = $("#berat_badan").val();
  let tinggi_badan = $("#tinggi_badan").val();
  let luas_tubuh = $("#luas_tubuh").val();
  let penjamin_id = $("#penjamin_id").val();
  let header_racikan_id;
  let is_puasa = 0;
  let dokterId = $("#generalresepturform-pegawai_id").val();
  let skipConfirm = true;
  let confirmMessage = "";
  let obatKronis = "<p style='text-align: left; padding-left: 30px;'>";
  let textRacikanNonRacikan;
  let kategori_resep = null;
  if (is_ranap) {
    is_puasa = $("#reminder_puasa:checked").length;

    if (cek_kategori_resep) {
      kategori_resep = $(".kategori_resep:checked").val();
    }
  }

  var is_racikan = false;
  for (var i = 0; i < list_temp_obat.length; i++) {
    if (konfigStokObatAlkes == "true") {
      if (
        parseFloat(list_temp_obat[i].qty_reseptur) >
        parseFloat(list_temp_obat[i].stok_sisa)
      ) {
        docoNotification(
          "warning",
          "Perhatian",
          "Qty tidak boleh melebihi stok tersedia"
        );
        return false;
      }
    }

    textRacikanNonRacikan = "";
    if (list_temp_obat[i].obatalkes_id == 0) {
      list_temp_obat[i].qty_reseptur = 1;
      list_temp_obat[i].hargasatuan_reseptur = 1;
    }
    if (list_temp_obat[i].detail_type == "racikan_freetext") {
      is_racikan = true;
      break;
    }

    if (list_temp_obat[i].is_kronis == true) {
      skipConfirm = false;
      textRacikanNonRacikan =
        list_temp_obat[i].racikan_id == "OR"
          ? "Racikan " + list_temp_obat[i].rke
          : "Non Racikan";
      obatKronis +=
        "<br>" +
        list_temp_obat[i].obatalkes_nama +
        " (" +
        textRacikanNonRacikan +
        ")";
      list_temp_obat[i].is_kronis = 1;
    } else {
      list_temp_obat[i].is_kronis = 0;
    }
  }
  obatKronis += "</p>";

  if (skipConfirm == false) {
    confirmMessage =
      "Apakah Anda yakin ingin menyimpan obat kronis berikut?<br>" + obatKronis;
  }

  if (is_racikan) {
    header_racikan_id = "OR";
  } else {
    header_racikan_id = "NR";
  }

  if (is_ranap && cek_kategori_resep) {
    var data_header = {
      racikan_id: header_racikan_id,
      ruangan_id: data_depo,
      pasien_id: pasien_id,
      pegawai_id: dokterId,
      is_hamil: is_hamil,
      berat_badan: berat_badan,
      tinggi_badan: tinggi_badan,
      luas_tubuh: luas_tubuh,
      pendaftaran_id: pendaftaran_id_origin,
      ruanganreseptur_id: ruanganreseptur_id,
      diagnosa_id: diagnosa_id,
      iter: data_iter,
      catatan: catatan,
      is_puasa: is_puasa,
      kategori_resep: kategori_resep,
      is_ranap: is_ranap,
      penjamin_id: penjamin_id,
      kelaspelayanan_id: kelaspelayanan_id,
    };
  } else {
    var data_header = {
      racikan_id: header_racikan_id,
      ruangan_id: data_depo,
      pasien_id: pasien_id,
      pegawai_id: dokterId,
      is_hamil: is_hamil,
      berat_badan: berat_badan,
      tinggi_badan: tinggi_badan,
      luas_tubuh: luas_tubuh,
      pendaftaran_id: pendaftaran_id_origin,
      ruanganreseptur_id: ruanganreseptur_id,
      diagnosa_id: diagnosa_id,
      iter: data_iter,
      catatan: catatan,
      is_puasa: is_puasa,
      penjamin_id: penjamin_id,
      kelaspelayanan_id: kelaspelayanan_id,
    };
  }
  if (data_depo == "") {
    docoNotification(
      "warning",
      "Peringatan",
      "Depo Tujuan Tidak Boleh Kosong!"
    );
    return false;
  }
  if (list_temp_obat.length > 0) {
    var _url = $("#order-reseptur-form").attr("action");
    $(thisSimpan).docoForm("click", {
      url: _url,
      skipConfirm: skipConfirm,
      confirmMessage: confirmMessage,
      isScrollableConfirm: true,
      data: {
        reseptur_header: data_header,
        list_obat: list_temp_obat,
      },
      success: function (response) {
        const { cppt_id } = response.data ? response.data : response.response;

        $("#modal-reseptur").modal("toggle");
        $("#tb-cppt").DataTable().ajax.reload();

        //RJ
        if (typeof instalasiId != "undefined" && instalasiId == 1) {
          let prevPlanning = $('[name="SoapRjForm[planning]"]').val();
          let planningText =
            prevPlanning + getSuggestPlaningHtml(list_temp_obat);
          let suggestPlanning = [
            {
              name: "SoapRjForm[planning]",
              value: planningText,
            },
          ];
          updateSessionStorage(suggestSoapSessionName, suggestPlanning, {
            isArraySession: true,
            key: "name",
          });
          loadSuggestSOAP(suggestSoapSessionName, { loadSuggest: true });

          // kalo belum ada suggest, set form manual
          if (
            prevPlanning == "" ||
            prevPlanning == null ||
            sessionStorage.getItem(suggestSoapSessionName) == null
          ) {
            $('[name="SoapRjForm[planning]"]').val(planningText);
          }
        }

        if (typeof table_dpjp != "undefined") {
          table_dpjp.draw();
        }

        if (typeof last_cppt != "undefined" && cppt_id != "undefined") {
          last_cppt = cppt_id;
        }
      },
      error: function (res) {
        hideLoader();
        let responseErrorTitle = "Proses Tidak Dapat Dilanjutkan!";
        responseErrorMsg = "Terjadi Kesalahan!";

        if (res.responseText != undefined) {
          let responseJson = JSON.parse(res.responseText);
          if (responseJson.meta != undefined) {
            if (
              responseJson.meta.code != undefined &&
              responseJson.meta.code >= 400 &&
              responseJson.meta.code <= 499
            ) {
              // make sure u used the macroResponseJson helper for return error
              responseErrorTitle =
                responseJson.meta.title != undefined
                  ? responseJson.meta.title
                  : "Proses Tidak Bisa Dilanjutkan!";
              responseErrorMessage =
                responseJson.meta.message != undefined
                  ? responseJson.meta.message
                  : "Formulir tidak dapat diakses!";
              docoNotification(
                "error",
                responseErrorTitle,
                responseErrorMessage
              );
              return false;
            }
          } else {
            if (
              res.status != undefined &&
              res.status >= 400 &&
              res.status <= 499
            ) {
              // make sure u used the responseJson helper for return error
              responseErrorTitle =
                responseJson.response.title != undefined
                  ? responseJson.response.title
                  : "Proses Tidak Bisa Dilanjutkan!";
              responseErrorMessage = responseJson.response;
              if (responseErrorMessage.message != undefined) {
                responseErrorMessage =
                  responseErrorMessage.message != undefined
                    ? responseErrorMessage.message
                    : "Formulir tidak dapat diakses!";
              }
              if (responseErrorMessage.text != undefined) {
                responseErrorMessage =
                  responseErrorMessage.text != undefined
                    ? responseErrorMessage.text
                    : "Formulir tidak dapat diakses!";
              }
              docoNotification(
                "error",
                responseErrorTitle,
                responseErrorMessage
              );
              if (responseJson?.response?.data?.obatalkes_tidak_tersedia) {
                let obatalkesTidakTersedia =
                  responseJson?.response?.data?.obatalkes_tidak_tersedia;
                $("#list-temp-obat tr").removeClass("obatalkes__not-found");
                listTempObatTidakTersedia = [];
                obatalkesTidakTersedia.forEach((item, i) => {
                  listTempObatTidakTersedia = list_temp_obat.filter(
                    (x) => x.obatalkes_id == item
                  );
                  listTempObatTidakTersedia.forEach((item, i) => {
                    let tempObatTidakTersediaIndex = list_temp_obat.findIndex(
                      (x) => x == item
                    );
                    $("#list-temp-obat tr")
                      .eq(tempObatTidakTersediaIndex)
                      .addClass("obatalkes__not-found");
                  });
                });
                $("#list-temp-obat")[0].scrollIntoView();
              }

              if (
                responseJson?.response?.data?.stok_tidak_tersedia != undefined
              ) {
                let obatAlkesId =
                  responseJson?.response?.data?.stok_tidak_tersedia;
                list_temp_obat.forEach((item2) => {
                  var obatalkes_id = item2.obatalkes_id;
                  var foundItem = obatAlkesId.find(
                    (item1) => item1.obatalkes_id === obatalkes_id
                  );
                  if (foundItem) {
                    item2.stok_sisa = foundItem.qty_tersedia;
                  }
                });
                obatAlkesId.forEach((item, i) => {
                  $(
                    "#tabel-reseptur tr[data-obatalkes='" +
                      item.obatalkes_id +
                      "']"
                  ).css("background-color", "#F08080");
                  let qtyLatest =
                    item.qty_tersedia + " " + item.satuankecil_nama;
                  $(
                    "#tabel-reseptur tr[data-obatalkes='" +
                      item.obatalkes_id +
                      "']"
                  )
                    .find("td.stok-tersedia")
                    .attr("data-qtylatest", item.qty_tersedia);
                  $(
                    "#tabel-reseptur tr[data-obatalkes='" +
                      item.obatalkes_id +
                      "']"
                  )
                    .find("td.stok-tersedia .text-list-resep")
                    .text(qtyLatest);
                  $(
                    "#tabel-reseptur tr[data-obatalkes='" +
                      item.obatalkes_id +
                      "']"
                  )
                    .find(
                      "td.stok-tersedia .input-list-resep .form-group .input-stok"
                    )
                    .val(item.qty_tersedia);
                });
              } else {
                responseErrorTitle =
                  responseJson?.response?.data?.stok_tidak_tersedia != undefined
                    ? responseJson.response.title
                    : "Proses Tidak Bisa Dilanjutkan!";
                responseErrorMessage =
                  responseJson?.response?.data?.stok_tidak_tersedia != undefined
                    ? responseJson.response.message
                    : "Formulir tidak dapat diakses!";
                docoNotification(
                  "error",
                  responseErrorTitle,
                  responseErrorMessage
                );
              }
              return false;
            }
          }

          if (responseJson.response != undefined) {
            var _response = responseJson.response;
            if (_response.failed != undefined) {
              responseErrorTitle =
                _response.title != undefined
                  ? _response.title
                  : "Proses Tidak Bisa Dilanjutkan!";
              responseErrorMessage =
                _response.text != undefined
                  ? _response.text
                  : "Formulir tidak dapat diakses!";
              docoNotification(
                "error",
                responseErrorTitle,
                responseErrorMessage
              );
              return false;
            }
          }
        }

        docoNotification("error", responseErrorTitle, responseErrorMsg);
        return false;
      },
    });
  } else {
    docoNotification("warning", "Peringatan", "List obat tidak boleh kosong");
  }
}

function loadTemplate() {
  $.ajax({
    url:
      "/rajal/allow/get-template-resep?pegawai_id=" +
      $("#generalresepturform-pegawai_id").val(),
    success: function (data) {
      res = [];
      res.push({
        text: "Pilih Template",
        id: "",
      });

      $.map(data.data, function (template) {
        res.push({
          text: template.reseptemp_nama,
          id: template.reseptemp_id,
        });
      });

      $("#template_list")
        .empty()
        .select2({
          placeholder: "Pilih Template",
          data: res,
          dropdownParent: $("#div-tabel-reseptur"),
        });
      $("#template_list").trigger("change");
      $(".pilih-template").attr("disabled", "disabled");
    },
  });
}
loadTemplate();

$("#template_list").on("change", function (e) {
  if ($("#template_list").val()) {
    $(".pilih-template").removeAttr("disabled");
  } else {
    $(".pilih-template").attr("disabled", "disabled");
  }
});

$(".pilih-template").on("click", function (e) {
  $.ajax({
    data: {
      id: $("#template_list").val(),
      ruangan_depo_id: $("#select_ruangan").val(),
      penjamin_id: $("#penjamin_id").val(),
      kelaspelayanan_id: kelaspelayanan_id,
    },
    url: "/rajal/allow/get-template-resep-detail",
    dataType: "json",
    success: function (results) {
      let datas = results.data;
      for (let data of datas) {
        let obat = null;
        if (data.racikan_id == "NR") {
          obat = {
            additional_data:
              typeof data.additional_data !== "undefined"
                ? data.additional_data
                : "-",
            detail_type:
              typeof data.detail_type !== "undefined" ? data.detail_type : "-",
            etiket: typeof data.etiket !== "undefined" ? data.etiket : "-",
            harganetto_reseptur:
              typeof data.harganetto_reseptur !== "undefined"
                ? data.harganetto_reseptur
                : "-",
            hargasatuan_reseptur:
              typeof data.hargasatuan_reseptur !== "undefined"
                ? data.hargasatuan_reseptur
                : "-",
            obatalkes_id:
              typeof data.obatalkes_id !== "undefined"
                ? data.obatalkes_id
                : "-",
            obatalkes_nama:
              typeof data.obatalkes_nama !== "undefined"
                ? data.obatalkes_nama
                : "-",
            qty_konversi:
              typeof data.qty_konversi !== "undefined"
                ? data.qty_konversi
                : "-",
            qty_reseptur:
              typeof data.qty_reseptur !== "undefined"
                ? data.qty_reseptur
                : "-",
            racikan_id:
              typeof data.racikan_id !== "undefined" ? data.racikan_id : "-",
            rke: typeof data.rke !== "undefined" ? data.rke : "-",
            satuaninput_id:
              typeof data.satuaninput_id !== "undefined"
                ? data.satuaninput_id
                : "-",
            satuaninput_text:
              typeof data.satuaninput_text !== "undefined"
                ? data.satuaninput_text
                : "-",
            satuankecil_id:
              typeof data.satuankecil_id !== "undefined"
                ? data.satuankecil_id
                : "-",
            satuankecil_text:
              typeof data.satuankecil_text !== "undefined"
                ? data.satuankecil_text
                : "-",
            signa: typeof data.signa !== "undefined" ? data.signa : "-",
            signa_id:
              typeof data.signa_id !== "undefined" ? data.signa_id : "-",
            is_kronis:
              typeof data.is_kronis !== "undefined" ? data.is_kronis : "-",
            kebutuhan:
              typeof data.kebutuhan !== "undefined" ? data.kebutuhan : "-",
            hari: typeof data.hari !== "undefined" ? data.hari : "-",
            qty_obat:
              typeof data.qty_obat !== "undefined" ? data.qty_obat : "-",
            iterasi: typeof data.iterasi !== "undefined" ? data.iterasi : "-",
            stok_sisa:
              typeof data.qty_tersedia !== "undefined"
                ? data.qty_tersedia
                : "-",
          };
        } else if (
          (typeof data.detail_type !== "undefined" ? data.detail_type : "-") ==
          "racikan_freetext"
        ) {
          obat = {
            detail_type:
              typeof data.detail_type !== "undefined" ? data.detail_type : "-",
            racikan_id:
              typeof data.racikan_id !== "undefined" ? data.racikan_id : "-",
            racikan_text:
              typeof data.racikan_text !== "undefined"
                ? data.racikan_text
                : "-",
            rke: typeof data.rke !== "undefined" ? data.rke : "-",
          };
        } else {
          obat = {
            additional_data:
              typeof data.additional_data !== "undefined"
                ? data.additional_data
                : "-",
            detail_type:
              typeof data.detail_type !== "undefined" ? data.detail_type : "-",
            etiket: typeof data.etiket !== "undefined" ? data.etiket : "-",
            hargajual_reseptur:
              typeof data.hargajual_reseptur !== "undefined"
                ? data.hargajual_reseptur
                : "-",
            harganetto_reseptur:
              typeof data.harganetto_reseptur !== "undefined"
                ? data.harganetto_reseptur
                : "-",
            hargasatuan_reseptur:
              typeof data.hargasatuan_reseptur !== "undefined"
                ? data.hargasatuan_reseptur
                : "-",
            nama_racikan:
              typeof data.nama_racikan !== "undefined"
                ? data.nama_racikan
                : "-",
            obatalkes_id:
              typeof data.obatalkes_id !== "undefined"
                ? data.obatalkes_id
                : "-",
            obatalkes_nama:
              typeof data.obatalkes_nama !== "undefined"
                ? data.obatalkes_nama
                : "-",
            qty_konversi:
              typeof data.qty_konversi !== "undefined"
                ? data.qty_konversi
                : "-",
            qty_racikan:
              typeof data.qty_racikan !== "undefined" ? data.qty_racikan : "-",
            qty_reseptur:
              typeof data.qty_reseptur !== "undefined"
                ? data.qty_reseptur
                : "-",
            racikan_id:
              typeof data.racikan_id !== "undefined" ? data.racikan_id : "-",
            r: typeof data.r !== "undefined" ? data.r : "-",
            rke: typeof data.rke !== "undefined" ? data.rke : "-",
            satuan_besar:
              typeof data.satuan_besar !== "undefined"
                ? data.satuan_besar
                : "-",
            satuan_nama:
              typeof data.satuan_nama !== "undefined" ? data.satuan_nama : "-",
            satuan_racikan_id:
              typeof data.satuan_racikan_id !== "undefined"
                ? data.satuan_racikan_id
                : "-",
            satuan_racikan_nama:
              typeof data.satuan_racikan_nama !== "undefined"
                ? data.satuan_racikan_nama
                : "-",
            satuanbesar_id:
              typeof data.satuanbesar_id !== "undefined"
                ? data.satuanbesar_id
                : "-",
            satuaninput_nama:
              typeof data.satuaninput_nama !== "undefined"
                ? data.satuaninput_nama
                : "-",
            satuaninput_id:
              typeof data.satuaninput_id !== "undefined"
                ? data.satuaninput_id
                : "-",
            satuaninput_text:
              typeof data.satuaninput_text !== "undefined"
                ? data.satuaninput_text
                : "-",
            satuankecil_id:
              typeof data.satuankecil_id !== "undefined"
                ? data.satuankecil_id
                : "-",
            satuankecil_text:
              typeof data.satuankecil_text !== "undefined"
                ? data.satuankecil_text
                : "-",
            signa: typeof data.signa !== "undefined" ? data.signa : "-",
            signa_id:
              typeof data.signa_id !== "undefined" ? data.signa_id : "-",
            is_kronis:
              typeof data.is_kronis !== "undefined" ? data.is_kronis : "-",
            kebutuhan:
              typeof data.kebutuhan !== "undefined" ? data.kebutuhan : "-",
            hari: typeof data.hari !== "undefined" ? data.hari : "-",
            qty_obat:
              typeof data.qty_obat !== "undefined" ? data.qty_obat : "-",
            iterasi: typeof data.iterasi !== "undefined" ? data.iterasi : "-",
            stok_sisa:
              typeof data.qty_tersedia !== "undefined"
                ? data.qty_tersedia
                : "-",
          };
        }
        let _tmpObatIndex = list_temp_obat.findIndex(
          (itemObat) => itemObat.obatalkes_id == obat.obatalkes_id
        );
        if (_tmpObatIndex < 0) {
          list_temp_obat.push(obat);
        }
      }
      appendObat(list_temp_obat);
      countTemplate += 1;
      relatedResepTempId = $("#template_list").val();
      validateTemplateBtn();
      $(".check-template:checked").prop("checked", false);
      $(".check-template").trigger("change");
    },
    error: function (data) {
      return false;
    },
  });
});

function simpanTemplate() {
  let _hasError = false;
  if ($("#template_name").val() == "") {
    $("#template_name")
      .closest(".col-sm-12")
      .append(
        '<p class="has-error" style="color: red">Nama Template Tidak Boleh Kosong!</p>'
      );
    _hasError = true;
  }
  if (_hasError) {
    setTimeout(() => {
      $(".has-error").remove();
    }, 2000);
    return false;
  }
  let listObat = $(".check-template:checked");

  let templateListObat = [];
  for (let obatTemplate of listObat) {
    let index = list_temp_obat.findIndex(
      (itemObat) =>
        (itemObat.obatalkes_id == obatTemplate.value ||
          itemObat.racikan_text == obatTemplate.value) &&
        ((itemObat.rke === null &&
          obatTemplate.getAttribute("data-rke") === "null") ||
          itemObat.rke == obatTemplate.getAttribute("data-rke")) &&
        itemObat.racikan_id === obatTemplate.getAttribute("data-racikan-id")
    );
    templateListObat.push(list_temp_obat[index]);
  }

  let data = {
    dokter_id: $("#generalresepturform-pegawai_id").val(),
    reseptemp_id: $("#old_reseptemp_id").val(),
    reseptemp_nama: $("#template_name").val(),
    list_obat: templateListObat,
  };

  $.ajax({
    data: data,
    url: urlSimpanReseptur,
    dataType: "json",
    type: "POST",
    beforeSend: function () {
      showLoader();
    },
    success: function (data) {
      $(".check-template:checked").prop("checked", false);
      $(".check-template").trigger("change");
      $("#modal-template-resep").modal("toggle");
      docoNotification(
        "success",
        "Proses Berhasil",
        "Berhasil menyimpan template"
      );
      loadTemplate();
      countTemplate = 0;
      validateTemplateBtn(true);
    },
    error: function (data) {},
  }).done(function () {
    hideLoader();
  });
}

function validateTemplateBtn(defaultState = false) {
  let titleMsg = "";
  if (defaultState) {
    $(".btn-template-group").prop("disabled", true);
    $(".btn-template-group").addClass("disabled");
    $(".btn-template-group").unwrap("<span></span>");
  } else {
    if (
      countTemplate == 1 &&
      pegawai_id == $("#generalresepturform-pegawai_id").val()
    ) {
      relatedResepTempName = $("#template_list :selected").text();
      let updatedUrl = urlUpdateTemplate.replace(
        "#reseptemp_id#",
        relatedResepTempId
      );
      $("#btn-update-template").attr("href", updatedUrl);
      $("#btn-hapus-template").prop("disabled", false);
      $("#btn-hapus-template").removeClass("disabled");
    } else {
      titleMsg =
        countTemplate > 1
          ? "Template resep tidak dapat dihapus karena terdapat lebih dari 1 template."
          : pegawai_id != $("#generalresepturform-pegawai_id").val()
          ? "Anda tidak diizinkan menghapus template resep ini."
          : "";
      $("#btn-hapus-template").prop("disabled", true);
      $("#btn-hapus-template").addClass("disabled");
      $("#btn-hapus-template").wrap(
        `<span class='d-inline-block' tabindex='0' data-toggle='tooltip' data-placement='top' title='${titleMsg}'></span>`
      );
    }
  }
}

$("#btn-hapus-template").bind("click", function () {
  let resepTempName = $("#template_list :selected").text();
  confirmationDialog(
    `Apakah anda yakin untuk menghapus template resep - ${resepTempName} ?`,
    (confirm) => {
      if (confirm) {
        $.ajax({
          url: urlDeleteTemplate,
          method: "POST",
          data: {
            reseptemp_id: $("#template_list").val(),
          },
          beforeSend: function () {
            showLoader();
          },
          success: function (response) {
            let { data } = response;
            docoNotification("success", data.title, data.text);
            loadTemplate();
            countTemplate = 0;
            validateTemplateBtn(true);
          },
          error: function () {
            hideLoader();
          },
        });
      }
    }
  );
});

function udd_checked(event) {
  let target = $(event.delegateTarget);
  let pos = target.data("pos");
  list_temp_obat[pos].udd = target.prop("checked");
}

function editObat(target) {
  let tr = $(target).closest("tr");
  let data = list_temp_obat[$(target).data("pos")];
  if (tr.find(".input-racikan").length > 0) {
    let racikan = $(tr.find(".input-racikan")[0]);
    racikan.val(data.racikan_id);

    if (data.detail_type == "racikan_detail") {
      tr.find(".input-nama-racikan").val(data.nama_racikan);
      tr.find(".input-qty-racikan").val(data.qty_racikan);
      if (
        tr.find(
          '.input-satuan-racikan option[value="' + data.satuan_racikan_id + '"]'
        ).length > 0
      ) {
        tr.find(".input-satuan-racikan")
          .val(data.satuan_racikan_id)
          .trigger("change");
      } else {
        var newOption = new Option(
          data.satuan_racikan_nama,
          data.satuan_racikan_id,
          true,
          true
        );
        tr.find(".input-satuan-racikan").append(newOption).trigger("change");
      }
    }
    racikan.trigger("change");
  }

  if (tr.find(".input-qty").length > 0) {
    let qty = $(tr.find(".input-qty")[0]);
    qty.val(data.qty_reseptur);
  }
  if (tr.find(".input-obat").length > 0) {
    let obat = $(tr.find(".input-obat")[0]);

    if (obat.find('option[value="' + data.obatalkes_id + '"]').length > 0) {
      obat.val(data.obatalkes_id).trigger("change");
    } else {
      var newOption = new Option(
        data.obatalkes_nama,
        data.obatalkes_id,
        true,
        true
      );
      obat.append(newOption).trigger("change");
    }
  }
  if (tr.find(".input-catatan").length > 0) {
    let catatan = $(tr.find(".input-catatan")[0]);
    catatan.val(data.etiket);
  }
  if (tr.find(".input-racikan-freetext").length > 0) {
    let freetext = $(tr.find(".input-racikan-freetext")[0]);
    freetext.val(data.racikan_text);
  }
  if (tr.find(".input-signa").length > 0) {
    let signa = $(tr.find(".input-signa")[0]);
    let value = data.signa_id != null ? data.signa_id : data.signa; //pake value text untuk signa freetext
    signa.val(value);
    signa.trigger("change");
    signa
      .empty()
      .append('<option value="' + value + '">' + data.signa + "</option>")
      .val(value)
      .trigger("change");
  }
  if (tr.find(".input-is-kronis").length > 0) {
    let is_kronis =
      $(tr.find(".text-is-kronis")[0]).attr("data-value") == "true"
        ? true
        : false;
    $(tr.find(".input-is-kronis")[0]).prop("checked", is_kronis).uniform();
  }

  if (tr.find(".input-hari").length > 0) {
    let hari = $(tr.find(".input-hari")[0]);
    hari.val(data.hari).change();
  }

  if (tr.find(".input-kebutuhan").length > 0) {
    let kebutuhan = $(tr.find(".input-kebutuhan")[0]);
    kebutuhan.val(data.kebutuhan);
    if (data.detail_type == "non_racikan") {
      kebutuhan.attr("readonly", true);
    }
  }

  if (konfigStokObatAlkes == "true") {
    if (tr.find(".input-stok").length > 0) {
      let stok = $(tr.find(".input-stok")[0]);
      stok.val(data.stok_sisa);
    }
  }

  tr.find(".text-list-resep").addClass("hidden");
  tr.find(".input-list-resep").removeClass("hidden");
}

function confirmObat(target) {
  let tr = $(target).closest("tr");
  let data = { ...list_temp_obat[$(target).data("pos")] };
  let additional =
    typeof data.additional_data !== "undefined"
      ? JSON.parse(data.additional_data)
      : {};

  if (konfigStokObatAlkes == "true") {
    if (
      parseFloat($(tr.find(".input-qty")[0]).val()) >
      parseFloat($(tr.find(".input-stok")[0]).val())
    ) {
      docoNotification(
        "warning",
        "Perhatian",
        "Qty tidak boleh melebihi stok tersedia"
      );
      return false;
    }
    if (
      tr.find(".input-qty").val() == null ||
      tr.find(".input-qty").val() == undefined ||
      tr.find(".input-qty").val() <= 0
    ) {
      docoNotification("warning", "Perhatian", "Qty harus lebih dari 0");
      return false;
    }

    if (tr.find(".input-stok").length > 0) {
      let stok = $(tr.find(".input-stok")[0]);
      let stokSisa = tr.find(".input-stok").val() + " " + data.satuankecil_text;
      stok.closest("td").find(".text-list-resep").html(stokSisa);
      tr.css("background-color", "#FFFFFF");
    } else {
      return false;
    }
  }

  if (tr.find(".input-racikan").length > 0) {
    let racikan = $(tr.find(".input-racikan")[0]);
    data.racikan_id = racikan.val();
    if (data.detail_type != "racikan_freetext") {
      if (racikan.val() == "OR") {
        data.detail_type = "racikan_detail";
      } else {
        data.rke = null;
        data.detail_type = "non_racikan";
        racikan.find('option[value="OR"]').remove();
        racikan.select2();
        if (is_ranap && tr.find(".udd-checkbox").length > 0) {
          tr.find(".udd-checkbox").prop("disabled", false);
        }
      }
    }

    if (data.detail_type == "racikan_detail") {
      data.nama_racikan = tr.find(".input-nama-racikan").val();
      data.qty_racikan = tr.find(".input-qty-racikan").val();
      data.satuan_racikan_id = tr.find(".input-satuan-racikan").val();
      data.satuan_racikan_nama = tr
        .find(".input-satuan-racikan")
        .find(":selected")
        .text();
      racikan
        .closest("td")
        .find(".text-list-resep")
        .html(
          racikan.find(":selected").text() +
            "<br>" +
            data.nama_racikan +
            " </br> " +
            data.qty_racikan +
            " " +
            data.satuan_racikan_nama
        );
    } else {
      racikan
        .closest("td")
        .find(".text-list-resep")
        .html(racikan.find(":selected").text());
    }
  }
  if (tr.find(".input-racikan").length > 0) {
    let rke = $(tr.find(".text-rke")[0]);
    rke.html(data.rke ? data.rke : "-");
  }
  if (tr.find(".input-obat").length > 0) {
    let obat = $(tr.find(".input-obat")[0]);
    let selectedObat = apotek.list_stok[obat.val()];

    let listTempObatTidakTersediaFiltered = listTempObatTidakTersedia.filter(
      (x) => x.obatalkes_id == obat.val()
    );
    if (listTempObatTidakTersediaFiltered.length > 0) {
      tr.addClass("obatalkes__not-found");
    } else {
      tr.removeClass("obatalkes__not-found");
    }

    data.obatalkes_id = obat.val();
    data.obatalkes_nama = obat.find(":selected").text();

    if (typeof selectedObat !== "undefined") {
      data.satuaninput_id = selectedObat.satuankecil_id;
      data.satuaninput_text = selectedObat.satuankecil_nama;
      data.satuankecil_id = selectedObat.satuankecil_id;
      data.satuankecil_text = selectedObat.satuankecil_nama;
      data.stok_sisa = selectedObat.qty_tersedia;
      additional.satuaninput_id = selectedObat.satuankecil_id;
      additional.satuan_input = selectedObat.satuankecil_nama;
      additional.satuankonversi_id = selectedObat.satuankecil_id;
      additional.satuan_konversi = selectedObat.satuankecil_nama;
      if (
        typeof _group !== "undefined" &&
        typeof _group[selectedObat.satuankecil_id] !== "undefined"
      )
        additional.nilai_konversi = _group[selectedObat.satuankecil_id];
    }

    obat.closest("td").find(".text-list-resep").html(data.obatalkes_nama);
  }
  if (tr.find(".input-catatan").length > 0) {
    let catatan = $(tr.find(".input-catatan")[0]);
    let is_racikan = ["racikan_freetext", "racikan_detail"].includes(
      data.detail_type
    );
    let rke = data.rke;
    if (is_racikan) {
      data.etiket = catatan.val();
      data.racikan_text = catatan.val();
      list_temp_obat = list_temp_obat.map(function (val, i) {
        if (val.rke == rke) {
          val.etiket = catatan.val();
          val.racikan_text = catatan.val();
          $(".satuan").eq(i).find(".text-list-resep").html(catatan.val());
        }
        return val;
      });
    } else {
      catatan.closest("td").find(".text-list-resep").html(catatan.val());
      data.etiket = catatan.val();
      data.racikan_text = catatan.val();
    }
  }

  if (tr.find(".input-is-kronis").length > 0) {
    let rke = data.rke;
    let is_racikan = ["racikan_freetext", "racikan_detail"].includes(
      data.detail_type
    );
    let is_kronis = $(tr.find(".input-is-kronis")[0]);
    data.is_kronis = is_kronis.is(":checked");
    let checkKronis = "";

    if (is_racikan) {
      list_temp_obat = list_temp_obat.map(function (val, i) {
        if (val.rke == rke) {
          val.is_kronis = is_kronis.is(":checked");
          checkKronis =
            val.is_kronis == true
              ? '<span class="text-is-kronis" style="font-weight: bold; font-size: 12pt;" data-value="true">✓</span>'
              : "";
          $(".flag-kronis").eq(i).find(".text-list-resep").html(checkKronis);
        }
        return val;
      });
    } else {
      checkKronis =
        data.is_kronis == true
          ? '<span class="text-is-kronis" style="font-weight: bold; font-size: 12pt;" data-value="true">✓</span>'
          : "";
      is_kronis.closest("td").find(".text-list-resep").html(checkKronis);
    }
  }

  if (tr.find(".input-racikan-freetext").length > 0) {
    let freetext = $(tr.find(".input-racikan-freetext")[0]);
    data.racikan_text = freetext.val();
    freetext.closest("td").find(".text-list-resep").html(data.racikan_text);
  }
  if (tr.find(".input-qty").length > 0) {
    let qty = $(tr.find(".input-qty")[0]);
    let obat = $(tr.find(".input-obat")[0]);
    let selectedObat = apotek.list_stok[obat.val()];

    qty
      .closest("td")
      .find(".text-list-resep")
      .html(parseFloat(qty.val()).toFixed(2) + " " + data.satuaninput_text);
    data.qty_reseptur = parseFloat(qty.val()).toFixed(2);

    if (typeof selectedObat !== "undefined") {
      let jumlah_harga = selectedObat.hargaygdipakai * qty.val();

      data.qty_konversi =
        qty.val() *
        (typeof _group !== "undefined" &&
        typeof _group[selectedObat.satuankecil_id] !== "undefined"
          ? _group[selectedObat.satuankecil_id]
          : 1);
      data.harganetto_reseptur = selectedObat.harganetto;
      data.hargasatuan_reseptur = selectedObat.hargaygdipakai;
      additional.harga_reseptur = selectedObat.hargaygdipakai;

      tr.find(".jumlah_harga").html(docoHelper.convertToRupiah(jumlah_harga));
    }
  }
  if (tr.find(".input-signa").length > 0) {
    let index = tr.index();
    let signa = $(tr.find(".input-signa")[0]);
    let is_racikan = ["racikan_freetext", "racikan_detail"].includes(
      data.detail_type
    );
    let rke = data.rke;
    let select2Id = isNaN(signa.val())
      ? signa.find(":selected").text()
      : signa.val();
    let thisSigna = dataSigna[signa.val()];
    thisSigna = thisSigna != undefined ? thisSigna : list_temp_obat[index]; // check if first initiate (if dataSigna empty)
    if (is_racikan) {
      data.signa = signa.find(":selected").text();
      data.signa_id = isNaN(signa.find(":selected").val())
        ? null
        : signa.find(":selected").val();
      data.qty_obat = thisSigna != null ? thisSigna.qty_obat : 0;
      data.iterasi = thisSigna != null ? thisSigna.iterasi : 0;
      list_temp_obat = list_temp_obat.map(function (val, i) {
        if (val.rke == rke) {
          val.signa_id = isNaN(signa.find(":selected").val())
            ? null
            : signa.find(":selected").val();
          val.signa = signa.find(":selected").text();
          val.qty_obat = thisSigna != null ? thisSigna.qty_obat : 0;
          val.iterasi = thisSigna != null ? thisSigna.iterasi : 0;
          $(".input-signa")
            .eq(i)
            .empty()
            .append(
              '<option value="' + select2Id + '">' + val.signa + "</option>"
            )
            .val(select2Id)
            .trigger("change");
          $(".signa").eq(i).find(".text-list-resep").html(val.signa);
          if (!isSignaFreetext(data)) {
            $(".input-qty").prop("readonly", false);
          } else {
            $(".input-qty").prop("readonly", false);
          }
        }
        return val;
      });
    } else {
      data.signa = signa.find(":selected").text();
      data.signa_id = isNaN(signa.find(":selected").val())
        ? null
        : signa.find(":selected").val();
      signa.closest("td").find(".text-list-resep").html(data.signa);
    }
  }

  if (tr.find(".input-hari").length > 0) {
    let hari = $(tr.find(".input-hari")[0]);
    let kebutuhan = $(tr.find(".input-kebutuhan")[0]);
    let is_racikan = ["racikan_freetext", "racikan_detail"].includes(
      data.detail_type
    );
    let rke = data.rke;
    if (is_racikan) {
      data.hari = hari.val();
      data.kebutuhan =
        kebutuhan.val() != null
          ? kebutuhan.val() != ""
            ? kebutuhan.val()
            : 1
          : 1;
      list_temp_obat = list_temp_obat.map(function (val, i) {
        if (val.rke == rke) {
          let satuantext = "";
          let hariKalkulasi =
            data.hari != null && data.hari != "" ? data.hari : 1;
          if (i == $(target).data("pos")) {
            satuantext =
              data.satuaninput_text != null ? data.satuaninput_text : "";
            val.kebutuhan = data.kebutuhan;
          } else {
            satuantext =
              val.satuaninput_text != null ? val.satuaninput_text : "";
            val.kebutuhan =
              val.kebutuhan != null
                ? val.kebutuhan != ""
                  ? val.kebutuhan
                  : 1
                : 1;
          }
          val.qty_obat =
            val.qty_obat != null ? val.qty_obat : data.qty_obat_signa;
          val.iterasi = val.iterasi != null ? val.iterasi : 1;
          let result = parseFloat(
            val.qty_obat * val.iterasi * hariKalkulasi * val.kebutuhan
          ).toFixed(2);
          if (
            !isSignaFreetext(val) &&
            data.hari != null &&
            data.hari != "" &&
            i != $(target).data("pos") &&
            data.hari != val.hari
          ) {
            val.qty_reseptur = result;
            $(".input-qty").eq(i).val(val.qty_reseptur).change();
            $(".qty")
              .eq(i)
              .find(".text-list-resep")
              .html(val.qty_reseptur + " " + satuantext);
          }
          val.hari = data.hari;
          $(".input-hari").eq(i).val(val.hari).change();
          $(".hari").eq(i).find(".text-list-resep").html(val.hari);
        }
        return val;
      });
    } else {
      data.hari = hari.val();
      hari.closest("td").find(".text-list-resep").html(data.hari);
    }
  }

  if (tr.find(".input-kebutuhan").length > 0) {
    let kebutuhan = $(tr.find(".input-kebutuhan")[0]);
    let is_racikan = ["racikan_freetext", "racikan_detail"].includes(
      data.detail_type
    );
    let rke = data.rke;
    data.kebutuhan = kebutuhan.val();
    let result =
      !isNaN(data.kebutuhan) && data.kebutuhan != ""
        ? parseFloat(data.kebutuhan).toFixed(2)
        : "";
    kebutuhan.closest("td").find(".text-list-resep").html(result);
  }

  if (
    typeof data.qty_reseptur !== "undefined" &&
    typeof data.hargasatuan_reseptur !== "undefined"
  ) {
    let jumlah_harga = data.qty_reseptur * data.hargasatuan_reseptur;
    tr.find(".jumlah_harga").html(docoHelper.convertToRupiah(jumlah_harga));
  }

  data.additional_data = JSON.stringify(additional);
  if (validateObatAlkes($(target).data("pos"), data)) {
    list_temp_obat[$(target).data("pos")] = data;
    tr.find(".text-list-resep").removeClass("hidden");
    tr.find(".input-list-resep").addClass("hidden");
  } else {
    docoNotification(
      "warning",
      "Peringatan",
      "Obat " + data.obatalkes_nama + " sudah diinputkan"
    );
  }
}

function closeObat(target) {
  let tr = $(target).closest("tr");
  let data = { ...list_temp_obat[$(target).data("pos")] };

  tr.find(".input-list-resep").addClass("hidden");

  if (
    typeof data.qty_reseptur !== "undefined" &&
    typeof data.hargasatuan_reseptur !== "undefined"
  ) {
    let jumlah_harga = data.qty_reseptur * data.hargasatuan_reseptur;
    tr.find(".jumlah_harga").html(docoHelper.convertToRupiah(jumlah_harga));
  }

  if (tr.find("input[type!='hidden']").length > 0) {
    tr.find("input[type!='hidden']").val(null);
  }
  if (tr.find("input[type!='hidden']").length > 0) {
    tr.find("select").val(null);
  }

  tr.find(".text-list-resep").removeClass("hidden");
}

function validateObatAlkes(pos, data) {
  for (let idx in list_temp_obat) {
    if (idx != pos) {
      let temp_obat = list_temp_obat[idx];
      if (data.detail_type == temp_obat.detail_type) {
        if (
          data.detail_type == "non_racikan" ||
          (data.detail_type == "racikan_detail" && data.rke == temp_obat.rke)
        ) {
          if (data.obatalkes_id == temp_obat.obatalkes_id) {
            return false;
          }
        }
      }
    }
  }
  return true;
}

// Array data
function getSuggestPlaningHtml(data) {
  let html = "";
  data.forEach((item, i) => {
    if (["non_racikan", "racikan_detail"].includes(item.detail_type)) {
      html += `Resep - ${item.obatalkes_nama} (${item.qty_reseptur} ${item.satuankecil_text}) \n`;
    } else if (item.detail_type == "racikan_freetext") {
      html += `Resep - ${item.racikan_text} \n`;
    }
  });

  return html;
}

/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
  if (list_temp_obat.length > 0) {
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

// ada 2 kondisi, karena dari list_temp_obat dan dataSigna[];
function isSignaFreetext(data) {
    if (data.signa != null) {
        return data.signa != null && data.signa_id == null;
    } else {
        return data.signa_nama != null && data.signa_id == data.signa_nama;
    }
}
