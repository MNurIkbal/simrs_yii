var table;

$(document).on("click", ".data-reload", function () {
    table.draw();
});

$(document).ready(function () {
    // Generate Table
    table = $("#tabel-riwayat").docoTabel({
        filter: true,
        sorting: [],
        displayLength: 50,
        processing: true,
        serverSide: true,
        scrollX: true,
        lengthMenu: [
            [50, 100, 150],
            [50, 100, 150],
        ],
        ajax: `${baseUrl}pendaftaran/informasi-pencarian-pasien/get-data-riwayat-pasien?id=` + id,
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "No Pendaftaran",
                data: "no_pendaftaran",
                searchable: false
            },
            {
                title: "Tanggal Pendaftaran",
                data: "tgl_pendaftaran",
                searchable: false

            },
            {
                title: "Tanggal Pulang",
                data: "tglpasienpulang",
                searchable: false
            },
            {
                title: "Ruangan Nama / Instalasi",
                data: "ruangan_nama",
                searchable: false
            },
            {
                title: "Dokter DPJP",
                data: "nama_pegawai",
                searchable: false
            },
            {
                title: "Cara Bayar / Penjamin",
                data: "cara_bayar",
                searchable: false,
                render: function (data, type, row) {
                    let caraBayar = row.carabayar_nama != null ? row.carabayar_nama : ' - ';
                    let penjamin = row.penjamin_nama != null ? row.penjamin_nama : ' - ';
                    return `${caraBayar} / ${penjamin}`;
                }
            },
            {
                title: "Kelas Pelayanan",
                data: "kelaspelayanan_nama",
                searchable: false
            },
            {
                title: "Cara Keluar",
                data: "carakeluar",
                searchable: false
            },
            {
                title: "Status Periksa",
                data: "status_periksa",
                searchable: false
            }
        ]
    });

    $(".dataTables_filter").hide();
});
