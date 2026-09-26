// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    // $("#btn-edit").prop("disabled", true);
    $("#btn-delete").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-info-slider tbody tr", function() {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    // Assign to ubah
    console.log(primaryKey);
    $("#btn-update").attr("data-target", updateUrl + primaryKey);
    $("#btn-delete").attr("action", deleteUrl + primaryKey);

    // Check class selected
    if ($('#tb-info-slider tr.selected').length == 0) {
        // Disable edit button
        $("#btn-update").prop("disabled", true);
        $("#btn-delete").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-update").prop("disabled", false);
        $("#btn-delete").prop("disabled", false);
    }
});


// Event Ready
$(document).ready(function() {
    // Generate Table

    $(function () {
        $(".pickadate").pickadate({
            formatSubmit: "dd-m-yyyy",
        });
    })

    table = $("#tb-info-slider").docoTabel({
        filter: true,
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
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "master/info-slider-rumah-sakit/get-data",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
                width: "7%",
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: judul, data: "judul"},
            { title: gambar, data: "img_show",searchable: false },
            {title: tgl_mulai, data: "tgl_mulai" ,searchable: true },
            {
                title: tgl_selesai,
                data: "tgl_selesai",
                searchable: true
            },
,
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
        // [6, filterTanggalSlider],
        [4, filter_tanggal_mulai],
        [5, filter_tanggal_selesai]
    ]);

    dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");

    $(".daterange-basic").daterangepicker({
        startDate: '<?=(date("01-M-Y"))?>',
        autoUpdateInput: true,
        endDate: '<?=(date("d-M-Y"))?>',
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

});