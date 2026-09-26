var _tmp = {};
var tableBmhp;
var _checkStatus = function () {
  var _currentStatus = _status;
  if (_statusSelesai === _currentStatus) {
    $("#obat-form").find("input, select").prop("disabled", true);
    $("#btn-save-obat, #btn-ulang-obat").prop("disabled", true);
  }
};

$(document).ready(function () {
  _checkStatus();
  var _baseUrl = `${_endPoint}/order-obat-alkes`;
  var _params = `kelaspelayanan_id=${_kelaspelayanan_id}&penjamin_id=${_penjamin_id}&ruangan_id=${_ruangan_id}&penunjang_id=${_id}`;
  var _paramsTindakan = `penunjang_id=${_id}&pendaftaran_id=${_pendaftaran_id}&jenis=tindakan`;
  tableBmhp = $("#tmp-bmhp").docoTabel({
    filter: false,
    sorting: [[1, "desc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    ajax: {
      url:
        _endPoint +
        "/order-obat-alkes/get-data-tindakan-obat?pendaftaran_id=" +
        _pendaftaran_id +
        "&pasienmasukpenunjang_id=" +
        _id +
        "&jenis=obat",
    },
    columns: [
      {
        data: null,
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData, rowAdditionalData) => {
          var tableInfo = tableBmhp.page.info();
          return tableInfo.start + rowAdditionalData.row + 1;
        },
      },
      {
        data: "tgl_tindakan",
        render: (data) => {
          return data == "" || data == null
            ? "-"
            : moment(data).format("DD MMM YYYY");
        },
      },
      {
        data: "nama_tindakan",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "obat",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "petugas_1",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "petugas_2",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "qty",
        className: "text-right",
        searchable: false,
        sortable: false,
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "harga_tindakan",
        className: "text-right",
        searchable: false,
        sortable: false,
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: "is_ditagihkan",
        className: "text-center",
        searchable: false,
        sortable: false,
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        data: null,
        orderable: false,
        className: "text-center",
        render: (data, rowElement, rowData) => {
          return rowData.action;
        },
      },
    ],
  });

  // $("#daftartindakan_id").docoPaginationSelec2(
  //    config = {
  //       placeholder: "-- Cari Tindakan --",
  //       _api: `${_baseUrl}/data-pemakaian-tindakan?${_paramsTindakan}`
  //    }
  // )

  $("#daftartindakan_id").on("change", function () {
    var pelayananId = "";
    var _tindakan_id = $(this).val();
    if (_tindakan_id == "") {
      return false;
    } else {
      var _data = $("#daftartindakan_id").select2("data");
      _obatAlkes = _data[0];
      var _dataValue = _obatAlkes.datavalue;
      pelayananId = _arrayTindakan[_tindakan_id].tindakanpelayanan_id;
      $("#tindakanpelayanan_id").val(pelayananId);
    }
  });
  $("#obatform-depo_id").docoPaginationSelec2(
    (config = {
      placeholder: "-- Cari Depo --",
      _api: _endPoint + "/order-obat-alkes/list-depo",
    })
  );
  $("#obatalkes_id").select2InfinityScroll({
    url: `${_endPoint}/order-obat-alkes/list-obat`,
    callbackData: (param) => {
      return {
        ...param,
        depo_id: $("#obatform-depo_id").val(),
        penjamin_id: _penjamin_id,
        kelaspelayanan_id: _kelaspelayanan_id,
        instalasi_id: instalasiId,
      };
    },
    callbackProccess: (response) => {
      let results = [];
      response.results.map((item) => {
        results.push({
          id: item.obatalkes_id,
          text: `${item.obatalkes_kode} - ${item.obatalkes_nama} - stok ${item.qty_tersedia}`,
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
  $("#obatalkes_id").on("change", function () {
    var _data = $(this).select2("data");
    if (typeof _data[0] != "undefined") {
      var _dataValue = _data[0];
      var _harga =
        typeof _dataValue.hargaygdipakai != "undefined"
          ? _dataValue.hargaygdipakai
          : 0;
      var qty_tersedia =
        typeof _dataValue.qty_tersedia != "undefined"
          ? _dataValue.qty_tersedia
          : 0;
      $("#harga_obat").val(_harga);
      $("#stok").val(qty_tersedia);
      _hitungTarifObat();
    }
  });
  var _muatUlangObat = function () {
    $("div").removeClass("has-error");
    $("span.help-block.error").remove();
    $("div.help-block.error").remove();
    $("#obatalkes_id").val("").trigger("change");
    $("#daftartindakan_id").val("").trigger("change");
    $("#obatform-qty").val(1).trigger("change");
    $("#obatform-is_tagihkan").prop("checked", false);
    $("#obatform-petugas_satu").val("").trigger("change");
    $("#obatform-petugas_dua").val("").trigger("change");
    $("#obatform-depo_id").val("").trigger("change");
  };
  $(document).on("click", ".delete-obat", function (event) {
    event.preventDefault();
    $(this).docoForm("delete", {
      success: function (data) {
        tableBmhp.draw();
        _muatUlangObat();
      },
    });
  });

  $("#btn-ulang-obat").on("click", function (event) {
    event.preventDefault();
    _muatUlangObat();
  });

  $("#obatform-qty").on("keyup change", function (event) {
    event.preventDefault();
    _hitungTarifObat();
  });

  $("#btn-add-bmhp").on("click", function (event) {
    event.preventDefault();
    var _idObatAlkes = $("#obatalkes_id").select2("data");
    if (typeof _idObatAlkes[0] != "undefined") {
      _obatAlkes = _idObatAlkes[0];
      var _dataValue = _obatAlkes;
    }

    var _form = $("#obat-form");
    var _data = _form.serializeArray();

    if (_dataValue.qty_tersedia < parseFloat($("#obatform-qty").val())) {
      docoNotification("error", "Peringatan", "Stok tidak mencukupi");
      return false;
    }
    _data.push({
      name: "jenis",
      value: "obat",
    });
    _data.push({
      name: "stok",
      value: _dataValue.qty_tersedia,
    });
    $().docoForm("click", {
      data: _data,
      url: _form.attr("action"),
      success: function (data) {
        tableBmhp.draw();
        _muatUlangObat();
      },
    });
  });

  var _hitungTarifObat = function () {
    var qty = $("#obatform-qty").val()
      ? parseFloat($("#obatform-qty").val())
      : 1;
    var harga_satuan = parseFloat($("#harga_obat").val());
    var jumlah = parseFloat(harga_satuan * qty);
    $("#obatform-jumlah_tarif").val(docoHelper.convertToRupiah(jumlah));
  };
});
