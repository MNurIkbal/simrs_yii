// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function () {
  // Reload table
  table.draw();

  // Disable edit and delete button
  $("#btn-approve").prop("disabled", true);
  $("#btn-batal").prop("disabled", true);
});

// Event click
$(document).on("click", "#table-pasien-lab tbody tr", function () {
  // Try catch
  try {
    // Get primary
    primaryKey = table.row(".selected").data().primary
      ? table.row(".selected").data().primary
      : null;
    statusPeriksa = table.row(".selected").data().status_periksa
      ? table.row(".selected").data().status_periksa
      : null;
    caraBayar = table.row(".selected").data().carabayar_nama
      ? table.row(".selected").data().carabayar_nama
      : null;
    isBayar = table.row(".selected").data().is_bayar
      ? table.row(".selected").data().is_bayar
      : null;
  } catch (e) {
    // Make it false
    primaryKey = false;
  }

  // Assign to ubah
  // $("#btn-edit").attr("action", updateUrl + primaryKey);
  $("#btn-approve").attr("data-target", approveUrl + primaryKey);
  $("#btn-batal").attr("data-target", batalUrl + primaryKey);

  // Check class selected
  if ($("#table-pasien-lab tr.selected").length == 0) {
    // Disable edit button
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
  } else {
    // Disable edit button
    $("#btn-approve").prop("disabled", false);
    $("#btn-batal").prop("disabled", false);
    $("#btn-batal").attr("data-options", "link");

    if (statusPeriksa == "Ambil Sampel") {
      $("#btn-approve").prop("disabled", true);
      $("#btn-batal").attr("data-options", "click");
      $("#btn-batal").attr("data-target", "#");
      $("#btn-batal").attr(
        "data-error-message",
        "Tidak Bisa membatalkan karena sudah mengambil sampel"
      );
    }

    if (statusPeriksa == "Batal") {
      $("#btn-batal").prop("disabled", true);
    }

    if (caraBayar != "Umum") {
      $("#btn-approve").prop("disabled", true);
      $("#btn-batal").attr("data-options", "click");
      $("#btn-batal").attr("data-target", "#");
      $("#btn-batal").attr(
        "data-error-message",
        "Tidak Bisa membatalkan karena pembayarannya menggunakan penjamin"
      );
    }
    if (isBayar) {
      $("#btn-approve").prop("disabled", true);
      $("#btn-batal").attr("data-options", "click");
      $("#btn-batal").attr("data-target", "#");
      $("#btn-batal").attr(
        "data-error-message",
        "Tidak Bisa membatalkan karena sudah melakukan pembayaran"
      );
    }
  }
  // console.log(primaryKey);
});

$("#btn-batal").on("click", function () {
  if ($("#btn-batal").attr("data-options") == "click") {
    docoNotification(
      "error",
      "Gagal Melakukan Batal",
      $("#btn-batal").attr("data-error-message")
    );
  }
});

// Event Ready
$(document).ready(function () {
  $("#btn-batal").prop("disabled", true);

  // Generate Table
  table = $("#example").docoTabel({
    filter: true,
    sorting: [[1, "desc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    // fixedColumns: {
    //     leftColumns: 1
    // },
    ajax: function (data, callback, settings) {
      $.ajax({
        url:
          baseUrl +
          "laboratorium/laporan-pasien-lab/get-data?ruangan_default=" +
          _ruanganDefault,
        data: data,
        success: function (data) {
          callback(data);
          _ruanganDefault = "";
        },
      });
    },
    columns: [
      {
        title: "No",
        data: "rowNum",
        searchable: false,
        orderable: false,
      },
      {
        title: tanggal_pendaftaran_title,
        data: "tglmasukpenunjang",
      },
      {
        title: no_pendaftaran_title,
        data: "no_pendaftaran",
      },
      {
        title: no_rekam_medik_title,
        data: "no_rekam_medik",
        name: "no_rekam_medik",
      },
      {
        title: nama_pasien_title,
        data: "nama_pasien",
      },
      {
        title: tanggal_lahir_title,
        data: "tanggal_lahir",
      },
      {
        title: dokter_title,
        data: "dokter_penunjang",
      },
      {
        title: cara_bayar_title,
        data: "carabayar_nama",
      },
      {
        title: penjamin_title,
        data: "penjamin_nama",
        name: "penjamin_nama",
      },
      {
        title: no_lab_title,
        data: "no_masukpenunjang",
      },
      {
        title: asal_rujukan_title,
        data: "asalrujukan_id",
        name: "asalrujukan_id",
        render: function (data, type, row) {
          return row.asalrujukan_nama;
        },
      },
      {
        title: status_title,
        data: "status_periksa",
      },
      {
        title: harga_title,
        data: "harga",
        orderable: false,
      },
      {
        title: ruangan_title,
        data: "ruangan_id",
        name: "ruangan_id",
        render: function (data, type, row) {
          return row.ruangan_nama;
        },
      },
    ],
  });
  $(".dataTables_filter").hide();
  $(".filter-form").datatableBootstrapFilter(
    table,
    [
      [1, filterTanggalPendaftaran],
      [5, filterTanggalLahir],
      [6, autocompleteDokter],
      [7, dropdownCaraBayar],
      [8, dropdownPenjamin],
      [10, dropdownAsalRujukan],
      [11, dropdownStatus],
      [13, dropdownRuangan],
    ],
    {
      1: 0, // tgl pendaftaran
      5: 1, // tgl lahir
      4: 2, // nama pasien
      3: 3, // no rekam medis
      2: 4, // no pendaftaran
      6: 5, // dokter
      7: 6, // cara bayar
      8: 7, // penjamin
      9: 8, // no lab
      10: 9, // asal rujukan (instalasi)
      11: 10, // status
      13: 11, // ruangan_id
    },
    true
  );
  // Generate Table

  dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");
  dateRangeHelper(".startDate", ".endDate", ".targetDate");

  $(".daterange-basic").daterangepicker({
    startDate: '<?=(date("01-M-Y"))?>',
    autoUpdateInput: true,
    endDate: '<?=(date("d-M-Y"))?>',
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
      format: "DD-MMMM-YYYY",
    },
  });

  // select ruangan
  $("#select-ruangan").select2InfinityScroll({
    url: "/laboratorium/laporan-pasien-lab/get-filters?type=ruangan",
    callbackData: (param) => {
      return {
        payload: {
          ...param,
          ruangan_id: $("#select-ruangan").val(),
        },
      };
    },
    callbackProccess: (data) => {
      data.results.unshift({
        id: "",
        text: semuaRuanganText,
      });
      return data;
    },
  });

  $("#select_asalrujukan").select2InfinityScroll({
    url: "/laboratorium/laporan-pasien-lab/get-filters?type=asalrujukan",
    callbackData: (param) => {
      return {
        payload: {
          ...param,
          asalrujukan_id: $("#select_asalrujukan").val(),
        },
      };
    },
    callbackProccess: (data) => {
      data.results.unshift({
        id: "",
        text: semuaRuanganText,
      });
      return data;
    },
  });
});
