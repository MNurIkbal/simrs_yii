/*
* @Author: Sigit
* @Date:   2018-09-26 10:37:32
*/

var table;
var key_start = "key_start";
var key_end = "key_end";

$(document).ready(function() {
    localStorage.removeItem(key_start);
    localStorage.removeItem(key_end);

    table = $("#tb-rujukan-bpjs").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollCollapse: true,
        ajax: baseUrl + "pendaftaran/rujukan-bpjs/get-data",
        sorting: [[3, "desc"]],
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets: 0,
            checkboxes: {
                selectRow: true
            }
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        columns: [
            {data: null, searchable: false, orderable: false, defaultContent: ""},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: noRujukan, data: "no_rujukan", orderable: false},
            {title: tanggalRujukan, data: "tanggal_rujukan"},
            {title: riRj, data: "rujukan", searchable: false, orderable: false},
            {title: noSep, data: "nosep", orderable: false},
            {title: noKartu, data: "nokartuasuransi", searchable: false, orderable: false},
            {title: nama, data: "nama_peserta", searchable: false, orderable: false},
            {title: ppkRujuk, data: "ppkrujukan", searchable: false, orderable: false}
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
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [3, "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value='"+date+"'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value='"+date+"'/><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>"]
    ], {
        0:3,
        1:2,
        2:5
    });

    $("#rangeDemoStart").on("change", function() {
        localStorage.setItem(key_start, $(this).val());
        localStorage.setItem(key_end, $("#rangeDemoFinish").val());
    });

    $("#rangeDemoFinish").on("change", function() {
        localStorage.setItem(key_end, $(this).val());
    });

    $(".data-reset").on("click", function() {
        var rangeDemoFormat = '%e-%b-%Y';
        var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});
        var start_date = new Date(localStorage.getItem(key_start));
        var end_date = new Date(localStorage.getItem(key_end));
        $("#rangeDemoStart").AnyTime_noPicker().val(rangeDemoConv.format(start_date)).AnyTime_picker({format: rangeDemoFormat});
        $("#rangeDemoFinish").AnyTime_noPicker().val(rangeDemoConv.format(end_date)).AnyTime_picker({format: rangeDemoFormat});
    });

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});

$(document).on("click", "#btn-print-rujukan", function(event) {
    event.preventDefault();
    var tableData = table.row(".selected").data();

    if (typeof tableData !== "undefined") {
        if ("primary" in tableData) {
            var target = $(this).attr("data-target");
            var primary = tableData.primary;
            var bpjs = tableData.bpjs_id;

            window.open(target+"?id="+primary+"&bpjs="+bpjs);
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
        }
    } else {
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
    }
});