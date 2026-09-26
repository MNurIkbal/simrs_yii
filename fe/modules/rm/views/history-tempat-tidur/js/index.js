/*
* @Author: Sigit
* @Date:   2019-02-18 14:02:23
*/

var table;

$(document).ready(function() {
    // localStorage.clear();
    localStorage.setItem("ruangan", ruangan);
    localStorage.setItem("kamar", kamar);

    table = $("#tb-history-tempat-tidur").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        order: [[1, "desc"]],
        ajax: baseUrl + "rm/history-tempat-tidur/get-data-history-tempat-tidur",
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {title: tgl_tthistory, data: "tgl_tthistory"},
            {title: ruangan_nama, data: "ruangan_nama", name: "ruangan_id"},
            {title: kamarruangan_nokamar, data: "kamarruangan_nokamar", name: "kamarruangan_id"},
            {title: no_tempattidur, data: "no_tempattidur"},
            {title: keterangan, data: "keterangan", name: "keterangan_id"},
            // {title: status, data: "status"},
        ],
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
        rowCallback: function(row, data, index) {
            if (data['status'] == 'Aktif') {
                $(row).find('td:eq(6)').css('color', 'white');
                $(row).find('td:eq(6)').css('background-color', '#26A65B');
            } else {
                $(row).find('td:eq(6)').css('color', 'white');
                $(row).find('td:eq(6)').css('background-color', '#D24D57');
            }
        },
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [1, inputTanggal],
        [2, dropdownRuangan],
        [3, dropdownKamar],
        [5, dropdownKeterangan],
        // [6, dropdownStatus],
    ], {
        1: 0,
        2: 1,
        3: 2,
        4: 3,
        5: 4,
        // 6: 5,
    });

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});