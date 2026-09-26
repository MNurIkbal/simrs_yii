
$(document).ready(function(){
    $('#content-rujukan').docoLoad({
        url: baseController + 'rujukan',
        dataType: 'html',
        success : function(data) {
            $(" .select2 ").select2();
            var tableRujukan;
        }
    });

    $(document).on('click', '.spa', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        var render = $(this).attr('data-render');
        var target = $(this).attr('data-target');
        var tab = $(this).attr('data-tab');
        var action = $(this).attr('action');
        var form_id = $(this).attr('form-id');
        var contentTarget = $(target + " div").attr("id");
        var urlRender;

        if (type == 'wp') {
            var tableId = $(this).attr('data-table');
            var table = $(tableId).DataTable();
            var tableData = table.row(".selected").data();
            if (typeof tableData !== 'undefined') {
                var primaryId = tableData.primary;
                if (typeof primaryId !== 'undefined') {
                    urlRender = baseController + render + primaryId;
                }
            } else {
                docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
                return false;
            }
        } else {
            urlRender = baseController + render;
        }
        
        if (typeof action !== 'undefined') {
            var data_form = $('#' + form_id).serializeArray();
            if(form_id == 'form-approve') {
                if (_listApprove.tindakan.length == 0 && _listApprove.paket.length == 0) {
                    docoNotification('error', 'Terjadi kesalahan pada input.', 'Tidak ada Pemeriksaan yang akan di Approve!')
                    return false;
                 }
                 data_form.push({
                    name: 'list_approved',
                    value: JSON.stringify(_listApprove)
                })
            }
            else if(form_id == 'form-batal') {
                if (_listBatal.tindakan.length == 0 && _listBatal.paket.length == 0) {
                    docoNotification('error', 'Terjadi kesalahan pada input.', 'Tidak ada Pemeriksaan yang akan di Batal!')
                    return false;
                }
                data_form.push({
                    name: 'list_batal',
                    value: JSON.stringify(_listBatal)
                })
            }
            else if(form_id == 'form-order-obat') {
                data_form.push(
                    {
                       name: "OrderObatAlkesForm[pemeriksaan_id]",
                       value: $("#pemeriksaan_id").val()
                    }
                );
                data_form.push(
                    {
                       name: "OrderObatAlkesForm[tindakan_id]",
                       value: $("#tindakan_id").val()
                    }
                 );
                data_form.push(
                    {
                       name: "OrderObatAlkesForm[jenis]",
                       value: 'tindakan'
                    }
                 );
            }
            $(this).docoForm("click", {
                data: data_form,
                url: action,
                method: 'POST',
                success: function (data) {
                    $('#' + tab).trigger('click')
                }
            });
        } else {
            $('#' + contentTarget).docoLoad({
                url: urlRender,
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });

    $('#tab-non-rujukan').on('click', function(){
        $('#content-non-rujukan').docoLoad({
            url: baseController + 'pasien-lab',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
                $("#content-riwayat").empty()
                var tablePasienLab;
            }
        });
    });

    $('#tab-riwayat').on('click', function(){
        $('#content-riwayat').docoLoad({
            url: baseController + 'riwayat',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
                $("#content-non-rujukan").empty()
                var tableRiwayat;
            }
        });
    });

    $('#tab-batal').on('click', function(){
        $('#content-batal').docoLoad({
            url: baseController + 'batal',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
                var tableBatal;
            }
        });
    });

    $('#tab-rujukan').on('click', function(){
        $('#content-rujukan').docoLoad({
            url: baseController + 'rujukan',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
                var tableRujukan;
            }
        });
    });
})