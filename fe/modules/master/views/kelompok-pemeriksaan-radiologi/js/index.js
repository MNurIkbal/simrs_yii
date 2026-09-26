/*
* @Author: Sigit
* @Date:   2018-04-23 13:31:08
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-03 15:02:12
*/

// Global Var
var table;

// Event click
$(document).on("click", ".data-reload", function() {
    table.draw();
});

// Event click
$(document).on("click", "#tb-kelompok-pemeriksaan-rad tbody tr", function() {
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
    if ($('#tb-kelompok-pemeriksaan-rad tr.selected').length == 0) {
        // Disable edit button
        $("#btn-edit").prop("disabled", true);
        $("#btn-delete").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-edit").prop("disabled", false);
        $("#btn-delete").prop("disabled", false);
    }
});

// Event Ready
$(document).ready(function() {
    // Generate Table
    table = $("#tb-kelompok-pemeriksaan-rad").docoTabel({
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
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        scrollX: true,
        ajax: baseUrl + "master/kelompok-pemeriksaan-radiologi/get-data",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
            {title: no, data: "rowNum", searchable: false, orderable: false},
            {title: kode, data: "kode_kelompok", searchable: false},
            {title: kelompokPemeriksaan, data: "nama_kelompok"},	
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
            paginate: {
                first: first,
                last: last,
                next: next,
                previous: previous
            },
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        }
    });

    // Hide datatables filter form
    $(".dataTables_filter").hide();

    // Custom filter
    $(".filter-form").datatableBootstrapFilter(table , []);
});