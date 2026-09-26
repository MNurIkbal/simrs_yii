/*
* @Author: Sigit
* @Date:   2018-12-10 17:45:22
*/

var table;

$(document).ready(function() {
    table = $("#tb-riwayat-permintaan-konsul").docoTabel({
        columnDefs: [ {
            searchable: false,
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        displayLength: 10,
        order: [[2, "desc"]],
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "igd/riwayat-pasien/get-data-riwayat-permintaan-konsul?norm="+norm,
        columns: [
            {title: "", data: "check", searchable: false, orderable: false},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: tgl_permintaan, data: "waktu_permintaan", searchable: false},
            {title: no_rekam_medik, data: "no_rekam_medik", searchable: false},
            {title: nama_pasien, data: "nama_pasien", searchable: false},
            {title: jenis_kelamin, data: "jenis_kelamin", searchable: false},
            {title: dokter_dpjp, data: "dok_dpjp", searchable: false},
            {title: cara_bayar, data: "cara_bayar", searchable: false},
            {title: hak_kelas, data: "hak_kelas", searchable: false},
            {title: nama_ruangan, data: "nama_ruangan", searchable: false},
            {title: jenis_konsul, data: "jenis_konsul_nama", searchable: false},
            {title: dokter_konsul, data: "dok_konsul", searchable: false},
            {title: status, data: "status_konsul_nama", searchable: false},
        ],
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
        }
    });

    $(".dataTables_filter").hide();
});

$(document).on("click", "#tb-riwayat-permintaan-konsul tbody tr", function() {
    try {
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        primaryKey = false;
    }
});