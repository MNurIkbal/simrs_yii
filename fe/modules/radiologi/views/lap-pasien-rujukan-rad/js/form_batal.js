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
$(document).on("click", "#tb-inf-pasien-rujukan-rad tbody tr", function() {
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
    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
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
    
    setTimeout(() => {
        // $(".datetimepicker-hours").remove();
        // $(".datetimepicker-minutes").remove();
    }, 10);
    // Generate Table
    table = $("#tb-rencana-pemeriksaan-rad").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data-pemeriksaan?id="+id,
        columns: [
            {
                title: no, data: "rowNum", 
                searchable: false, 
                orderable: false},
            { 
                title: jenis_pemeriksaan, 
                data: "jenispemeriksaanrad_nama", 
                searchable: false 
            },
            { 
                title: "Pemeriksaan", 
                data: "daftartindakan_nama", 
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
    $(".filter-form").datatableBootstrapFilter(table , [
   
    ]);

    dateRangeHelper(".startDate");

    /*$(".daterange-basic").daterangepicker({
        minDate: '<?=(date("D-M-Y"))?>',
        startDate: '<?=(date("D-M-Y"))?>', autoUpdateInput: true,
        endDate: '<?=(date("d-M-Y"))?>',
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });*/
    // $(".startDate").datepicker();

    $(document).on('click', "td.day.active", function () {
        alert("test");
    });

    $('#btn-kembali').on('click', function () {
        $(location).attr('href', redirectUrl);
    });

    $('#btn-simpan').on('click',function(){
         $("#form").submit();
    });

    $("#form").docoForm('submit', {
        success: function (data) {
            // $('#modal_backdrop').modal('toggle');
            // $("#btn-edit").prop("disabled", true);
            // $("#btn-delete").prop("disabled", true);
            // Check data status
            if (data.metadata.status == 201) {
                // Reset form
                $("#form")[0].reset();

                // Draw table
                table.draw();
            } else if (data.metadata.status == 200) {
                // Draw table
                table.draw();
                setTimeout(function () {
                    // var url = "http://jquery4u.com";
                    $(location).attr('href', redirectUrl);
                    // alert("Boom!");
                }, 2000);
                /*
                
                     */
            }
        }
    });
   
});