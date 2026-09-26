var table;
var _type = `ranap`;
$(document).ready(() => {
    table = $("#example").docoTabel({
        select: {
            style: "single",
            selector: "tr"
        },
        filter: true,
        sorting: [1, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        // scrollX: true,
        ajax: baseUrl + "fisioterapi/laporan-kunjungan-fisioterapi-ranap/get-data-ranap",
        columns: [
            {
                title: "",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Pemeriksaan",
                data: "tgl_permintaan",
                searchable: true,
                orderable: true,
                visible: true,
            },
            {
                title: "Data Pasien",
                data: "nama_pasien",
                width: '20%',
                render: (data, type, row, meta) => {
                    let namaPasien = `<b>` + data + `</b>`;
                    return namaPasien + ' ' + '(' + row.jeniskelamin_kode + ')' + `</br>` + row.tanggal_lahir + `</br>` + row.no_rekam_medik
                }
            },
            {
                title: "Tanggal Lahir",
                data: "tanggal_lahir",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => {
                    let tgl_lahir = data ? data: '-';
                    let umur = row.umur ? row.umur : '-';
                    return tgl_lahir + '<br/>' +  umur;
                }
            },
            {
                title: "Alamat",
                data: "alamat_pasien",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => data ? data : '-'
            },
            { 
                title: "Terapi",
                data: "terapi_nama_lain",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => {
                    let allData = [];
                    let splitDataTerapi = data.split('||');
                    splitDataTerapi.forEach((each) => {
                        let dataPerTerapi = each.split('|');
                        allData.push('<p>' + dataPerTerapi[0] + ' <span><b>' + dataPerTerapi[1] + '</b></span></p>')
                    })
                    return allData.join("")
                }
            },
            { 
                title: "Terapis",
                data: "terapis_nama",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Cara Bayar / Penjamin",
                data: "carabayar_id",
                render: (data, type, row, meta) => {
                    return row.carabayar_nama + ' / ' + row.penjamin_nama;
                },
                searchable: false
            },
            {
                title: "Cara Bayar",
                data: "carabayar_id",
                searchable: true,
                visible: false
            },
            {
                title: "Penjamin",
                data: "penjamin_id",
                searchable: true,
                visible: false,
                orderable: false
            },
            {
                title: "Instalasi / Ruangan",
                data: "ruangan_id",
                render: (data, type, row, meta) => {
                    return row.instalasi_nama + ' / ' + row.ruangan_nama;
                },
                searchable: false
            },
            {
                title: "Instalasi",
                data: "instalasi_id",
                searchable: true,
                visible: false,
                orderable: false
            },
            {
                title: "Ruangan",
                data: "ruangan_id",
                searchable: true,
                visible: false,
                orderable: false
            },
            {
                title: "Dokter",
                data: "dokterdpjp_nama",
                searchable: false,
            },
            {
                title: "Dokter",
                data: "dokterdpjp_id",
                visible: false,
                orderable: false
            },
            {
                title: "Status Periksa",
                data: "status_periksa_nama",
                searchable: false,
                orderable: false
            },
            {
                title: "Status Periksa",
                data: "status_periksa_id",
                visible: false,
                searchable: true,
                orderable: false
            },
            {
                title: "No Telepon Pasien",
                data: "no_telepon_pasien",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Lahir",
                data: "tanggal_lahir",
                visible: false,
                searchable: true,
                orderable: false
            },
        ],
    });

    // document.getElementById('data-pasien').innerText = "Data Pasien";
    // document.getElementById('status-bayar').innerText = "Status Bayar";
    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table, [
        [1, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
        [8, formFilter.caraBayar],
        [9, formFilter.penjamin],
        [11, formFilter.instalasi],
        [12, formFilter.ruangan],
        [14, '<div class="form-group"><select class="form-control" name="dokterdpjp" id="dokterdpjp"></select></div>'],
        [16, '<div class="form-group"><select class="form-control" name="statusPeriksa" id="statusPeriksa"></select></div>'],
        [18, '<div class="input-group"><input type="text" id="tanggal_lahir-startDate" class="form-control tanggal_lahir-startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="tanggal_lahir-endDate" class="form-control tanggal_lahir-endDate" /><input type="text" style="display:none" class="tanggal_lahir-targetDate"></div>'],
    ], {
        1: 0,
        2: 1,
        8: 2,
        9: 3,
        11: 4,
        12: 5,
        14: 6,
        16: 7,
        18: 8,
    }, true);

    $(".tanggal_lahir-startDate").val("");
    $(".tanggal_lahir-endDate").val("");
    dateRangeHelper(".startDate", ".endDate", ".targetDate");
    dateRangeHelper(".tanggal_lahir-startDate", ".tanggal_lahir-endDate", ".tanggal_lahir-targetDate");

    $("#dokterdpjp").select2({
        placeholder: "Cari Berdasarkan Dokter",
        data: dataFilter.listDokter,
    });

    $("#statusPeriksa").select2({
        placeholder: "Cari Berdasarkan Status Periksa",
        data: dataFilter.listStatusPeriksa,
    });

    $("#dokterdpjp").val('Semua').trigger("change");
    $("#statusPeriksa").val('Semua').trigger("change");
    $("#penjamin").val('Semua').trigger("change");
    $("#caraBayar").val('Semua').trigger("change");
    $("#ruangan").val('Semua').trigger("change");
    $("#instalasi").val('Semua').trigger("change");

    $(document).on("click", ".data-reset", function () {
        $("#dokterdpjp").val('Semua').trigger("change");
        $("#statusPeriksa").val('Semua').trigger("change");
        $("#penjamin").val('Semua').trigger("change");
        $("#caraBayar").val('Semua').trigger("change");
        $("#ruangan").val('Semua').trigger("change");
        $("#instalasi").val('Semua').trigger("change");
    });

    $('.nav-link').on('click', function () {
        var _type = $(this).attr("data-type");
        const tableId = "example";
        const tableElement = $(`#${tableId}`).DataTable();
        tableElement.clear();
        if (_type == "ranap") {
            showLoader();
            window.location.href = '/fisioterapi/laporan-kunjungan-fisioterapi-ranap';
        } else {
            showLoader();
            window.location.href = '/fisioterapi/laporan-kunjungan-fisioterapi-rajal';
        }
    });
});
