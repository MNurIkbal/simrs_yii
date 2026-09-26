var table;
var _type = `rajal`;

$(document).ready(() => {
  table = $("#example").docoTabel({
    filter: true,
    sorting: [1, "desc"],
    displayLength: 10,
    scrollX: true,
    processing: true,
    serverSide: true,
    stateSave: false,
    ajax: "/fisioterapi/laporan-pasien-fisioterapi-rajal/get-data-rajal",
    columns: [
      {
        title: "No",
        data: "rowNum",
        searchable: false,
        orderable: false
      },
      {
        title: "Tanggal Permintaan",
        data: "tgl_pendaftaran",
        searchable: true,
        orderable: true,
        render: (data, type, row, meta) => (data ? data : "-")
      },
      {
        title: "Data Pasien",
        data: "nama_pasien",
        render: (data, type, row, meta) => {
          let jeniskelamin = row.jeniskelamin_kode;
          let namaPasien = data ? `<b>` + data + `</b>` : "-";
          let no_rekam_medik = row.no_rekam_medik ? row.no_rekam_medik : "-";
          return (namaPasien + " " + "(" + jeniskelamin + ")" + "<br/>" + no_rekam_medik);
        }
      },
      {
        title: "Dokter Perujuk",
        data: "dokterperujuk_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Dokter Perujuk",
        data: "dokterperujuk_id",
        searchable: true,
        orderable: false,
        visible: false
      },
      {
        title: "Ruangan Asal",
        data: "ruangan_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Ruangan Asal",
        data: "ruangan_id",
        searchable: true,
        orderable: false,
        visible: false
      },
      {
        title: "Jenis Pemeriksaan",
        data: "jenispemeriksaanfisio_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Jenis Pemeriksaan",
        data: "jenispemeriksaanfisio_id",
        searchable: true,
        orderable: false,
        visible: false
      },
      {
        title: "Program",
        data: "terapi_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Frekuensi",
        data: "frekuensi",
        searchable: false,
      },
      {
        title: "Realisasi",
        data: "realisasi",
        searchable: false,
        orderable: false
      },
      {
        title: "Sisa Terapi",
        data: "sisa",
        searchable: false,
        orderable: false
      },
      {
        title: "Tidak Hadir",
        data: "jumlah_ketidakhadiran",
        searchable: false,
        orderable: false
      },
      {
        title: "Status",
        data: "status_program_fisio_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Status",
        data: "status_program_fisio_id",
        searchable: true,
        orderable: false,
        visible: false
      },
      {
        title: "Penjadwalan",
        data: "tgl_penjadwalan_awal",
        searchable: false,
        orderable: false,
        render: (data, type, row, meta) => {
          let tglPenjadwalanAwal = row.tgl_penjadwalan_awal ? row.tgl_penjadwalan_awal : '';
          let tglPenjadwalanAkhir = row.tgl_penjadwalan_akhir ? row.tgl_penjadwalan_akhir : '';
          return tglPenjadwalanAwal + " - " + tglPenjadwalanAkhir;
        }
      },
      {
        title: "Realisasi",
        data: "tgl_realisasi",
        searchable: false,
        orderable: false
      },
      {
        title: "Terapis",
        data: "terapis_nama",
        searchable: false,
        orderable: false
      },
      {
        title: "Terapis",
        data: "terapis_id",
        searchable: true,
        orderable: false,
        visible: false
      }
    ]
  });

  $(".dataTables_filter").hide();

  $(".filter-form").datatableBootstrapFilter(
    table,
    [
      [1, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
      [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
      [4, '<div class="form-group"><select class="form-control" name="dokterPerujuk" id="dokterPerujuk"></select></div>'],
      [6, formFilter.ruangan],
      [8, formFilter.jenisPemeriksaan],
      [15, formFilter.statusProgram],
      [19, '<div class="form-group"><select class="form-control" name="dokterdpjp" id="dokterdpjp"></select></div>'],
    ],
    {
      1: 0,
      2: 1,
      4: 2,
      6: 3,
      8: 4,
      15: 5,
      19: 6
    },
    true
  );

  $(".tanggal_lahir-startDate").val("");
  $(".tanggal_lahir-endDate").val("");
  dateRangeHelper(".startDate", ".endDate", ".targetDate");
  dateRangeHelper(
    ".tanggal_lahir-startDate",
    ".tanggal_lahir-endDate",
    ".tanggal_lahir-targetDate"
  );

  $("#dokterPerujuk").select2({
    placeholder: "Cari Berdasarkan Dokter",
    data: dataFilter.listDokter,
  });

  $("#dokterdpjp").select2({
    placeholder: "Cari Berdasarkan Dokter",
    data: dataFilter.listDokterDpjp,
  });

  $("#statusPeriksa").select2({
    placeholder: "Cari Berdasarkan Status Periksa",
    data: dataFilter.listStatusPeriksa,
  });

  $("#dokterdpjp").val("Semua").trigger("change");
  $("#dokterPerujuk").val("Semua").trigger("change");
  $("#statusPeriksa").val("Semua").trigger("change");
  $("#penjamin").val("Semua").trigger("change");
  $("#caraBayar").val("Semua").trigger("change");
  $("#ruangan").val("Semua").trigger("change");
  $("#instalasi").val("Semua").trigger("change");

  $(document).on("click", ".data-reset", function () {
    $("#dokterdpjp").val("Semua").trigger("change");
    $("#dokterPerujuk").val("Semua").trigger("change");
    $("#statusPeriksa").val("Semua").trigger("change");
    $("#penjamin").val("Semua").trigger("change");
    $("#caraBayar").val("Semua").trigger("change");
    $("#ruangan").val("Semua").trigger("change");
    $("#instalasi").val("Semua").trigger("change");
  });

  $(".nav-link").on("click", function () {
    var _type = $(this).attr("data-type");
    const tableId = "example";
    const tableElement = $(`#${tableId}`).DataTable();
    tableElement.clear();
    if (_type == "ranap") {
      showLoader();
      window.location.href = "/fisioterapi/laporan-pasien-fisioterapi-ranap";
    } else {
      showLoader();
      window.location.href = "/fisioterapi/laporan-pasien-fisioterapi-rajal";
    }
  });
});
