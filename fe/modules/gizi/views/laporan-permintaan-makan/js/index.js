/*
* @Author: Sigit
* @Date:   2018-12-20 16:03:25
*/

var table;

$(document).ready(function() {
    table = $("#tb-lap-permintaan-makan").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[1, "desc"]],
        ajax: {
            url: baseUrl + "gizi/laporan-permintaan-makan/get-data-permintaan-makan",
        },
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {title: tgl_permintaanmakan, data: "tgl_permintaanmakan"},
            {title: jenisdiet_nama, data: "jenisdiet_nama"},
            {title: makanandiet_nama, data: "makanandiet_nama"},
            {title: jenis_kelamin, data: "jenis_kelamin", searchable: false, },
            {title: tanggal_lahir, data: "tanggal_lahir", searchable: false, },
            {title: jenisdiet_nama, data: "jenisdiet_nama", searchable: false, },
            {title: diagnosa, data: "diagnosa", searchable: false, },
            {title: riwayat_alergi, data: "riwayat_alergi", searchable: false, },
            {title: penjamin_nama, data: "penjamin_nama", searchable: false, },
            {title: jumlah, data: "jumlah", searchable: false},
        ],
        columnDefs: [{
            targets: 4,
            className: "text-right"
        }],
        scrollCollapse: true,
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var counters = this.api().ajax.json().counters;
            $(api.column(2).footer()).html(counters.count_jenis);
            $(api.column(3).footer()).html(counters.count_makanan);
            $(api.column(10).footer()).html(counters.count_jumlah);
            //var api = this.api(), data;
            // if (data.length > 0) {
            //     temp_jenis = [];
            //     temp_makanan = [];
            //     count_jenis = 0;
            //     count_makanan = 0;
            //     count_jumlah = 0;
            //     for (var i = data.length - 1; i >= 0; i--) {
            //         if (jQuery.inArray(data[i]["jenisdiet_id"], temp_jenis) === -1) {
            //             temp_jenis.push(data[i]["jenisdiet_id"]);
            //             count_jenis = count_jenis + 1;
            //         }

            //         if (jQuery.inArray(data[i]["makanandiet_id"], temp_makanan) === -1) {
            //             temp_makanan.push(data[i]["makanandiet_id"]);
            //             count_makanan = count_makanan + 1;
            //         }

            //         count_jumlah = count_jumlah + parseInt(data[i]["jumlah"]);
            //     }

            //     $(api.column(2).footer()).html(count_jenis);
            //     $(api.column(3).footer()).html(count_makanan);
            //     $(api.column(4).footer()).html(count_jumlah);
            // } else {
            //     $(api.column(2).footer()).html(0);
            //     $(api.column(3).footer()).html(0);
            //     $(api.column(4).footer()).html(0);
            // }

            // $.ajax({
            //     url: "/gizi/laporan-permintaan-makan/get-data-count",
            //     type: "GET",
            //     dataType: "JSON",
            //     success: function (response) {
            //         $(api.column(2).footer()).html(response.count_jenis);
            //         $(api.column(3).footer()).html(response.count_makanan);
            //         $(api.column(4).footer()).html(response.count_jumlah);
            //     },
            //     error: function (error) {
            //         docoNotification("error", "Peringatan!", "Penjumlahan total Error!");
            //         $(api.column(2).footer()).html("0 dari total 0");
            //         $(api.column(3).footer()).html("0 dari total 0");
            //         $(api.column(4).footer()).html("0 dari total 0");
            //     }
            // });
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [1, inputTanggal],
    ]);

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});