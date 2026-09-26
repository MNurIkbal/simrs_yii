$(document).ready(function() {
    localStorage.clear();
    table = $('#table-patient').docoTabel({
        autoWidth: false,
        filter: true,
        sorting: [[4, 'desc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        // scrollX: true,
        ajax: baseUrl+'master/dashboard-satusehat/get-data',
        columns: [
            {
                data: "select_item",
                searchable: false,
                orderable: false,
                className: "text-center",
            },
            {
                width: '50px',
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Detail', 
                data: 'detail',
                searchable: false,
                orderable: false
            },
            {
                title: 'Status', 
                data: 'status_integrasi',
                orderable: false,
            },
            {
                title: 'Sync ID API', 
                data: 'id',
                orderable: false,
                searchable: false
            },
            {
                title: 'No. Pendaftaran', 
                data: 'no_pendaftaran',
            },
            {
                title: 'Tanggal Sync', 
                data: 'tgl_sync'
            },
            {
                title: 'Tanggal Resend', 
                data: 'tgl_resend'
            },
            {
                title: 'Satu Sehat ID', 
                data: 'satusehat_id',
                searchable: false,
                orderable: false
            },
            {
                title: 'Satu Sehat Type', 
                data: 'type',
            },
            {
                title: 'Satu Sehat State', 
                data: 'state',
            }
        ],
        drawCallback: function(e) {
            var api = this.api();
            for (var i = 0; api.rows().count() > i; i++) {
                var rowData = api.row(i).data();
                var rowNode = api.row(i).node();
                var status_integrasi = rowData.status_integrasi.toLowerCase();
                var primary = rowData.primary;

                if (status_integrasi == "selesai") {
                    $(rowNode).css("background-color", "#ffffff");
                }
                if (status_integrasi == "gagal") {
                    $(rowNode).css("background-color", "#D24D57");
                    $(rowNode).css("color", "#ffffff");
                }
                if (status_integrasi == "diproses") {
                    $(rowNode).css("background-color", "#D1F2EB");
                }
                if (status_integrasi !== "gagal") {
                    $("#select_item-" + primary).prop("disabled", true);
                }
            }
        },
    });
    $('.dataTables_filter').hide();
    $('.filter-form').datatableBootstrapFilter(table, [
        [
            6,
            "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' readonly='true' class='form-control endDate'><input type='text' style='display:none'  class='targetDate'></div>"
        ],
        [
            9,
            _filterType
        ],
        [
            10,
            _filterState
        ],
        [
            3,
            _filterStatus
        ],
    ], {
        6:0,
        5:1,
        9:2,
        10:3,
        3:7
    }, true);

    $(document).on('click','.data-reset',function(){
        location.reload();
        var table = $('#table-patient').DataTable();
        table.state.clear();
        table.draw();
        table = $("#table-patient").dataTable();
        table.fnDraw();
        return false;
    });
    dateRangeHelper('.startDate','.endDate','.targetDate');
    
    $(".resend-all").on("click", function(){
        var rows = table.rows({"search" : "applied"}).nodes();
        for (var i = 0; rows.count() > i; i++) {
            var rowData = rows.row(i).data();
            var rowNode = rows.row(i).node();
            var status_integrasi = rowData.status_integrasi.toLowerCase();
            var primary = rowData.primary;
            if (status_integrasi == "gagal") {
                $(".select_item", rowNode).prop("checked", this.checked);
            }
        }
    });

    $('#resend-satusehat').on('click', function(){
        var _data = [];

        $('.select_item').each(function(){
            if (this.checked == true) {
                _data.push($(this).val());
            }
        });

        if (_data.length === 0) {
            docoNotification('error', i18next.t('Proses Gagal'), i18next.t('Tidak Ada Data yang dapat di Resend'));
        } else {
            var _dataPost = {
                id: _data,
            }

            $.ajax({
                method: 'POST',
                data: _dataPost,
                url: baseUrl+'master/dashboard-satusehat/resend',
                success: function(res) {
                    var res = res.data;
                    if (typeof res.data != "undefined") {
                        var message = res.data.message;
                        docoNotification('success', i18next.t('Proses Berhasil'), i18next.t(message));
                    } else {
                        docoNotification('success', i18next.t('Proses Berhasil'), i18next.t("Proses Resend Berhasil"));
                    }
                    setTimeout(() => {
                        table.draw();
                    }, 3000);
                },
                error: function(res) {
                    // var res = res.responseJSON.data
                    // docoNotification('error', i18next.t('Proses Gagal'), i18next.t(res.message));
                }
            });
        }
    });

    $(document).on('click', '#table-patient tr', function(){
        var disabled_btn_resend = true;
        $('.select_item').each(function(){
            if (this.checked == true) disabled_btn_resend = false;
        });
        $("#resend-satusehat").prop("disabled", disabled_btn_resend);
    });

    $(document).on('click', '#table-patient_paginate', function(){
        $("#resend-satusehat").prop("disabled", true);
    });
});