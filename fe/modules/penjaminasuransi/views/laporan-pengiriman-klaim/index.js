let table;

$(document).ready(function () {
  table = $("#tableLaporan").docoTabel({
    filter: false,
    select: {
      style: "single",
      selector: "tr",
    },
    sorting: [[3, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    scrollY: true,
    ajax: {
      url: "/penjamin-asuransi/laporan-pengiriman-klaim/get-data",
    },
    stateSave: true,
    columns: [
      {
        data: "rowNum",
        name: "rowNum",
        searchable: false,
        orderable: false, // 1
      },
      {
        data: "nosep",
        name: "nosep",
        orderable: true, // 2
      },
      {
        title: "Tanggal Pendaftaran / Tanggal Pulang",
        data: "tgl_masukpulang",
        searchable: false,
        orderable: false, // 3
      },
      {
        title: "Tanggal Klaim",
        data: "tgl_klaim",
        searchable: false,
        orderable: false, // 3
      },
      {
        title: "Nama Pasien/No. RM",
        data: "nama_pasien_lengkap",
        searchable: false,
        orderable: false, // 4
      },
      {
        data: "group_nama",
        searchable: false,
        orderable: true, // 5
      },
      {
        data: "spesial_prosesedur",
        searchable: false,
        orderable: false, // 6
      },
      {
        data: "group_tarif_rp",
        searchable: false,
        orderable: false, // 7
      },
      {
        data: "total_tarifrs_rp",
        searchable: false,
        orderable: false, // 8
        render: (data) => {
          if (data) {
            return data;
          } else {
            return "-";
          }
        }
      },
      {
        data: "is_terkirim",
        searchable: false,
        orderable: false, // 9
        render: (data) => {
          if (data) {
            return "Sudah Kirim Online";
          } else {
            return "Belum Terkirim";
          }
        }
      },
    ],
    formFilters: [
      {
        fieldName: "tgl_pendaftaran",
        label: "Tanggal Pendaftaran",
      },
      {
        fieldName: "tgl_pulang",
        label: "Tanggal Pulang",
      }, 
      {
        fieldName: "tgl_klaim",
        label: "Tanggal Klaim",
      },  
    ],
    fnRowCallback: function(nRow, aData) {
      if (aData?.is_cob) {
        $('td', nRow).css('background-color', '#FFD44D');
      }
    },
  });

  table.on('xhr', function () {
    getSummary(table);
  })

  let tanggalPendaftaran = $('#tableLaporan-tgl_pendaftaran--form').pickadate({
    format: 'yyyy-mm-dd', // Format tanggal
    selectMonths: true,   // Dropdown untuk bulan
    selectYears: 20,
  })

  let tanggalPulang = $('#tableLaporan-tgl_pulang--form').pickadate({
    format: 'yyyy-mm-dd', // Format tanggal
    selectMonths: true,   // Dropdown untuk bulan
    selectYears: 20,
  })

  let tanggalKlaim = $('#tableLaporan-tgl_klaim--form').pickadate({
    format: 'yyyy-mm-dd', // Format tanggal
    selectMonths: true,   // Dropdown untuk bulan
    selectYears: 20,
  })

  $('#btn-reset__tableLaporan').on('click', function (e) {
    tanggalPendaftaran.pickadate('picker').clear();
    tanggalPulang.pickadate('picker').clear();
  })

  $('#btn-cob-pasien').on('click', function (e) {
    e.preventDefault();
    window.open(
      baseUrl +
        `penjamin-asuransi/informasi-dashboard-integrasi/cob-pasien`
    );
  })
});

$(document).on("keyup", "#form-filter__tableLaporan", function (e) {
  e.preventDefault();
  if (e.key == "Enter") {
    $("#search-button").trigger("click");
  }
});

$(document).on("click", ".btn-reset", function (e) {
  localStorage.clear();
  const tableId = "tableLaporan";
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

function getSummary(table) {
  $.ajax({
    type: "GET",
    url: "/penjamin-asuransi/laporan-pengiriman-klaim/summary",
    data: table.ajax.params(),
    success: function (response) {
      let tagihanRs = response?.tagihanRs;
      let tarifKlaim = response?.tarifKlaim;
      $('#total_tagihan_rs').html(tagihanRs);
      $('#total_klaim').html(tarifKlaim);
    },
    error: function(error) {
        docoNotification('warning', 'Peringatan', error);
    }
  });
}

