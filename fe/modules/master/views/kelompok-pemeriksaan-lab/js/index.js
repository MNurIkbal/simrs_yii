/*
* @Author: Sigit
* @Date:   2018-04-23 13:31:08
* @Last Modified by:   Sigit
* @Last Modified time: 2018-04-23 16:48:06
*/

// Global Var
var table;

// Event click
$(document).on("click", ".data-reload", function() {
    table.draw();
});

// Event click
$(document).on("click", "#tb-kelompok-pemeriksaan-lab tbody tr", function() {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    // Assign to ubah
    $("#btn-edit").attr("action", updateUrl + primaryKey);

    // Check class selected
    // if ($('#tb-kelompok-pemeriksaan-lab tr.selected').length == 0) {
    //     // Disable edit button
    //     $("#btn-edit").prop("disabled", true);
    //     $("#btn-delete").prop("disabled", true);
    // }
    // else {
    //     // Disable edit button
    //     $("#btn-edit").prop("disabled", false);
    //     $("#btn-delete").prop("disabled", false);
    // }
});

// Event Ready
$(document).ready(function() {
    // Generate Table
    table = $("#tb-kelompok-pemeriksaan-lab").docoTabel({
        columnDefs: [ {
            // searchable: false,
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl + "master/kelompok-pemeriksaan-lab/get-data",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false,width:'5%'},
            {title: no, data: "rowNum", searchable: false, orderable: false,width:'5%'},
            {title: kode, data: "kode_kelompok"},
            {title: kelompokPemeriksaan, data: "nama_kelompok"},
        ],
        // scrollCollapse: true,
        // language: {
        //     emptyTable: emptyTable,
        //     info: info,
        //     infoEmpty: infoEmpty,
        //     infoFiltered: infoFiltered,
        //     lengthMenu: lengthMenu,
        //     loadingRecords: loadingRecords,
        //     processing: processing,
        //     search: search,
        //     zeroRecords: zeroRecords,
        //     paginate: {
        //         first: first,
        //         last: last,
        //         next: next,
        //         previous: previous
        //     },
        //     aria: {
        //         sortAscending: sortAscending,
        //         sortDescending: sortDescending
        //     }
        // }
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table , [
        [2, Filterkode],
        [3, FilterkelompokPemeriksaan],
    ]);
});