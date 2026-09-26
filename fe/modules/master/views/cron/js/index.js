/*
* @Author: Sigit
* @Date:   2018-09-19 13:37:55
* @Edited: ali.padilah@docotel.com
*/

var table;

$(document).ready(function() {
    table = $("#tb-cron").docoTabel({
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
        ajax: baseUrl + "master/cron/get-data-cron",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: namaCron, data: "cron_nama"},
            {title: tanggalPembuatan, data: "cron_tgl_mulai", searchable: false},
            {title: tanggalTerakhirUpdate, data: "last_modified_date", searchable: false},
            {title: createdBy, data: "created_by", searchable: false},
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

    ]);

    $(document).on("click","#btn-sinkronisasi", function(){
        var tableData = table.row(".selected").data();
        if(typeof tableData != "undefined"){
            var _primary = tableData.primary;

            console.log(tableData.primary);

            $.ajax({
                url: $(this).attr("data-target")+_primary,
                type: 'POST',
                success: function (res) {
                    var msg = res.response;
                    var succMessage = "Sinkronasi";
                    if(msg.text != undefined){
                        succText = msg.text;
                    }
                    if(msg.message != undefined){
                        succMessage = msg.message;
                    }

                    if (res.response.text.status == false) {
                        new PNotify({
                            title: succMessage,
                            text: succText.error,
                            addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                            type: 'danger'
                        });
                    } else {
                        new PNotify({
                            title: succMessage,
                            text: 'Sinkronasi Berhasil',
                            addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                            type: 'success'
                        });
                    }
                },
                error: function (res) {
                    var errMessage = 'Gagal Diproses';
                    var errText = 'Terjadi Kesalahan';
                    new PNotify({
                        title: errMessage,
                        text: errText,
                        addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                        type: 'danger'
                    });
                }
            });
        }else{
            docoNotification("warning", "Peringatan", "Belum ada data yang dipilih!");
        }
        
    })

});