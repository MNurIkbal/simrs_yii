var table;

var _type = `rajal`;
$(document).ready(() => {
    table = $("#example").docoTabel({
        select: {
            style: "single",
            selector: "tr"
        },
        filter: true,
        sorting: [3, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        // scrollX: true,
        ajax: baseUrl + "fisioterapi/laporan-pemeriksaan-fisioterapi-rajal/get-data-rajal",
        columns: [
            {
                title: "",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Pendaftaran",
                data: "tgl_pendaftaran",
                searchable: true,
                orderable: true,
                visible: true,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Data Pasien", 
                data: "nama_pasien", 
                render: (data, type, row, meta) => {
                    let jeniskelamin = row.jeniskelamin ? row.jeniskelamin : '-';
                    let namaPasien = data ? `<b>` + data + `</b>` : '-';
                    let no_rekam_medik = row.no_rekam_medik ? row.no_rekam_medik : '-';
                    return namaPasien + ' ' + '(' + jeniskelamin + ')' + '<br/>' +  no_rekam_medik
                }
            },
            {
                title: "Cara Bayar / Penjamin",
                data: "carabayar_id",
                render: (data, type, row, meta) => {
                    let carabayarNama = row.carabayar_nama ? row.carabayar_nama : '-';
                    let penjaminNama = row.penjamin_nama ? row.penjamin_nama : '-';
                    return carabayarNama+' / <br/>'+penjaminNama;
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
                title: "Dokter Terapis",
                data: "terapis_nama",
                render: (data, type, row, meta) => {
                    return data ? data : '-';
                },
                searchable: false
            },
            {
                title: "Dokter Terapis",
                data: "terapis_id",
                searchable: true,
                visible: false,
                orderable: false
            },
            {
                title: "Dokter DPJP",
                data: "dokterdpjp_nama",
                searchable: false,
                orderable: false
            },
            {
                title: "Instalasi / Ruangan",
                data: "ruangan_id",
                render: (data, type, row, meta) => {
                    let ruanganNama = row.ruangan_nama ? row.ruangan_nama : '-';
                    let instalasiNama = row.instalasi_nama ? row.instalasi_nama : '-';
                    return instalasiNama+' / <br/>'+ruanganNama;
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
                title: "Jenis Pemeriksaan",
                data: "jenispemeriksaanfisio_nama",
                searchable: false,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Jenis Pemeriksaan",
                data: "jenispemeriksaanfisio_id",
                visible: false,
                orderable: false
            },
            { 
                title: "Nama Pemeriksaan",
                data: "daftartindakan_nama",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Nama Pemeriksaan",
                data: "daftartindakan_id",
                visible: false,
                searchable: true,
                orderable: false
            },
            {
                title: "Jumlah",
                data: "jumlah",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => data ? data : '-'
            },
        ],
        footerCallback: function(row, data, start, end, display) {
            let api = this.api();
            let res = this.api().ajax.json();
            let jumlahTindakan = 0;
            let jumlahKegiatan = 0;
            let jumlahHasil = 0;
            let footer = $(this).append('<tfoot><tr></tr></tfoot>');
            if(res) {
                jumlahKegiatan = res.rowJumlah.jumlah_jeniskegiatan
                jumlahTindakan = res.rowJumlah.jumlah_tindakan
                jumlahHasil = res.rowJumlah.jumlah_hasil
            }

            $(api.column(11).footer()).html(
                'Total Jenis Pemeriksaan ' + jumlahKegiatan
            );

            $(api.column(13).footer()).html(
                'Total Nama Pemeriksaan ' + jumlahTindakan 
            );

            $(api.column(15).footer()).html(
                'Total Jumlah ' + jumlahHasil
            );
        },
    });

    // document.getElementById('data-pasien').innerText = "Data Pasien";
    // document.getElementById('status-bayar').innerText = "Status Bayar";
    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table, [
        [1, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
        [4, formFilter.caraBayar],
        [5, formFilter.penjamin],
        [7, '<div class="form-group"><select class="form-control" name="terapis" id="terapis"></select></div>'],
        [9, formFilter.instalasi],
        [10, formFilter.ruangan],
        [12, formFilter.jenisPemeriksaan],
        [14, formFilter.pemeriksaan],

    
    ], {
        1: 0,
        2: 1,
        4: 2,
        5: 3,
        7: 4,
        9: 5, 
        10: 6, 
        12: 7, 
        14: 8, 
    }, true);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $("#terapis").select2({
        placeholder: "Cari Berdasarkan Dokter Terapis",
        data: dataFilter.listDokter,
    });
    
    $("#statusPeriksa").val('Semua').trigger("change");
    $("#penjamin").val('Semua').trigger("change");
    $("#caraBayar").val('Semua').trigger("change");
    $("#ruangan").val('Semua').trigger("change");
    $("#instalasi").val('Semua').trigger("change");
    $("#terapis").val('Semua').trigger("change");

    $(document).on("click", ".data-reset", function () {
        $("#statusPeriksa").val('Semua').trigger("change");
        $("#penjamin").val('Semua').trigger("change");
        $("#caraBayar").val('Semua').trigger("change");
        $("#ruangan").val('Semua').trigger("change");
        $("#instalasi").val('Semua').trigger("change");
        $("#terapis").val('Semua').trigger("change");
    });
    

    $('.nav-link').on('click', function () {
        var _type = $(this).attr("data-type");
        const tableId = "example";
        const tableElement = $(`#${tableId}`).DataTable();
        tableElement.clear();
        if (_type == "ranap") {
            showLoader();
            window.location.href = '/fisioterapi/laporan-pemeriksaan-fisioterapi-ranap';
        } else {
            showLoader();
            window.location.href = '/fisioterapi/laporan-pemeriksaan-fisioterapi-rajal';
        }
    });
});
