/*
* @Author: Sigit
* @Date:   2018-09-17 11:39:58
*/

var table;

$(document).on("click", "#tb-fasilitas-rs tbody tr", function() {
    try {
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        primaryKey = false;
    }

    $("#btn-edit").attr("action", updateUrl + primaryKey);

    if ($('#tb-fasilitas-rs tr.selected').length == 0) {
        $("#btn-edit").prop("disabled", true);
        $("#btn-delete").prop("disabled", true);
    }
    else {
        $("#btn-edit").prop("disabled", false);
        $("#btn-delete").prop("disabled", false);
    }
});

$(document).ready(function() {
    localStorage.clear();

    table = $("#tb-fasilitas-rs").docoTabel({
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
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "master/fasilitas-rs/get-data-fasilitas-rs",
        rowsGroup: [2, 1, 0],
        columns: [
            {title: "", data: "checkbox", searchable: false, orderable: false},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: jenisFasilitas, data: "nama_jenis"},
            {title: namaFasilitas, data: "nama_fasilitas"},
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
        },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var page_storage = JSON.parse(localStorage.getItem("paging-fasilitas"));
            var number = 0;

            if (page_storage != null) {
                if(Object.values(page_storage).indexOf(aData.jenis_fasilitas) > -1) {
                    number = getKeyByValue(page_storage, aData.jenis_fasilitas);
                    number = number.replace("N", "");
                } else {
                    page_storage["N" + (parseInt(Object.keys(page_storage).length) + 1)] = aData.jenis_fasilitas;
                    number = getKeyByValue(page_storage, aData.jenis_fasilitas);
                    number = number.replace("N", "");
                }
            } else {
                page_storage = {};
                page_storage["N" + (parseInt(Object.keys(page_storage).length) + 1)] = aData.jenis_fasilitas;
                number = getKeyByValue(page_storage, aData.jenis_fasilitas);
                number = number.replace("N", "");
            }

            localStorage.setItem("paging-fasilitas", JSON.stringify(page_storage));
            $("td:eq(1)", nRow).html(number);
            
            return nRow;
        },
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [2, dropdownJenisFasilitas],
    ]);
});

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}