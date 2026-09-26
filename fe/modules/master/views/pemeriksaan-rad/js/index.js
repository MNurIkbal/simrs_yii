/*
* @Author: Sigit
* @Date:   2018-04-26 09:17:32
* @Last Modified by:   Sigit
* @Last Modified time: 2018-04-26 09:17:47
*/

// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-edit").prop("disabled", true);
    $("#btn-delete").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-pemeriksaan-rad tbody tr", function() {
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
    if ($('#tb-pemeriksaan-rad tr.selected').length == 0) {
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
    table = $("#tb-pemeriksaan-rad").docoTabel({
        columnDefs: [{
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
        scrollX: true,
        ajax: baseUrl + "master/pemeriksaan-rad/get-data",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
            {title: no, data: "row", searchable: false, orderable: false},
            {title: kode, data: "pemeriksaanrad_kode", searchable: false},
            {title: namaPemeriksaan, data: "daftartindakan_nama", name: "daftartindakan_id"},
            {title: kelompokPemeriksaan, data: "kelompok_nama", name: "kelompokpemeriksaanrad_id"},
            {title: jenisPemeriksaan, data: "jenispemeriksaanrad_nama", name: "jenispemeriksaanrad_id"},
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
    $(".filter-form").datatableBootstrapFilter(table , [
        [3, dropdownRad],
        [4, dropdownKelompok],
        [5, dropdownJenis],
    ]);
});