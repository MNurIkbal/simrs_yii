var table;
var { form_filters } = dataFilter;
const statusProgramOpenId = dataFilter.statusProgramOpenId;

$(document).on("click", ".data-reset", () => {
    $("#status").val(statusProgramOpenId).trigger("change");
    $("#dokter_perujuk").val('Semua').trigger("change");
    $("#select_widget_kamar_ranap").val('').trigger("change");
    table.draw();
});

$(document).ready(() => {
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[3, "desc"]],
        displayLength: 10,
        stateSave: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "fisioterapi/informasi-program-fisioterapi-ranap/get-data",
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
                    let btnBatalProgram = row.btnBatalProgram
                    let btnEditSchedule = row.btnEditSchedule
                    return editProgram + btnEditSchedule + detailCppt + btnBatalProgram
                },
                searchable: false,
                orderable: false,
                width: '170px'
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
                orderable: false
            },
            {
                title: "Dokter Perujuk",
                data: "dokter_perujuk_id",
                visible: false,
                searchable: true,
                orderable: true
            },
            { 
                title: "Nama Ruangan No.Kamar-No.Bed",
                data: "kamar",
                render: (data, type, row, meta) => {
                    let ruangan = row.noRuangannya ? row.noRuangannya : '-';
                    return ruangan
                },
                searchable: false,
                orderable: false
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
                orderable: false
            },
            {
                title: "Realisasi",
                data: "realisasi",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`
                },
                searchable: false,
                orderable: false
            },
            {
                title: "Sisa Terapi",
                data: "sisa",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`;
                },
                searchable: false,
                orderable: false
            },
            {
                title: "Tidak Hadir",
                data: "jumlah_ketidakhadiran",
                render: (data, type, row, meta) => {
                    return `<p style='text-align: center;'>` + data + `</p>`;
                },
                searchable: false,
                orderable: false
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
            {
                title: "Ruangan",
                data: "ruangan_nama",
                searchable: true,
                orderable: false,
                visible: false
            },
            { 
                title: "Kamar",
                data: "kamar",
                visible: false,
                searchable: true,
                orderable: false,
            },
            { 
                title: "Tempat Tidur",
                data: "no_tempattidur",
                visible: false,
                searchable: true,
                orderable: false,
            },
        ],
    });

    document.getElementById('data-pasien').innerText = "Data Pasien";

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [3, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
        [5, "<div class='form-group'><select id='dokter_perujuk' name='dokter_perujuk'></div>"],
        [8, form_filters.pemeriksaan],
        [16, form_filters.kamarRanap],
        [15, form_filters.ruanganRanap],
        [17, form_filters.tempatTidurRanap],
        [14, "<div class='form-group'><select id='status' name='status'></div>"]
    ], {
        3: 0,
        2: 1,
        5: 2,
        8: 3,
        15: 4,
        16: 5,
        17: 6,
        14: 7,
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