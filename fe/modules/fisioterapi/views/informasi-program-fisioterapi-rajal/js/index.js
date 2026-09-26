var table;
const statusProgramOpenId = dataFilter.statusProgramOpenId;

$(document).on("click", ".data-reset", () => {
    $("#status").val(statusProgramOpenId).trigger("change");
    $("#dokter_perujuk").val('Semua').trigger("change");
    table.draw();
});

$(document).ready(() => {
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        stateSave: false,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "fisioterapi/informasi-program-fisioterapi-rajal/get-data",
        columns: [
            {
                title: "",
                data: "rowNum",
                searchable: false,
                orderable: false,
            },
            {
                title: "",
                data: "",
                render: (data, type, row, meta) => {
                    let editProgram = row.btnEditProgram
                    let detailCppt = row.btnDetailCppt
                    let batalProgram = row.btnBatalProgram
                    let editSchedule = row.btnEditSchedule
                    return editProgram + editSchedule + detailCppt + batalProgram
                },
                searchable: false,
                orderable: false,
                width: '165px'
            },
            {
                title: "Nama Pasien / No Rekam Medik",
                data: "nama_pasien",
                render: (data, type, row, meta) => {
                    let namaPasien = `<b>` + row.nama_pasien + `</b>`
                    return namaPasien + ' ' + '(' + row.jenis_kelamin + ')' + '</br>' + row.tanggal_lahir + '</br>' + row.no_rekam_medik
                }
            },
            {
                title: "Tanggal Rujukan",
                data: "tgl_rujukan",
                searchable: true,
                orderable: true
            },
            {
                title: "Dokter Perujuk",
                data: "dokter_perujuk",
                searchable: false,
                orderable: true
            },
            {
                title: "Dokter Perujuk",
                data: "dokter_perujuk_id",
                visible: false,
                searchable: true,
                orderable: true
            },
            {
                title: "Nama Terapi",
                data: "daftartindakan_nama_view",
                searchable: false,
                orderable: true
            },
            {
                title: "Nama Terapi",
                data: "daftartindakan_id",
                visible: false,
                searchable: true,
                orderable: true
            },
            {
                title: "Frekuensi",
                data: "frekuensi",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`
                },
                searchable: false,
                orderable: false,
                // width: '10px'
            },
            {
                title: "Realisasi",
                data: "realisasi",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`
                },
                searchable: false,
                orderable: false,
                // width: '10px'
            },
            {
                title: "Sisa Terapi",
                data: "sisa",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`;
                },
                searchable: false,
                orderable: false,
                // width: '40px'
            },
            {
                title: "Tidak Hadir",
                data: "jumlah_ketidakhadiran",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`;
                },
                searchable: false,
                orderable: false,
                // width: '10px'
            },
            {
                title: "Pembayaran",
                data: "bayar",
                render: (data, type, row, meta) => {
                    let totalBayar = data ? data : 0;
                    return `<p style='text-align: center;'>` + totalBayar + `</p>`;
                },
                searchable: false,
                orderable: false
            },
            {
                title: "Status",
                data: "status_program_fisio_nama",
                searchable: true,
                orderable: true
            },
        ],
    });

    document.getElementById('data-pasien').innerText = "Data Pasien";
    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table, [
        [3, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
        [5, "<div class='form-group'><select id='dokter_perujuk' name='dokter_perujuk'></div>"],
        [7, formFilter.pemeriksaan],
        [13, "<div class='form-group'><select id='status' name='status'></div>"]
    ], {
        3: 0,
        2: 1,
        5: 2,
        7: 3,
        13: 4,
    }, true);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $('#dokter_perujuk').select2({
        placeholder: "Pilih Berdasarkan Dokter Perujuk",
        data: dataFilter.listDokter,
    });

    $("#status").select2({
        placeholder: "Pilih Berdasarkan Status",
        data: dataFilter.listStatus,
    }).val(statusProgramOpenId).trigger("change");

    $("#dokter_perujuk").val('Semua').trigger("change");
    $("#status").val(statusProgramOpenId).trigger("change");

    $('.nav-link').on('click', function () {
        var _type          = $(this).attr("data-type");
        const tableId      = "example";
        const tableElement = $(`#${tableId}`).DataTable();
        tableElement.clear();
        if (_type == "ranap") {
            showLoader();
            window.location.href = '/fisioterapi/informasi-program-fisioterapi-ranap';
        } else {
            showLoader();
            window.location.href = '/fisioterapi/informasi-program-fisioterapi-rajal';
        }
    });
});