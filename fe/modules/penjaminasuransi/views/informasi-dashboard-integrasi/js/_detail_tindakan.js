$(document).ready(function () {
    let tableTindakan = $(`#table-expand-tindakan-${kelompoktindakanId}`).docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        processing: true,
        serverSide: true,
        scrollY: "300px",
        scrollCollapse: true,
        ajax: {
            url: "/penjamin-asuransi/informasi-dashboard-integrasi/get-grouping-detail",
            data: function (d) {
                d.pendaftaran_id = pendaftaranId,
                d.kelompoktindakanId = kelompoktindakanId,
                d.isobat = isobat,
                d.penjamin_id = penjaminId,
                d.no_klaim = noKlaim
            }
        },
        columns: [
            {
                data: "rowNum",
                name: "rowNum",
                searchable: false,
                orderable: false, // 0
            },
            {
                title: "Nama Item",
                data: "tindakan_obat_nama",
                name: "tindakan_obat_nama",
                searchable: false
            },
            {
                title: "Kode Item",
                data: "daftartindakan_kode",
                name: "daftartindakan_kode",
                orderable: false, // 1
                searchable: false,
                render: function(data) {
                    if (data) {
                        return `<span class="kodetindakan-${data}">${data}</span>`;
                    }

                    return "-";
                }
            },
            {
                title: "No. Pendaftaran",
                data: "no_pendaftaran",
                name: "no_pendaftaran",
                orderable: false, // 2
                searchable: false
            },
            {
                title: "Qty",
                data: "qty",
                name: "qty",
                orderable: false, // 3
                searchable: false,
                className: "text-right",
            },
            {
                title: "Price",
                data: "tarif_satuan",
                name: "tarif_satuan",
                orderable: false, // 4
                searchable: false,
                className: "text-right",
            },
            {
                title: "Sub Total",
                data: "sub_total",
                name: "sub_total",
                orderable: false, // 5
                searchable: false,
                className: "text-right",
            },
            {
                title: "Status",
                data: "status",
                name: "status",
                searchable: false
            }
        ],
        fnRowCallback: function(nRow, aData) {
            $('td', nRow).addClass(`kodetindakan-${aData.daftartindakan_kode}`);
        },
    });

    tableTindakan.on('xhr', function (e, settings, data, response) {
        setTimeout(() => {
            notKodeFoundItem()
        }, 100)
    })

    function notKodeFoundItem() {
        let itemNotFound = JSON.parse(localStorage.getItem("item_not_found"));
        if (!itemNotFound) {
            return
        }
    
        itemNotFound.map((item) => {
            $(`.kodetindakan-${item.daftartindakan_kode}`).css("background", "#FFC0CB");
        })
    }
});
