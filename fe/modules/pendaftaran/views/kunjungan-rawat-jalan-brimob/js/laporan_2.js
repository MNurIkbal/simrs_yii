// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-inf-pasien-rujukan-rad tbody tr", function() {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPenunjang = table.row(".selected").data().status_penunjang ? table.row(".selected").data().status_penunjang : null;
        statusBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;
                        // docoNotification('error', errTitle, errMsg);
    } catch (e) {
        // Make it false
        primaryKey = false;
        statusPenunjang = false;
        statusPeriksa = false;
    }
    // console.log(statusPeriksa);
    if (statusPeriksa != 477 && statusPeriksa) {
        statusPeriksa = true;
    } else {
        statusPeriksa = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);

    // Check class selected
    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
        // Disable edit button
        $("#btn-approve").prop("disabled", true);
        $("#btn-batal").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-approve").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');

        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        if (statusPenunjang == '471'){
            $("#btn-approve").prop("disabled", true);
        }
        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == '472') {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").prop("disabled", true);
        }
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == '474' || statusPeriksa) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');            
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah mengambil sampel");
        }
        if(statusBayar){
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah melakukan Pembayaran");
        } else {
            if (statusPeriksa) {
                $("#btn-batal").attr("data-options", 'click');
                $("#btn-batal").attr("data-target", "#");
                $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah diperiksa");
            }
        }
    }
});

$("#btn-batal").on('click',function(){
    if ($("#btn-batal").attr("data-options") == "click"){
        docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-pesan-error"));
    }
});

// Event Ready
$(document).ready(function() {
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    // Generate Table
    table = $("#tb-inf-pasien-rujukan-rad").docoTabel({
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
        fnRowCallback: function (nRow, data, iDisplayIndex, iDisplayIndexFull) {
            // if (data['stat_penunjang'] == "SUDAH DISETUJUI") {
            //     $('td', nRow).css({
            //         'background-color': '#26A65B',
            //         'color': '#ffffff'
            //     });
            // } else if (data['stat_penunjang'] == "BATAL") {
            //     $('td', nRow).css({
            //         'background-color': '#D24D57',
            //         'color': '#ffffff'
            //     });
            // }
            // console.log(data['stat_penunjang']);
        },
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
            {title: no, data: "rowNum", searchable: false, orderable: false},
            {title: no_rekam_medik, data: "no_rekam_medik", searchable: true},
            {
                title: nama_pasien,
                data: "nama_pasien",
                searchable: true
            },
            { title: tgl_rujukan, data: "tgl_rujukan", searchable: true },
            { title: no_rujukan, data: "no_rujukan", searchable: true },
            { title: tanggal_lahir, data: "tanggal_lahir", searchable: true },
            { title: ruangan_nama, data: "ruangan_nama", searchable: true },
            { title: dokter_perujuk, data: "dokter_perujuk", searchable: true },
            {
                title: carabayar_nama, 
                data: "carabayar_nama", 
                searchable: true 
            },
            {
                title: penjamin_nama,
                data: "penjamin_nama",
                searchable: true
            },
            {
                title: status_penunjang,
                data: "status_periksa_btn",
                name : "stat_penunjang",
                searchable: true
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
        [4, filterTanggalRujukan],
        [6, filterTanggalLahir],
        [7, dropdownRujukan],
        [8, autocompleteDokter],
        [9, dropdownCaraBayar],
        [10, dropdownPenjamin],
        [11, dropdownStatus],
    ],{
        2:2,
        3:1,
        4:0,
        5:3,
        6:4,
        7:5,
        8:6,
        9:7,
        10:8,
        11:9,
    });

    dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");
    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(".daterange-basic").daterangepicker({
        startDate: '<?=(date("01-M-Y"))?>', autoUpdateInput: true,
        endDate: '<?=(date("d-M-Y"))?>',
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

});