var table;
var socket;

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
      style: "multi",
      selector: "tr",
    },
    sorting: [[2, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    scrollY: true,
    ajax: {
      url: "/fisioterapi/rehabilitasi/get-rehabilitasi",
      type: "GET",
      data: function (d) {
        d.pasien_id = rehabVars.pasienId;
      },
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
        data: "tgl_permintaan",
        name: "tgl_permintaan",
        orderable: true, // 2
      },
      {
        title: "Dokter Perujuk",
        data: "dokter_perujuk",
        searchable: true,
        orderable: true, // 3
      },
      {
        title: "Jumlah Terapi",
        data: "frekuensi",
        searchable: false,
        orderable: false, // 4
      },
      {
        title: "Aksi",
        data: "aksi",
        searchable: false,
        orderable: true, // 5
      },
    ],
  });

  $("#check-all").on("click", function () {
    if ($("#check-all:checked").val() === "on") {
      table.rows().select();
    } else {
      table.rows().deselect();
    }
  });

  $("#btn-generate").on("click", function () {
    let selected = table.rows(".selected").data();

    if (selected.length == 0) {
      docoNotification("warning", "Perhatian", "Belum ada data yang dipilih!");
      return false;
    }

    let mappingData = [];
    selected.map((item, index) => {
      mappingData.push({
        pendaftaran_id: item.pendaftaran_id,
        tgl_permintaan: item.tgl_permintaan,
        jam_permintaan: item.tgl_permintaan,
      });
    });

    $().docoForm("click", {
      data: {
        pendaftaranId: rehabVars?.pendaftaranId,
        params: JSON.stringify(mappingData),
        randString: rehabVars.randString,
      },
      confirmMessage:
        "Apakah anda yakin akan melakukan generate program terapi ?",
      url: "/fisioterapi/rehabilitasi/generate-program-terapi",
      skipSuccessNotif: true,
      success: function (data) {
        $("#loading-generate").show();
      },
    });
  });

  $("#btn-cetak-rehabilitasi").on("click", function () {
    window.open(
      `/fisioterapi/rehabilitasi/cetak-program-rehabilitasi?pendaftaran_id=${rehabVars.pendaftaranIdEnc}&randString=${rehabVars.randString}`,
      "_blank"
    );
  });

  connectionSocket();

  async function connectionSocket() {
    let config = await $.getJSON("./../../json/setup.json");
    if (!socket) {
      if (config.origin == "true") {
        socket = io.connect(window.location.origin);
      } else {
        socket = io.connect(config.ip + ":" + config.port);
      }
    }

    const channel = `sync-eklaim:${rehabVars.randString}`;
    socket.off(channel);
    socket.on(channel, (message) => {
      const _data = $.parseJSON(message);
      console.log(_data);
      const status = _data?.status;
      if (status == "success") {
        $("#btn-cetak-rehabilitasi").show();
        $("#btn-cetak-rehabilitasi").removeClass("hidden");
        $("#btn-cetak-rehabilitasi").prop("disabled", false);
        $("#loading-generate").hide();
        docoNotification("success", "Proses Berhasil!", 'Generate Program Terapi Berhasil!');
      } else {
        $("#btn-cetak-rehabilitasi").prop("disabled", true);
        $("#loading-generate").hide();
        docoNotification("warning", "Gagal", _data?.messageProcess);
      }
    });
  }
});

$(document)
  .off("click", ".btn-cetak-history")
  .on("click", ".btn-cetak-history", function (e) {
    e.preventDefault();

    let pendaftaran_id = $(this).data("pendaftaran_id");
    let tgl_permintaan = $(this).data("tgl_permintaan");
    let jam_permintaan = $(this).data("jam_permintaan");
    window.open(
      `/reports/viewer/cetak-program-terapi?pendaftaran_id=${pendaftaran_id}&tgl_permintaan=${tgl_permintaan}&jam_permintaan=${jam_permintaan}`,
      "_blank"
    );
  });
