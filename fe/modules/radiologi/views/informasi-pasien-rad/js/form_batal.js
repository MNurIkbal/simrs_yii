var table;

$(document).on("click", ".data-reset", function () {
    table.draw();

    $("#btn-edit").prop("disabled", true);
    $("#btn-delete").prop("disabled", true);
});

$(document).on("click", "#tb-inf-pasien-rujukan-rad tbody tr", function () {
    try {
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        primaryKey = false;
    }

    $("#btn-edit").attr("action", updateUrl + primaryKey);

    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
        $("#btn-edit").prop("disabled", true);
        $("#btn-delete").prop("disabled", true);
    } else {
        $("#btn-edit").prop("disabled", false);
        $("#btn-delete").prop("disabled", false);
    }
});

// Event Ready
$(document).ready(function () {
    $(".datepicker-months").remove();
    $(".datepicker-years").remove();
    $(".datepicker-decades").remove();
    $(".datepicker-centuries").remove();
    // Generate Table
    table = $("#tb-rencana-pemeriksaan-rad").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [
            [1, "asc"]
        ],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/informasi-pasien-rad/get-data-pemeriksaan?id=" + id,
        columns: [{
                title: no,
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: jenis_pemeriksaan,
                data: "jenispemeriksaanrad_nama",
                searchable: false
            },
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
    $(".filter-form").datatableBootstrapFilter(table, [

    ]);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

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

    $('#btn-kembali').on('click', function () {
        $(location).attr('href', redirectUrl);
    });

    $('#btn-simpan').on('click', function () {
        $("#form").submit();
    });

    $("#form").docoForm('submit', {
        success: function (data) {
            if (data.metadata.status == 201) {
                // Reset form
                $("#form")[0].reset();

                // Draw table
                table.draw();
            } else if (data.metadata.status == 200) {
                // Draw table
                table.draw();
                setTimeout(function () {
                    $(location).attr('href', redirectUrl);
                }, 2000);
            }
        }
    });

});