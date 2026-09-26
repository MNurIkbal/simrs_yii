/*
* @Author: Sigit
* @Date:   2018-11-27 17:53:29
*/

var table;

$(document).ready(function() {
    table = $("#tb-jenis-diet").docoTabel({
        filter: true,
        displayLength: 10,
        order: [[5, "desc"]],
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "master/jenis-diet/get-data-jenis-diet",
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {title: kode, data: "jenisdiet_kode"},
            {title: jenisDiet, data: "jenisdiet_nama"},
            {title: keterangan, data: "jenisdiet_keterangan", searchable: false, orderable: false},
            {title: status, data: "is_active"},
            {title: id, data: "jenisdiet_id", visible: false, searchable: false},
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
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [4, dropdownStatus],
    ]);
});

$(document).on("click", "#tb-jenis-diet tbody tr", function() {
    try {
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        primaryKey = false;
    }
});

$(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
    var data = table.row( this.closest('tr')).data();
    var id = data.primary;

    $.ajax({
        type: "GET",
        url: "/master/jenis-diet/update-status?id="+id,
        dataType: "json",
        beforeSend : function() {
            var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
            var dialogTemplate = '<div id="confirm-dialog">';
            dialogTemplate += '<div class="dialog-content">';
            dialogTemplate += '<div class="row"><h2 class=\"text-center\"><p class=\"confirm-header-text\"></p></h2></div>';
            dialogTemplate += '</div>';
            dialogTemplate += '</div>';

            $('body').append(overlayTemplate);
            $('body').append(dialogTemplate);
            $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .');
        },
        success: function(response) {
            if (typeof response.metadata.status !== "undefined" && response.metadata.status == 200) {
                docoNotification("success", "Proses Berhasil!", "Status Berhasil Diubah.");
            } else {
                docoNotification("error", "Proses Gagal!", "Status Gagal Diubah.");
            }
        }
    }).done(function() {
        hideQuestionDialog();
        $('body').find('.confirm-dialog-overlay').remove();
        table.draw();
    });
});