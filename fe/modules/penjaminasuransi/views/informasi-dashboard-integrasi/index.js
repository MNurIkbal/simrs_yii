let table;

$(document).ready(function () {
  table = $("#tableDataIntegrasi").docoTabel({
    filter: false,
    columnDefs: [
      {
        orderable: false,
        className: "select-checkbox",
        targets: 0,
      },
    ],
    select: {
      style: "single",
      selector: "tr",
    },
    sorting: [[2, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    scrollY: true,
    ajax: {
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/get-data-integrasi",
    },
    stateSave: true,
    columns: [
      {
        data: null,
        searchable: false,
        orderable: false,
        defaultContent: "", // 0
      },
      {
        data: "rowNum",
        name: "rowNum",
        searchable: false,
        orderable: false, // 1
      },
      {
        title: "Tanggal Pendaftaran - Tanggal Pulang",
        data: "tgl_daftar_keluar",
        name: "tgl_pendaftaran",
        orderable: true, // 2
      },
      {
        title: "No Pendaftaran",
        data: "no_pendaftaran",
        searchable: true,
        orderable: true, // 3
      },
      {
        title: "Nama Pasien",
        data: "nama_pasien_lengkap",
        searchable: false,
        orderable: false, // 4
      },
      {
        title: "Alamat",
        data: "alamat_pasien",
        searchable: false,
        orderable: true, // 5
      },
      {
        title: "Jenis Kelamin",
        data: "jeniskelamin",
        searchable: false,
        orderable: false, // 6
        render: (data) => {
          return data == "15" ? "Laki-laki" : "Perempuan";
        },
      },
      {
        title: "Poliklinik",
        data: "ruangan_nama",
        searchable: false,
        orderable: false, // 7
      },
      {
        title: "Jenis Kasus Penyakit",
        data: "jeniskasuspenyakit_nama",
        searchable: false,
        orderable: false, // 8
      },
      {
        title: "Kelas Pelayanan",
        data: "kelaspelayanan_nama",
        searchable: true,
        orderable: true, // 9
      },
      {
        title: "Dokter DPJP",
        data: "nama_pegawai",
        visible: true,
        orderable: false, // 10
      },
      {
        title: "Cara Bayar",
        data: "carabayar_nama",
        visible: true, // 11
      },
      {
        title: "Penjamin / Asuransi",
        data: "penjamin_nama",
        visible: true, // 12
      },
      {
        title: "Status Periksa",
        data: "status_periksa_nama",
        visible: true, // 13
      },
      {
        title: "No Klaim",
        data: "no_klaim",
        visible: true,
        orderable: false, // 14
      },
      {
        title: "Petugas",
        data: "petugas",
        visible: true,
        orderable: false, // 15
      },
      {
        title: "Status Klaim",
        data: "status_klaim",
        orderable: false, // 16
      },
    ],
    formFilters: [
      {
        fieldName: "tgl_pendaftaran",
        label: "Tanggal Pendaftaran",
        type: {
          name: "rangeDate",
        },
      },
      "no_pendaftaran",
      {
        fieldName: "status_periksa",
        label: "Status Periksa",
        type: {
          name: "select",
          payload: statusPeriksa,
        },
      },
      "no_klaim",
      {
        fieldName: "tgl_pulang",
        label: "Tanggal Pulang",
        type: {
          name: "rangeDate",
          payload: {
            allDate: true,
          },
        },
      },
      "nama_pasien",
    ],
    fnRowCallback: function(nRow, aData) {
      if (aData?.is_cob) {
        $('td', nRow).css('background-color', '#FFD44D');
      }
    },
  });

  $('#btn-cob-pasien').on('click', function (e) {
    e.preventDefault();
    window.open(
      baseUrl +
        `penjamin-asuransi/informasi-dashboard-integrasi/cob-pasien`
    );
  })
});

$(document).on("keyup", "#form-filter__tableDataIntegrasi", function (e) {
  e.preventDefault();
  if (e.key == "Enter") {
    $("#search-button").trigger("click");
  }
});

$(document).on("click", "#btn-proses", function (e) {
  e.preventDefault();
  let selectedData = table.row(".selected").data();
  console.log(selectedData);

  return false;
});

$(document).on("click", "#tableDataIntegrasi tr", function () {
  var _data = table.rows(".selected").data();
  
  if (_data.length > 0) {
    var _pendaftaranId = _data[0].primary
    var _asuransiId = _data[0].asuransiIdEncrypt
    var _penjaminId = _data[0].penjaminIdEncrypt
    var _noKlaim = _data[0].noKlaimEncrypt
    
    $("#btn-proses").attr("disabled", false);
    $("#btn-cetak-pendaftaran").attr("disabled", false);

    if (_data[0].is_pengesahan == true) {
      $("#btn-cetak-pengesahan").attr("disabled", false);
    } else {
      $("#btn-cetak-pengesahan").attr("disabled", true);
    }

    $('#btn-proses').attr('data-target',`informasi-dashboard-integrasi/proses?pendaftaran_id=${_pendaftaranId}&asuransi_id=${_asuransiId}&penjamin_id=${_penjaminId}&no_klaim=${_noKlaim}`);
  } else {
    $("#btn-cetak-pendaftaran").attr("disabled", true);
    $("#btn-cetak-pengesahan").attr("disabled", true);
    $("#btn-proses").attr("disabled", true);
  }
  console.log(_data);
});

$(document).on("click", ".btn-reset", function (e) {
  localStorage.clear();
  const tableId = "tableDataIntegrasi";
  const element = $(`#filter-section__${tableId}`);
  const formWrapper = $(`#form-filter__${tableId}`);
  element.find("input").val("");
  element.find("select").val(null).trigger("change");
  element
    .find("#tgl_pendaftaran-startDate")
    .val(moment().locale("en").format("DD-MMM-YYYY"))
    .trigger("change");
  element
    .find("#tgl_pendaftaran-endDate")
    .val(moment().locale("en").format("DD-MMM-YYYY"))
    .trigger("change");
  const tableElement = $(`#${tableId}`).DataTable();
  tableElement.context[0].ajax.data.advancedFilter =
    serializeArrayToJson(formWrapper);
  tableElement.ajax.reload();
});


$(document).on("click", "#btn-cetak-pendaftaran", function (e) {
  e.preventDefault();
  no_klaim = table.row(".selected").data().no_klaim;
  penjamin_id = table.row(".selected").data().penjamin_id;

  window.open(
    baseUrl +
      `penjamin-asuransi/informasi-dashboard-integrasi/cetak-pendaftaran?no_klaim=${no_klaim}&penjamin_id=${penjamin_id}`
  );
});

$(document).on("click", "#btn-cetak-pengesahan", function (e) {
  e.preventDefault();
  no_klaim = table.row(".selected").data().no_klaim;
  penjamin_id = table.row(".selected").data().penjamin_id;

  window.open(
    baseUrl +
      `penjamin-asuransi/informasi-dashboard-integrasi/cetak-pengesahan?no_klaim=${no_klaim}&penjamin_id=${penjamin_id}`
  );
});
