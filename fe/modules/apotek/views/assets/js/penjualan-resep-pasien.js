/**
 * @author : Randy Vianda Putra (aweutist)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
 */

window.list_obat = [];
var _group = {};
var isFromUnit;
var type;
var apotek = { list_stok: {} };
var apotek_nr = { list_stok: {} };
var transaksi_obat_minus = false;
var transaksi_obat_minus_racikan = false;
if (window.location.pathname == "/apotek/transaksi-resep/rumah-sakit") {
  isFromUnit = true;
  type = 3;
} else {
  isFromUnit = false;
  type = 4;
}

$(document).ready(function () {
  var _form_racikan = $("#form-racikan");
  var _form_nonracikan = $("#form-obat");
  var racikan_id = $(this).is(":checked");

  if (isFromUnit) {
    appendObat(transObat);
  }

  $(".disabled").prop("disabled", true);
  $(".r_ke").prop("disabled", true);
  $(".required_racikan").hide();
  $(".racikan_id").change(function () {
    // tampilkan label required jika user memilih obat racikan
    if (racikan_id) {
      $(".r_ke").prop("disabled", false);
      $(".required_racikan").show();
    } else {
      $(".r_ke").prop("disabled", true);
      $(".r_ke").val("");
      $(".required_racikan").hide();
    }
  });

  // mencegah karakter lain selain angka desimal
  $(document).on("input", ".iter", function () {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, "")
    );
    this.value = match[1] + match[2];
  });

  $(document).on("input", ".r_ke", function () {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, "")
    );
    this.value = match[1] + match[2];
    $("#rke_display").html($(this).val());
  });

  $(document).on("change", ".r_ke", function () {
    var rke = parseInt(this.value);
    if (transObat != null) {
      var racikan_list = Object.keys(transObat).map((key) => transObat[key]);
      console.log(racikan_list);

      var existing_racikan = racikan_list.find((val) => val.r_ke == rke);
      if (typeof existing_racikan !== "undefined") {
        $(".nama_racikan").val(existing_racikan.nama_racikan);
        $(".qty_racikan").val(existing_racikan.qty_racikan);
        $("#satuan_racikan_id")
          .val(existing_racikan.satuan_racikan_id)
          .trigger("change");
        $("#signa_racikan").val(existing_racikan.signa_id).trigger("change");

        var catatan;
        if (existing_racikan.etiket == "-") {
          catatan = null;
        } else {
          catatan = existing_racikan.etiket;
        }
        $(".catatan").val(catatan);
      } else {
        $(".catatan").val(null);
        $(".nama_racikan").val(null);
        $(".qty_racikan").val(null);
        $("#signa_racikan").val(null).trigger("change");
        $("#satuan_racikan_id").val(null).trigger("change");
      }
    }
  });

  $(document).on("input", ".qty", function () {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, "")
    );
    this.value = match[1] + match[2];

    // set value konversi ketika user menginput qty
    var _qty = $(this).val();
    var nilai_konversi = $("#nilai_konversi").val();
    var konversi = _qty * nilai_konversi;
    konversi = konversi.toFixed(docoHelper.decimal_places); // set harga konversi dibulatkan 2 angka di belakang koma
    $(".qty_konversi").val(konversi);
  });

  $(document).on("input", ".qty_racikan ", function () {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, "")
    );
    this.value = match[1] + match[2];
  });

  $("#id_auto_obat").change(function () {
    $(".qty_konversi").val("");
    $(".qty").val("");
    $("#nilai_konversi").val("");
  });

  $("#select_obat_non_racikan").change(function () {
    $(".qty_konversi").val("");
    $(".qty").val("");
    $("#nilai_konversi").val("");
  });

  // ketika value dropdown satuan berubah, set ulang label satuan, value harga, stok, dan qty konversi
  $("#ampuls_id").on("change", function () {
    /*var _stoktable = 0;
        $.each(transObat, function (x, y) {
            if (typeof transObat[x] !== "undefined") {
                if(y.obatalkes_id == _id && y.resepturdetail_id == null){
                    _stoktable = parseFloat(_stoktable) + parseFloat(y.qty_konversi);
                }
            }
        });*/
    // var _id = $('#id_auto_obat').val();
    var _id = $("#select_obat_non_racikan").val();

    $(".qty_konversi").val("");
    $(".qty").val("");

    var _val = $(this).val();
    var konversi = _group[_val];
    if (konversi != null && konversi != undefined) {
      konversi = parseFloat(konversi).toFixed(docoHelper.decimal_places);
    }
    $("#nilai_konversi").val(konversi);

    var _stok = $(".stok").val();
    var _konversi_stok = parseFloat(_stok) / parseFloat(konversi);
    _konversi_stok = isNaN(_konversi_stok) ? 0 : _konversi_stok;
    if (_konversi_stok != null) {
      _konversi_stok = _konversi_stok.toFixed(docoHelper.decimal_places);
    }
    $(".qty_tersedia").html(_konversi_stok);

    var _hargajual = $(".hargajual").val();
    var _konversi_harga = _hargajual * konversi;
    if (_konversi_harga != null && _konversi_harga != undefined) {
      _konversi_harga = _konversi_harga.toFixed(docoHelper.decimal_places);
    }
    $(".harga_konversi").val(docoHelper.convertToRupiah(_konversi_harga));

    var _val_nama = $(this).find("option:selected").text();
    var _val_value = $(this).val();
    if (_val_nama == "--Pilih--") {
      _val_nama = "";
      $(".harga_konversi").val("");
    }
    $(".stok_text").html(_val_nama);
    $(".harga_text").html(_val_nama);
    $(".satuan_kecil_text").html(_val_nama);

    $(".satuaninput_id").val(_val_value);
    $(".satuan_input").val(_val_nama);
  });

  let autoObat = $(".autoObat");
  let id_obat = $(".id_obat");
  let stok = $(".stok");
  let harga = $(".harga");
  let ppn = $(".ppn");
  let obat_nama = $(".obat_nama");
  let tgl_kadaluarsa = $(".tgl_kadaluarsa");
  let satuankecil_id = $(".satuankecil_id");
  let apotek = { list_stok: {} };
  // change here
  let harganetto = $(".harganetto");
  let hn_margin = $(".hn_margin");
  let hn_diskon = $(".hn_diskon");
  let hn_ppn = $(".hn_ppn");
  let hargajual = $(".hargajual");
  let persenppn = $(".persenppn");
  let persenmargin = $(".persenmargin");
  let persendiscount = $(".persendiscount");
  let posisi = $(".posisi");
  // end here

  // Non Racikan
  $("#select_obat_non_racikan").on(
    "depdrop:afterChange",
    function (event, id, value, jqXHR, textStatus) {
      let ajaxResults = $("#select_obat_non_racikan").depdrop("getAjaxResults");
      list_obat = ajaxResults["output"];
    }
  );

  $("#select_obat_non_racikan")
    .docoPaginationSelec2(
      (config = {
        placeholder: "Pilih Obat ... ",
        _api: "/apotek/transaksi-resep/list-obat-alkes-depo",
        ajax: {
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1,
              ruangan_id: ruangan_id,
              penjamin_id: $("#penjamin_id").val(),
              kelaspelayanan_id: $("#kelaspelayanan_id").val(),
              kelastitipan_id: $("#kelastitipan_id").val(),
              instalasi_id: instalasi_id,
            };
          },
          results: function (data, params) {
            var more = params.page * 30 < data.total_count;
            return { results: data.items, more: more };
          },
          processResults: function (res, params) {
            params.page = params.page || 1;
            var arr = [];
            transaksi_obat_minus = res.transaksi_obat_minus;
            $.each(res.data_stok, function (index, value) {
              if (index < 10) {
                arr.push({
                  id: value.obatalkes_id,
                  text: value.obatalkes_nama,
                });

                let data = [];
                let response = res.data_stok;
                for (var i in response) {
                  data.push({
                    id: response[i].obatalkes_id,
                    text: response[i].obatalkes_nama,
                  });
                  apotek_nr.list_stok[response[i].obatalkes_id] = response[i];
                }
              }
            });
            return {
              results: arr,
              pagination: {
                more: res.data_stok.length > 10,
              },
            };
          },
        },
      })
    )
    .on("change", function (e) {
      var id = $(this).val();
      var selected = apotek_nr.list_stok[id];
      if (typeof selected !== "undefined") {
        id_obat.val(selected.obatalkes_id);
        stok.val(selected.qty_tersedia);
        $(".qty_tersedia").html(
          !isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0
        );
        harga.val(selected.harganetto);
        ppn.val(selected.ppn);
        obat_nama.val(selected.obatalkes_nama);
        satuankecil_id.val(selected.satuankecil_id);
        harganetto.val(selected.harganetto);
        hn_margin.val(selected.hn_margin);
        hn_diskon.val(selected.hn_diskon);
        hn_ppn.val(selected.hn_ppn);
        persendiscount.val(selected.disc);
        persenmargin.val(selected.margin);
        persenppn.val(selected.ppn);
        hargajual.val(selected.hargajual);
        var urutan = parseInt(urutObatRs);
        urutan = urutan + 1;
        posisi.val(urutan);
        var _nilai_konversi = $("#nilai_konversi").val();
        var _konversi_harga = selected.hargajual * _nilai_konversi;
        _konversi_harga = _konversi_harga.toFixed(2);
        $(".harga_konversi").val(docoHelper.convertToRupiah(_konversi_harga));

        $("#tambah-obat").attr("disabled", false);
        $("#disabled_button_info").attr("style", "display:none");

        if (selected.qty_tersedia <= 0 && !transaksi_obat_minus) {
          $("#tambah-obat").attr("disabled", "true");
          $("#disabled_button_info").attr("style", "display:block");
        }

        $("#ampuls_id").val(selected.satuankecil_id).trigger("change");
      }
    });

  // Racikan
  $("#select_obat_racikan").on(
    "depdrop:afterChange",
    function (event, id, value, jqXHR, textStatus) {
      let ajaxResults = $("#select_obat_racikan").depdrop("getAjaxResults");
      list_obat = ajaxResults["output"];
    }
  );

  $("#select_obat_racikan")
    .docoPaginationSelec2(
      (config = {
        placeholder: "Pilih Obat ... ",
        _api: "/apotek/transaksi-resep/list-obat-alkes-depo",
        ajax: {
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1,
              ruangan_id: ruangan_id,
              penjamin_id: $("#penjamin_id").val(),
              kelaspelayanan_id: $("#kelaspelayanan_id").val(),
              kelastitipan_id: $("#kelastitipan_id").val(),
              instalasi_id: instalasi_id,
            };
          },
          results: function (data, params) {
            var more = params.page * 30 < data.total_count;
            return { results: data.items, more: more };
          },
          processResults: function (res, params) {
            params.page = params.page || 1;
            transaksi_obat_minus_racikan = res.transaksi_obat_minus;
            var arr = [];
            $.each(res.data_stok, function (index, value) {
              if (index < 10) {
                var _disabled = false;
                if (value.qty_tersedia <= 0 && !transaksi_obat_minus_racikan) {
                  _disabled = true;
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
                more: res.data_stok.length > 10,
              },
            };
          },
        },
      })
    )
    .on("change", function (e) {
      var id = $(this).val();
      var selected = apotek.list_stok[id];
      if (typeof selected !== "undefined") {
        _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
        _hargajual = parseFloat(
          selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0
        ).toFixed(2);
        _harganetto = parseFloat(selected.harganetto).toFixed(2);
        _satuankecil_nama = selected.satuankecil_nama;
        _satuankecil_id = selected.satuankecil_id;

        _form_racikan.find(`input[name=obatalkes_id]`).val(id);
        _form_racikan
          .find(`input[name=obat_nama]`)
          .val(selected.obatalkes_nama);
        _form_racikan.find(`input[name=stok]`).val(_stok_sisa);
        _form_racikan
          .find(`.qty_tersedia`)
          .html(isNaN(_stok_sisa) ? "-1" : _stok_sisa);
        _form_racikan.find(`input[name=harga]`).val(_harganetto);
        _form_racikan.find(`input[name=ppn]`).val(selected.ppn);
        _form_racikan.find(`input[name=satuankecil_id]`).val(_satuankecil_id);
        _form_racikan.find(`input[name=harganetto]`).val(_harganetto);
        _form_racikan.find(`input[name=jmlmargin]`).val(selected.hn_margin);
        _form_racikan.find(`input[name=jmldiscount]`).val(selected.hn_diskon);
        _form_racikan.find(`input[name=jmlppn]`).val(selected.hn_ppn);
        _form_racikan.find(`input[name=persendiscount]`).val(selected.disc);
        _form_racikan.find(`input[name=persenmargin]`).val(selected.margin);
        _form_racikan.find(`input[name=persenppn]`).val(selected.ppn);
        _form_racikan.find(`input[name=hargajual]`).val(selected.hargajual);

        var urutan = parseInt(urutObatRs);
        urutan = urutan + 1;
        _form_racikan.find(`input[name=posisiNo]`).val(urutan);

        var _nilai_konversi = _form_racikan
          .find(`input[name='nilai_konversi']`)
          .val();
        var _konversi_harga =
          parseFloat(selected.hargajual) * parseFloat(_nilai_konversi);
        _konversi_harga = _konversi_harga.toFixed(2);
        _form_racikan
          .find(`input[name='harga_konversi']`)
          .val(docoHelper.convertToRupiah(_konversi_harga));

        $("#satuan_racikan").val(_satuankecil_id).trigger("change");
      }
    });

  $("#satuan_racikan").on("change", function (ev) {
    $(".qty_konversi").val("");
    $(".qty").val("");

    var _satuan_id = $(this).val();
    var konversi = _group[_satuan_id];
    if (konversi != null && konversi != undefined) {
      konversi = parseFloat(konversi).toFixed(docoHelper.decimal_places);
    }

    _form_racikan.find(`input[name='nilai_konversi']`).val(konversi);
    var __nilai_konversi = _form_racikan
      .find(`input[name='nilai_konversi']`)
      .val();

    // Stok
    var __stok_obat = _form_racikan.find(`input[name='stok']`).val();
    var __konversi_stok_obat =
      parseFloat(__stok_obat) / parseFloat(__nilai_konversi);
    __konversi_stok_obat = isNaN(__konversi_stok_obat)
      ? 0
      : __konversi_stok_obat;
    if (__konversi_stok_obat != null) {
      __konversi_stok_obat = __konversi_stok_obat.toFixed(
        docoHelper.decimal_places
      );
    }
    _form_racikan.find(".qty_tersedia").html(__konversi_stok_obat);

    // Harga
    var __hargajual = _form_racikan.find(`input[name='hargajual']`).val();
    var __konversi_harga =
      parseFloat(__hargajual) * parseFloat(__nilai_konversi);
    _form_racikan
      .find(`input[name='harga_konversi']`)
      .val(docoHelper.convertToRupiah(__konversi_harga));

    // Label Satuan
    var __satuan_nama = $("#satuan_racikan").find("option:selected").text();
    var __satuan_id = $("#satuan_racikan").val();
    if (__satuan_nama == "--Pilih--") {
      __satuan_nama = "";
      __satuan_id = "";
    }

    _form_racikan.find(".satuan").html(__satuan_nama);
    _form_racikan.find(`input[name='satuaninput_id']`).val(__satuan_id);
    _form_racikan.find(`input[name='satuan_input']`).val(__satuan_nama);
    _form_racikan.find(`input[name='harga_kecil']`).val(__hargajual);

    _form_racikan.find(".satuan_kecil_racikan_text").html(__satuan_nama);
    _form_racikan.find(".stok_racikan_text").html(__satuan_nama);
  });

  $(document).on("input", "#form-racikan input[name='qty']", function (e) {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, "")
    );
    this.value = match[1] + match[2];
    var _qty = $(this).val();
    var nilai_konversi = _form_racikan
      .find(`input[name='nilai_konversi']`)
      .val();
    var konversi = _qty * nilai_konversi;
    $("#qty_konversi_racikan").val(konversi);
  });

  function validateFormAddRacikan() {
    var _form_data = _form_racikan.serializeArray();
    var _r_ke = _form_racikan.find(`input[name="r_ke"]`).val();
    var _signa_val = _form_racikan.find(`#signa_racikan`).val();
    var _signa = _form_racikan
      .find(`input[name="signa_hidden"]`)
      .val(_signa_val);
    var _catatan = _form_racikan.find(`input[name="catatan"]`).val();
    var submit_btn = _form_racikan.find("#btn-racikan");
    var _satuan_racikan_obat_val = _form_racikan
      .find(`#satuan_racikan_id`)
      .val();
    var _satuan_racikan_obat = _form_racikan
      .find(`input[name="satuan_racikan_hidden"]`)
      .val(_satuan_racikan_obat_val);
    var _qty_racikan = _form_racikan.find(`input[name="qty_racikan"]`).val();

    var _obat = _form_racikan.find(`input[name="obatalkes_id"]`).val();
    var _satuan_racikan = _form_racikan.find(`#satuan_racikan`).val();
    var _stok = _form_racikan.find(`input[name="stok"]`).val();
    var _qty = _form_racikan.find(`input[name="qty"]`).val();
    var _qty_konversi = _form_racikan.find(`input[name="qty_konversi"]`).val();
    var _nilai_konversi = _form_racikan
      .find(`input[name="nilai_konversi"]`)
      .val();

    var _stok_tersedia = _form_racikan.find(".qty_tersedia").html();
    var button_text =
      "<i class='fa fa-plus'></i> " + i18next.t("Tambah ke Racikan");

    // if(_form_racikan.find(`#select_obat_racikan`).val() == "") {
    //     docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat Alkes tidak boleh kosong"));
    //     ajaxAfterLoading(submit_btn, button_text);
    //     return false;
    // }

    if (_form_racikan.find(`#select_list_obat_racikan`).val() == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Nama Obat Alkes tidak boleh kosong")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_satuan_racikan == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Satuan tidak boleh kosong")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (
      parseFloat(_qty) > parseFloat(_stok_tersedia) &&
      !transaksi_obat_minus_racikan
    ) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Stok tidak mencukupi")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_r_ke == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("R ke tidak boleh kosong")
      );
      ajaxAfterLoading(
        submit_btn,
        "<i class='fa fa-plus'></i> " + i18next.t("Tambah")
      );
      return false;
    }

    if (_obat == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Obat tidak boleh kosong")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_qty == "" || _qty < 0) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Jumlah tidak boleh kurang dari 0")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_signa_val == "" || _signa_val == undefined) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Signa Belum Dipilih")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_nilai_konversi == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Konversi Belum Dipilih")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (
      _satuan_racikan_obat_val == "" ||
      _satuan_racikan_obat_val == undefined
    ) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Satuan Racikan Obat Belum Dipilih")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_qty_racikan == "" || _qty_racikan < 0) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Jumlah Racikan tidak boleh kurang dari 0")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    return true;
  }

  function validateExistingObat(tempData, _nama_obat) {
    var status = true;
    var _r_ke = _form_racikan.find(`input[name="r_ke"]`).val();
    var submit_btn = _form_racikan.find("#btn-racikan");
    var button_text =
      "<i class='fa fa-plus'></i> " + i18next.t("Tambah ke Racikan");

    // cek validasi jika obat sudah pernah diinputkan di tabel cache racikan
    $.each(tempObatRacikan, function (index, value) {
      if (tempData["identifier"] == value.identifier) {
        status = false;
        docoNotification(
          "warning",
          i18next.t("Perhatian"),
          i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`)
        );
        ajaxAfterLoading(submit_btn, button_text);
        return false;
      }
    });

    // cek validasi jika obat sudah pernah diinputkan di tabel cache obat
    $.each(transObat, function (index, value) {
      if (value.racikan_id == 1) {
        if (
          value.r_ke == tempData["r_ke"] &&
          value.obatalkes_id == tempData["obatalkes_id"]
        ) {
          status = false;
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`)
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }

        if (
          value.r_ke == tempData["r_ke"] &&
          value.signa_id.trim() != tempData["signa"].trim()
        ) {
          status = false;
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t(`Signa racikan ke-${_r_ke} harus sama`)
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }

        if (
          value.r_ke == tempData["r_ke"] &&
          value.nama_racikan != tempData["nama_racikan"] &&
          !value.is_deleted
        ) {
          status = false;
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t(`Nama Racikan racikan ke-${_r_ke} harus sama`)
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }

        if (
          value.r_ke == tempData["r_ke"] &&
          value.qty_racikan != tempData["qty_racikan"] &&
          !value.is_deleted
        ) {
          status = false;
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t(`Jumlah Racikan racikan ke-${_r_ke} harus sama`)
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }
        if (
          value.r_ke == tempData["r_ke"] &&
          value.satuan_racikan_id != tempData["satuan_racikan_id"] &&
          !value.is_deleted
        ) {
          status = false;
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t(`Satuan Racikan racikan ke-${_r_ke} harus sama`)
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }
      }
    });

    if (!status) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`)
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    return true;
  }

  // Tambah obat ke racikan
  $(document).on("click", "#btn-racikan", function (e) {
    e.preventDefault();
    var _form_data = _form_racikan.serializeArray();
    var _signaSelected = _form_racikan.find("#signa_racikan option:selected");
    var _r_ke = _form_racikan.find(`input[name="r_ke"]`).val();
    var _obat = _form_racikan.find(`input[name="obatalkes_id"]`).val();
    var _identifier = `${_r_ke}-${_obat}`;
    var _nama_obat = "";
    var button_text = $(this).html();
    var is_signafreetext = false;

    if (_signaSelected.data("select2Tag")) {
      is_signafreetext = true;
    }

    if (!validateFormAddRacikan()) return false;

    let tempData = {};
    $.each(_form_data, function (index, value) {
      var _data = _form_racikan.serializeArray()[index];
      if (_data.name == "signa_hidden") {
        _data.name = "signa";
      }

      if (_data.name == "obat_nama") {
        _nama_obat = _data.value;
      }

      if (_data.name == "satuan_racikan_hidden") {
        _data.name = "satuan_racikan_id";
      }
      tempData[_data.name] = _data.value;
    });
    tempData["identifier"] = _identifier;
    tempData["is_signafreetext"] = is_signafreetext;

    if (!validateExistingObat(tempData, _nama_obat)) return false;

    // readonly R ke & signa
    _form_racikan.find(`input[name="r_ke"]`).attr("readonly", true);
    _form_racikan.find(`#signa_racikan`).attr("disabled", "true");

    _form_racikan.find(`input[name="nama_racikan"]`).attr("readonly", true);
    _form_racikan.find(`input[name="qty_racikan"]`).attr("readonly", true);
    _form_racikan.find(`#satuan_racikan_id`).attr("disabled", "true");

    // reset form obat
    resetFormObat();

    if (is_signafreetext) {
      var selected_signa = list_signa.find(
        (item) => item.signa_id == tempData.signa
      );
      if (selected_signa != undefined) {
        tempData.signa_nama = selected_signa.signa_nama;
      }
    }

    tempObatRacikan.push(tempData);
    drawTempRacikan();
  });

  $(document).on("click", "#list-racikan-temp tr .btn-danger", function (e) {
    e.preventDefault();
    tempObatRacikan.splice($(this).data("id"), 1);
    drawTempRacikan();
  });

  $(document).on("click", "#form-racikan .btn-ulang", function (e) {
    e.preventDefault();
    tempObatRacikan = [];
    drawTempRacikan();
    resetFormRacikanKe();
  });

  $(document).on("keypress", "#form-racikan", function (e) {
    var keyPressed = event.keyCode || event.which;
    if (keyPressed === 13) {
      e.preventDefault();
      $("#btn-racikan").click();
    }
  });

  function resetFormObat() {
    _form_racikan.find(`#select_obat_racikan`).val(null).trigger("change");
    _form_racikan.find(`#select_list_obat_racikan`).val(null).trigger("change");
    document.getElementById("satuan_racikan").options.length = 0;
    _form_racikan.find(`#satuan_racikan`).attr("disabled", "true");
    _form_racikan.find(`input[name="harga_konversi"]`).val(null);
    _form_racikan.find(`input[name="qty"]`).val(null);
    _form_racikan.find(`.qty_tersedia`).html("-");
    _form_racikan.find(`.satuan`).html("");
    _form_racikan.find(`input[name="qty_konversi"]`).val(null);
  }

  function resetFormRacikanKe() {
    _form_racikan.find(`input[name="r_ke"]`).val(null);
    _form_racikan.find(`input[name="r_ke"]`).attr("readonly", false);
    _form_racikan.find(`#signa_racikan`).attr("disabled", false);
    _form_racikan.find(`#signa_racikan`).val(null).trigger("change");
    _form_racikan.find(`input[name="catatan"]`).val(null);
    _form_racikan.find(`input[name="nama_racikan"]`).val(null);
    _form_racikan.find(`input[name="nama_racikan"]`).attr("readonly", false);
    _form_racikan.find(`#satuan_racikan_id`).attr("disabled", false);
    _form_racikan.find(`#satuan_racikan_id`).val(null).trigger("change");
    _form_racikan.find(`input[name="qty_racikan"]`).val(null);
    _form_racikan.find(`input[name="qty_racikan"]`).attr("readonly", false);
  }

  function drawTempRacikan() {
    var _list_racik = $("#list-racikan-temp");
    _list_racik.html("");

    var _no = 0;
    var _html = "";
    $.each(tempObatRacikan, function (index, value) {
      _no++;
      _html +=
        `
            <tr data-index="` +
        index +
        `">
                <td>` +
        _no +
        `</td>
                <td>` +
        value.obat_nama +
        `</td>
                <td align='right'>` +
        value.harga_konversi +
        `</td>
                <td>` +
        value.qty +
        `</td>
                <td>` +
        value.satuan_input +
        `</td>
                <td><button class="btn btn-xs btn-danger" data-id=` +
        index +
        ` ><i class="fa fa-trash"></i></button></td>
            </tr>`;
    });

    if (_no <= 0) {
      _html += `
            <tr>
                <td colspan="5" class="text-center">Tidak ada Data</td>
            </tr>`;
    }

    _list_racik.prepend(_html);
  }

  $("#signa_racikan").on("change", function (e) {
    var signanama = $("#signa_racikan option:selected").text();
    var signa_val = _form_racikan.find(`#signa_racikan`).val();
    _form_racikan.find(`input[name="signa_nama"]`).val(signanama);
    _form_racikan.find(`input[name="signa_hidden"]`).val(signa_val);
  });

  $("#satuan_racikan_id").on("change", function (e) {
    var satuanracikanobat = $("#satuan_racikan_id option:selected").text();
    var satuanracikanobat_val = _form_racikan.find(`#satuan_racikan_id`).val();
    _form_racikan
      .find(`input[name="satuan_racikan_obat_nama"]`)
      .val(satuanracikanobat);
    _form_racikan
      .find(`input[name="satuan_racikan_hidden"]`)
      .val(satuanracikanobat_val);
  });

  $(document).on("click", "#btn-tambah-racikan", function (e) {
    e.preventDefault();
    var submit_btn = $(this);
    if (tempObatRacikan.length <= 0) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Belum ada obat di racikan")
      );
      ajaxAfterLoading(
        submit_btn,
        "<i class='fa fa-plus'></i> " + i18next.t("Tambahkan Racikan")
      );
      return false;
    }

    $.ajax({
      url: "/apotek/transaksi-resep/save-multiple-cache?type=" + type,
      type: "post",
      data: {
        data: tempObatRacikan,
      },
      beforeSend: function () {
        ajaxLoading(submit_btn);
      },
      success: function (response) {
        docoNotification(
          "success",
          i18next.t("Berhasil"),
          i18next.t("Data berhasil di tambah")
        );
        var value = response.data;
        if (!isFromUnit) {
          transObat = value;
          appendObat(value);
        } else {
          transObat = $.extend({}, transObat, value);
          appendObat(transObat);
        }
        urutObatRs = urutObatRs + 1;
        tempObatRacikan = [];
        drawTempRacikan();
        resetFormRacikanKe();
        resetFormObat();

        return true;
      },
      error: function (response) {
        ajaxAfterLoading(
          submit_btn,
          "<i class='fa fa-plus'></i> " + i18next.t("Tambah")
        );
        var resMessage = response.responseJSON.message;
        docoNotification(
          "error",
          i18next.t("Perhatian"),
          i18next.t(resMessage)
        );
        return false;
      },
      complete: function (response) {
        ajaxAfterLoading(
          submit_btn,
          "<i class='fa fa-plus'></i> " + i18next.t("Tambahkan Racikan")
        );
      },
    });
  });

  // Tambah Obat Non Racikan
  $(document).on("click", "#tambah-obat", function (event) {
    event.preventDefault();
    var data = $("#form-obat").serialize();
    var form = $("#form-obat");
    var button_text = $(this).html();
    var submit_btn = form.find(".add");
    var _r_ke = form.find(".r_ke").val();
    var _racikan_id = form.find(".racikan_id").is(":checked");
    var _id_obat = form.find(".id_obat").val();
    var _cekObatR = `${_r_ke}-${_id_obat}-${_racikan_id}`;
    var _stok = form.find(".stok").val();
    var _signa = form.find(".signaid").val();

    var _qty = form.find(".qty").val();
    var _qty_konversi = form.find(".qty_konversi").val();
    var _nilai_konversi = form.find(".nilai_konversi").val();
    var id_barang_list = form.find(".list_barang").get();

    var _stoksisa = form.find(".qty_tersedia").html();
    var invalid = false;

    if (parseFloat(_qty) > parseFloat(_stoksisa) && !transaksi_obat_minus) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Stok tidak mencukupi")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_racikan_id) {
      if (_r_ke == "") {
        docoNotification(
          "warning",
          i18next.t("Perhatian"),
          i18next.t("R ke tidak boleh kosong")
        );
        ajaxAfterLoading(submit_btn, button_text);
        return false;
      }
    }

    if (_id_obat == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Obat tidak boleh kosong")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_qty == "" || _qty < 0) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Jumlah tidak boleh kurang dari 0")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_qty_konversi == "" || _qty < 0) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Jumlah tidak boleh kurang dari 0")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_nilai_konversi == "") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Konversi Belum Dipilih")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    if (_signa == "" || _signa == undefined) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Signa Belum Dipilih")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    $.each(transObat, function (index, value) {
      if (value.obatalkes_id == _id_obat && !value.is_racikan) {
        invalid = true;
        return false;
      } else {
        invalid = false;
      }
    });

    if (invalid) {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t(`Obat sudah diinputkan!`)
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }

    for (var i = 0; i < id_barang_list.length; i++) {
      var cek_obat = $(id_barang_list[i]).data("id");

      if (_cekObatR == cek_obat) {
        invalid = true;
      }
    }

    if (!invalid) {
      $.ajax({
        url: "/apotek/transaksi-resep/save-cache",
        type: "post",
        data: form.serialize(),
        beforeSend: function () {
          ajaxLoading(submit_btn);
        },
        success: function (data) {
          docoNotification(
            "success",
            i18next.t("Berhasil"),
            i18next.t("Data berhasil di tambah")
          );
          form = "false";
          var value = data.data;
          var response = {};
          if (!isFromUnit) {
            transObat = value;
            appendObat(value);
          } else {
            transObat = $.extend({}, transObat, value);
            appendObat(transObat);
          }
          urutObatRs = urutObatRs + 1;
          $(".r_ke").val("");
          $(".r_ke").prop("disabled", true);
          $(".racikan_id").prop("checked", false);
          $(".autoObat").val(null).trigger("change");
          $("#select_obat_non_racikan").val(null).trigger("change");
          $(".signaid").val(null).trigger("change");
          $(".qty-obat").val("");
          $(".qty").val("");
          $(".id_obat").val(null).trigger("change");

          $("#ampuls_id, .satuan_select").val(null).trigger("change");
          $(".qty_tersedia").html("");
          $(".harga_text").html("");
          $(".stok_text").html("");
          $(".harga_konversi").val("");
          $(".catatan").val("");

          return false;
        },
        error: function (res) {
          ajaxAfterLoading(submit_btn, button_text);
          var resMessage = res.responseJSON.message;
          docoNotification(
            "error",
            i18next.t("Perhatian"),
            i18next.t(resMessage)
          );
          return false;
        },
        complete: function () {
          ajaxAfterLoading(submit_btn, button_text);
        },
      });
    } else {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Nama Obat telah di input")
      );
      ajaxAfterLoading(submit_btn, button_text);
      return false;
    }
  });

  function appendObat(object, list_error = []) {
    var _no = 0;
    var _html = "";
    $(".default-value").attr("style", "display:none");
    $.each(object, function (x, y) {
      if (typeof object[x] !== "undefined" && object !== "") {
        var out_of_stock =
          list_error.indexOf(parseInt(y.obatalkes_id)) > -1 ? "out-stock" : "";
        _no++;
        _html += "<tr class='resep " + out_of_stock + "'>";
        _html +=
          "<td style='display:none;'><input type='hidden' class='list_barang' data-id='" +
          y.r_ke +
          "-" +
          y.obatalkes_id +
          "-" +
          y.racikan_id +
          "' value='" +
          y.obatalkes_id +
          "'></td>";
        _html += '<td class="numbering">' + _no + "</td>";
        // _html +=
        //   "<td>" +
        //   (y.is_racikan ? "<i class='fa fa-check'></i>" : "-") +
        //   "</td>";
        var nama_racikan = y.nama_racikan == "" ? "" : " - " + y.nama_racikan;
        var satuan_racikan_obat_nama =
          y.satuan_racikan_obat_nama == "" ? "" : y.satuan_racikan_obat_nama;
        var qty_racikan = y.qty_racikan == "" ? "" : " - " + y.qty_racikan;
        _html +=
          '<td colspan="2">' +
          (y.r_ke == null
            ? "-"
            : y.r_ke + nama_racikan + qty_racikan + satuan_racikan_obat_nama) +
          "</td>";
        _html += "<td>" + y.obatalkes_nama + "</td>";
        _html +=
          '<td align="right">' + docoHelper.convertToRupiah(y.harga) + "</td>";
        _html += '<td class="signa">' + y.signa + "</td>";
        _html += '<td class="qty" align="right">' + y.qty + "</td>";
        _html += '<td class="satuan" align="right">' + y.satuan_input + "</td>";
        if (isFromUnit) {
          _html += '<td class="satuan" align="right">' + y.etiket + "</td>";
        } else {
          _html += '<td class="satuan" align="right">' + y.catatan + "</td>";
        }

        _html +=
          '<td align="right" class="total_harga" data-netto="' +
          y.harganetto +
          '" data-sub="' +
          y.subtotal +
          '">' +
          docoHelper.convertToRupiah(y.subtotal) +
          "</td>";
        _html +=
          '<td><a class="btn btn-xs btn-danger deleted" data-rdId="' +
          y.resepturdetail_id +
          '" data-id="' +
          x +
          '"><i class="fa fa-trash"></i></a></td>';
        _html += "</tr>";
      }
      object[x] = y;
    });

    if (_html === "") {
      _html += "<tr>";
      _html +=
        '<td colspan="9" id="data-null" class="text-center">Data Tidak Ditemukan</td>';
      _html += "</tr>";
    }
    $("#list-obat").html("");
    $("#list-obat").prepend(_html);
    sumHarga();
  }

  function ajaxLoading(element) {
    $(element).attr("disabled", true);
    $(element).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
  }

  function ajaxAfterLoading(element, text) {
    $(element).attr("disabled", false);
    $(element).html(text);
  }

  function sumHarga() {
    var sum_subtotalItem = 0;
    var biaya_admin = docoHelper.convertToAngka($(".biayaAdmin").val());
    if (biaya_admin == 0 || isNaN(biaya_admin)) {
      biaya_admin = 0;
    }

    $.each($(".total_harga"), function () {
      var value = $(this).data("sub");
      var netto = $(this).data("netto");
      sum_subtotalItem += value;
      sum_subtotalNetto += netto;
    });
    $(".subTotalItem").val(sum_subtotalItem);
    $(".subtotal").html(docoHelper.convertToRupiah(sum_subtotalItem));
    $(".total").html(
      docoHelper.convertToRupiah(sum_subtotalItem + biaya_admin)
    );
  }

  $(".biayaAdmin").on("input", function () {
    var sub_total = $(".subTotalItem").val();
    var biaya_admin = $(this).val();
    if (biaya_admin == 0 || isNaN(biaya_admin)) {
      biaya_admin = 0;
    }
    var total = docoHelper.convertToAngka(biaya_admin) + parseFloat(sub_total);
    $(".total").html(docoHelper.convertToRupiah(total));
  });

  $(".signaid").on("change", function () {
    var _txt = $(".signaid option:selected").text();
    $(".signanama").val(_txt);
  });

  /*improvement save with shortcut alt+s*/
  $(document).on("keydown", null, "alt+s", function (event) {
    if (isFromUnit) {
      $("#save-rs").click();
    } else {
      $("#save-pasien").click();
    }
  });

  // Simpan
  $("#save-pasien").on("click", function (e) {
    e.preventDefault();
    var _id = $(this).data("id");
    var _form = $("#form-pasien");
    var dataPost = {};
    var _url;

    var _pasien_id = _form[0].elements.pasien.value;
    var _iter = _form[0].elements.iter.value;

    $.each(_form.serializeArray(), function (index, value) {
      dataPost[value.name] = value.value;
    });

    if (_pasien_id == "" && dataPost["jenis_penjualan"] == "pasien") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Belum ada pasien yang dipilih")
      );
      return false;
    }

    if (_pasien_id == "" && dataPost["jenis_penjualan"] == "karyawan") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Belum ada karyawan yang dipilih")
      );
      return false;
    }

    if (_pasien_id == "" && dataPost["jenis_penjualan"] == "bebas") {
      docoNotification(
        "warning",
        i18next.t("Perhatian"),
        i18next.t("Nama pasien harus diisi")
      );
      return false;
    }

    if (dataPost["jenis_penjualan"] == "pasien") {
      _url = "/apotek/transaksi-resep/save-pasien";
    } else if (dataPost["jenis_penjualan"] == "bebas") {
      _url = "/apotek/transaksi-resep/save-bebas";
    } else if (dataPost["jenis_penjualan"] == "karyawan") {
      _url = "/apotek/transaksi-resep/save-karyawan";
    }

    $(this).docoForm("click", {
      url: _url,
      skipSuccessNotif: true,
      data: {
        info_pasien: dataPost,
        tanggal_lahir: $(".tgl_lahir").val(),
        totalharga_jual: $(".subTotalItem").val(),
        totalharga_netto: sum_subtotalNetto,
        biayaadministrasi: docoHelper.convertToAngka($(".biayaAdmin").val()),
      },
      success: function (data) {
        var response = data.response;
        if (data.status) {
          setTimeout(function () {
            $("#save-pasien, #ulang, #tambah-obat").attr(
              "disabled",
              "disabled"
            );
            $("#deleted").css("display", "none");
            $(".deleted").css("display", "none");
            $("#form-obat :input").prop("disabled", "disabled");
          }, 500);
          new PNotify({
            title: "Berhasil",
            text:
              "Penjualan Resep Pasien dengan Nomor " +
              "<strong>" +
              data.response.nomor +
              "</strong>" +
              " berhasil disimpan, apakah Anda ingin melakukan cetak?",
            addclass:
              "alert alert-success alert-arrow-right alert-styled-right",
            type: "success",
            buttons: {
              closer: false,
              sticker: false,
            },
            hide: false,
            confirm: {
              confirm: true,
              buttons: [
                {
                  text: "Ya",
                  addClass: "btn btn-xs btn-success",
                },
                {
                  text: "Tidak",
                  addClass: "btn btn-xs btn-danger",
                },
              ],
            },
            history: {
              history: false,
            },
          })
            .get()
            .on("pnotify.confirm", function () {
              // Print
              window.open(
                "/apotek/informasi-reseptur/print-resep?id=" +
                  response.id +
                  "&noresep=" +
                  response.nomor +
                  "&nomor=" +
                  response.encNomor
              );
            });

          setTimeout(function () {
            window.location.href = "/apotek/informasi-reseptur/#";
          }, 3000);
        }
      },
      error: function (data) {
        var response = data.responseJSON;
        appendObat(response.data_obat, response.response.list_obat_tidak_cukup);
        var _responseText = JSON.parse(data.responseText);
        if (_responseText.response.data != undefined) {
          docoNotification(
            "error",
            "Proses Gagal!",
            _responseText.response.data
          );
        }
      },
    });
  });

  // Hapus Obat
  $(document).on("click", ".deleted", function (event) {
    event.preventDefault();
    var id_ = $(this).data("id");
    var button = this;
    var ResData;

    if (isFromUnit) {
      var resepturdetail_id = $(this).data("rdid");
      ResData = {
        id: id_,
        type: type,
        resepturdetail_id: resepturdetail_id,
      };
    } else {
      ResData = {
        id: id_,
        type: type,
      };
    }

    $(this).docoForm("click", {
      url: "/apotek/transaksi-resep/delete-cache",
      confirmTitle: i18next.t("Konfirmasi"),
      confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
      data: ResData,
      method: "GET",
      before: function () {
        $(button).html('<i class="fa fa-spin fa-spinner"></i>');
        $(button).prop("disabled", true);
      },
      success: function (data) {
        var list = data.detail;
        $(button).parent().parent().remove();
        docoNotification(
          "success",
          i18next.t("Berhasil"),
          i18next.t("Data berhasil di hapus")
        );
        if (typeof transObat != "undefined") {
          if (typeof transObat != "undefined") {
            delete transObat[id_];
          }
          appendObat(transObat);
        }
      },
    });
  });

  $(document).on("click", ".ulang", function (e) {
    e.preventDefault();
    location.reload();
  });

  $(document).on("click", ".print", function (e) {
    e.preventDefault();

    var link = $(this).attr("data-target");

    if (typeof link !== "undefined") {
      window.location.href = link;
    } else {
      $("#btn-print").prop("disabled", true);
    }
  });
});
