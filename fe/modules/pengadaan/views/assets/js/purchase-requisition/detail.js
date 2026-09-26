/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

$(document).ready(function() {
    // initialize checkbox property
    $.each(detail, function(key, value) {
        $("#check_"+key).prop("disabled", true);

        if(value.status_id == belum_po || value.status_id == belum_approved) {
            $("#check_"+key).prop("disabled", false);
        }
    });

    if(type.toLowerCase() == 'obat') {
        if(status_pr == belum_approved) {
            var target_not_orderable = [0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
            var target_not_visible = 20;
        } else {
            var target_not_orderable = [0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21];
            var target_not_visible = 21;
        }
    } else {
        // tipe barang
        if(status_pr == belum_approved) {
            var target_not_orderable = [0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19];
            var target_not_visible = 19;
        } else {
            var target_not_orderable = [0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
            var target_not_visible = 20;
        }
    }

    $("#detail-pr").DataTable({
        sorting: [[3, "asc"]],
        filter: false,
        lengthChange: false,
        aLengthMenu: [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ],
        iDisplayLength: -1,
        scrollX: true,
        columnDefs: [
            {
                targets: target_not_visible,
                visible: false,
            },
            {
                targets: target_not_orderable,
                orderable: false,
            },
        ]
    });

    $("#detail-pr tr").on("click", function(e){
        var tag_name = e.target.tagName;
        if(e.target.type != "checkbox" && tag_name.toLowerCase() != "th"){
            var row_id = $(this).attr("id");
            var checked_row = $("#check_"+row_id);
            if(detail[row_id].status_id == belum_po || detail[row_id].status_id ==belum_approved) {
                checked_row.prop("checked", !checked_row.prop("checked"));
                detail[row_id].checked = checked_row.prop("checked");
            }
        }
    });

    $(".checkbox-pr").on("click", function(e){
        var trParent = $(this).closest("tr");
        var trId = trParent.attr("id");
        var checked_row = $("#check_"+trId);
        detail[trId].checked = checked_row.prop("checked");
    });

    $(".checkbox-all-pr").on("change", function(){
        var check = this.checked;
        $.each(detail, function(key, value) {

            if(value.status_id == belum_po || value.status_id == belum_approved) {
                var checked_row = $("#check_"+key);
                checked_row.prop("checked", check);
                detail[key].checked = check;
            }
        });
    });

    $("#btn-cancel").on("click", function (e) {
        $('#modal').modal('show')
            .find('#modal_backdrop')
            .load(detail);
    });

    $("#btn-submit-cancel").on("click", function(){
        var _data = $("#form-cancel-pr").serializeArray();
        var alasan_cancel = $("#alasan_cancel").val();
        var payloadCancel = [];
        var index = 0;
        $.each(detail, function(key, value) {
            if(value.checked) {
                payloadCancel[index] = value;
                payloadCancel[index]["alasan_cancel"] = alasan_cancel;
                index++;
            }
        });

        _data.push({
            name : "list_data",
            value : JSON.stringify(payloadCancel)
        });

        $().docoForm("click",{
            url : "/pengadaan/purchase-requisition/cancel?id="+id+"&type="+type,
            data : _data,
            skipSuccessNotif: true,
            success : function (data) {
                var data = data.response;
                if(data.statusCode == 206) {
                    if(data.data.count_cancel > 0) {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t(data.message));
                        docoNotification('success', i18next.t('Proses Berhasil !'), i18next.t("Data berhasil disimpan"));
                    } else {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t(data.message));
                    }
                } else {
                    docoNotification('success', i18next.t('Proses Berhasil !'), i18next.t(data.message));
                }

                $("#modal").modal("hide");
                $("#alasan_cancel").val("");
                $('#btn-cancel').prop('disabled', 'disabled');
                setTimeout(function(){
                    location.reload();
                }, 3000);
            }
        });
    });

    $("#btn-generate-po-partial").on("click", function(){
        var payloadGeneratePartial = [];
        var index = 0;
        $.each(detail, function(key, value) {
            if(value.checked) {
                payloadGeneratePartial[index] = value.purchasereqdetail_id;
                index++;
            }
        });

        var _data = {
            details: payloadGeneratePartial
        };

        $().docoForm("click",{
            url : "/pengadaan/purchase-requisition/generate-po-partial?id="+id+"&type="+type,
            data : _data,
            skipSuccessNotif: true,
            success : function (data) {
                var data = data.response;
                if(data.statusCode == 206) {
                    if(data.data.generated_po > 0) {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t(data.message));
                        docoNotification('success', i18next.t('Sukses'), i18next.t("Data berhasil disimpan"));
                    } else {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t(data.message));
                    }
                } else {
                    docoNotification('success', i18next.t('Sukses'), i18next.t(data.message));
                }
                $('#btn-generate-po-partial').prop('disabled', 'disabled');
                setTimeout(function(){
                    location.reload();
                }, 3000);
            }
        });
    });
});
