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
        scrollX: true,
        ajax: baseUrl + "fisioterapi/laporan-pasien-fisioterapi-ranap/get-data",
        columns: [
            {
                title: "",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Permintaan",
                data: "tgl_pendaftaran",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Data Pasien",
                data: "nama_pasien",
                render: (data, type, row, meta) => {
                    let jeniskelamin = row.jeniskelamin_kode;
                    let namaPasien = data ? `<b>` + data + `</b>` : '-';
                    let no_rekam_medik = row.no_rekam_medik ? row.no_rekam_medik : '-';
                    return namaPasien + ' ' + '(' + jeniskelamin + ')' + '<br/>' + no_rekam_medik
                }
            },
            {
                title: "Dokter Perujuk",
                data: "dokterperujuk_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let dokterPerujuk = row.dokterperujuk_nama ? row.dokterperujuk_nama : '-';
                    return dokterPerujuk;
                }
            },
            {
                title: "Dokter DPJP",
                data: "dokterdpjp_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let dokterDpjp = row.dokterdpjp_nama ? row.dokterdpjp_nama : '-';
                    return dokterDpjp;
                }
            },
            {
                title: "Ruangan Asal",
                data: "ruangan_id",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => {
                    let ruanganNama = row.ruangan_nama ? row.ruangan_nama : '-';
                    return ruanganNama;
                },
            },
            {
                title: "Jenis Pemeriksaan",
                data: "jenispemeriksaanfisio_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let jenisPemeriksaanNama = row.jenispemeriksaanfisio_nama ? row.jenispemeriksaanfisio_nama : '-';
                    return jenisPemeriksaanNama;
                },
            },
            {
                title: "Program",
                data: "terapi_id",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => {
                    let programNama = row.terapi_nama ? row.terapi_nama : '-';
                    return programNama;
                },
            },
            {
                title: "Frekuensi",
                data: "frekuensi",
                searchable: false,
                orderable: false
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
                data: "status_program_fisio_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let statusNama = row.status_program_fisio_nama ? row.status_program_fisio_nama : '-';
                    return statusNama;
                },
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
                title: "Tanggal Realisasi",
                data: "tgl_realisasi",
                searchable: false,
                orderable: false,
            },
            {
                title: "Terapis",
                data: "terapis_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let terapisNama = row.terapis_nama ? row.terapis_nama : '-';
                    return terapisNama;
                },
            },
        ],
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table, [
        [1, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [2, '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik"></div>'],
        [3, '<div class="form-group"><select class="form-control" name="dokterDpjp" id="dokterDpjp"></select></div>'],
        [4, '<div class="form-group"><select class="form-control" name="dokterPerujuk" id="dokterPerujuk"></select></div>'],
        [6, formFilter.jenis_pemeriksaan],
        [12, formFilter.status_program],
        [15, '<div class="form-group"><select class="form-control" name="dokterTerapis" id="dokterTerapis"></select></div>'],
    ], {
        1: 0,
        2: 1,
        3: 2,
        4: 3,
        6: 4,
        12: 5,
        15: 6,
    }, true);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $("#dokterDpjp").select2({
        placeholder: "Cari Berdasarkan Dokter",
        data: dataFilter.listDokterFisio,
    });

    $("#dokterPerujuk").select2({
        placeholder: "Cari Berdasarkan Dokter",
        data: dataFilter.listDokter,
    });

    $("#dokterTerapis").select2({
        placeholder: "Cari Berdasarkan Dokter",
        data: dataFilter.listDokterFisio,
    });

    $("#dokterDpjp").val('Semua').trigger("change");
    $("#dokterPerujuk").val('Semua').trigger("change");
    $("#dokterTerapis").val('Semua').trigger("change");

    $(document).on("click", ".data-reset", function () {
        $("#dokterDpjp").val('Semua').trigger("change");
        $("#dokterPerujuk").val('Semua').trigger("change");
        $("#dokterTerapis").val('Semua').trigger("change");
    });


    $('.nav-link').on('click', function () {
        var _type = $(this).attr("data-type");
        const tableId = "example";
        const tableElement = $(`#${tableId}`).DataTable();
        tableElement.clear();
        if (_type == "ranap") {
            showLoader();
            window.location.href = '/fisioterapi/laporan-pasien-fisioterapi-ranap';
        } else {
            showLoader();
            window.location.href = '/fisioterapi/laporan-pasien-fisioterapi-rajal';
        }
    });
});
