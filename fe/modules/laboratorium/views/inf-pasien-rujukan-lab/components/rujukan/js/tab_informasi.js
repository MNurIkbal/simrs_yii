
$(document).ready(function(){
    var rujukan = false;
    var non_rujukan = false;
    var riwayat = false;
    var batal = false;
    var baseController = "/laboratorium/inf-pasien-rujukan-lab/";

    $(document).on('click', '.spa', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        var render = $(this).attr('data-render');
        var target = $(this).attr('data-target');
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
            $(this).docoForm("click", {
                data: data_form,
                url: action,
                method: 'POST',
                success: function (data) {
                    setTimeout(function () {
                        $('#' + contentTarget).docoLoad({
                            url: urlRender,
                            dataType: 'html',
                            success : function(data) {
                                $(" .select2 ").select2();
                            }
                        });
                    }, 1000);
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

    $('#content-rujukan').docoLoad({
        url: baseController + 'rujukan',
        dataType: 'html',
        success : function(data) {
            $(" .select2 ").select2();
        }
    });

    $('#tab-non-rujukan').on('click', function(){
        $('#content-non-rujukan').docoLoad({
            url: baseController + 'non-rujukan',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
            }
        });
    });

    $('#tab-rujukan').on('click', function(){
        $('#content-rujukan').docoLoad({
            url: baseController + 'rujukan',
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
            }
        });
    });

    // $('#content-riwayat').docoLoad({
    //     url: baseController + 'riwayat',
    //     dataType: 'html',
    //     success : function(data) {
    //         $(" .select2 ").select2();
    //     }
    // });

    // $('#content-batal').docoLoad({
    //     url: baseController + 'batal',
    //     dataType: 'html',
    //     success : function(data) {
    //         $(" .select2 ").select2();
    //     }
    // });
})