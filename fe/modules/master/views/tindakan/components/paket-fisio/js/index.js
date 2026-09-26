
var { columns, form_filters, belum_po, status_po } = phpVars
var tablePaketFisio;

$(document).ready(function () {
    generateFilter("tab-paket-fisio", "filter-paket-fisio");
    tablePaketFisio = $("#table-paket-fisio").docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/master/tindakan/paket-fisio-get-paket",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
                width: '1%'
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false,
                width: '1%'
            },
            {
                data: "detail",
                searchable: false,
                orderable: false,
            },
            {
                title: 'Kode Paket',
                data: "daftartindakan_kode",
            },
            {
                title: 'Nama Paket',
                data: "daftartindakan_nama",
            },
            {
                title: 'Nama Paket Lainya',
                data: "daftartindakan_namalainnya",
            },
            {
                title: 'Frekuensi',
                data: "frekuensi",
                searchable: false,
                orderable: false,
            },
            {
                title: 'Jumlah',
                data: "jumlah",
                searchable: false,
                orderable: false,
            },
            {
                title: 'Status', data: "is_active",
                render: function (data, type, row) {
                    let currentStatus = `Tidak Aktif`
                    if (data) currentStatus = `Aktif`
                    return currentStatus;
                }
            },
            { title: 'Catatan', data: "catatan" }
        ],
        drawCallback: function (e) {
            var api = this.api();
            for (var i = 0; api.rows().count() > i; i++) {
                var rowData = api.row(i).data();
                var rowNode = api.row(i).node();
            }
        },
    });
    $(".dataTables_filter").hide();
    $(".filter-paket-fisio").datatableBootstrapFilter(tablePaketFisio, [
        [8, form_filters.is_active]
    ],
        {
            3: 0,
            4: 1,
            5: 2,
            8: 3,
            9: 4
        }
    );
    tablePaketFisio.on("select", function (e, dt, type, indexes) {
        if (type === "row") {
            const data = tablePaketFisio.rows(indexes).data();
            var id = data[0].primary;
            if (id) {
                // isDeletable();
            }
        }
    });
});

function isDeletable() {
    const urlCekTransaksi = `/master/tindakan/paket-fisio-cek-transaksi?id=${id}`
    $.ajax({
        type: "GET",
        url: urlCekTransaksi,
        success: function (response) {
            if (response) {
                $("#btn-delete-paket-fisio").attr("disabled", true);
            } else {
                $("#btn-delete-paket-fisio").attr("disabled", false);
            }
        }
    });
}