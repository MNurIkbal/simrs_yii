/*
* @Author: Sigit
* @Date:   2018-08-13 13:22:19
*/

$(document).ready(function() {
    jQuery("#btn-back-soap").removeClass("btn-toolbar");
    jQuery("#btn-back-soap-update").removeClass("btn-toolbar");
    jQuery("#btn-save-soap").removeClass("btn-toolbar");
    jQuery("#btn-reset-soap").removeClass("btn-toolbar");

    if(cppt_id === ""){
        $("#btn-back-soap").toggle(true);
        $("#btn-back-soap-update").toggle(false);
    }
    else
    {
        $("#btn-back-soap").toggle(false);
        $("#btn-back-soap-update").toggle(true);
    }
});

$("#btn-save-soap").on("click", function(event) {
    event.preventDefault();

    $(this).docoForm('click', {
        url: '/igd/pemeriksaan-igd/create-soap?id='+pendaftaran_id,
        data: $("#form-soap").serializeArray(),
        success : function(response) {
            $('.tabbable ul li a[href="#view-asesmen-dpjp"]').click();
        }
    });
});

$("#btn-back-soap").on("click", function(event) {
    event.preventDefault();

    $(this).docoForm('click', {
        url: '/igd/pemeriksaan-igd/create-soap?id='+pendaftaran_id+'&draft_id=0',
        data: $("#form-soap").serializeArray(),
        skipConfirm: true,
        skipNotifyMessage: true,
        skipSuccessNotif: true,
        success : function(response) {
        }
    });

    $('.tabbable ul li a[href="#view-asesmen-dpjp"]').click();
});

$("#btn-back-soap-update").on("click", function(event) {
    event.preventDefault();

    $('.tabbable ul li a[href="#view-asesmen-dpjp"]').click();
});

$("#btn-reset-soap").on("click", function(event) {
    event.preventDefault();

    var form = $("#form-soap");
    form[0].reset();
    $("#cpptform-a_diag_utama").select2(null).trigger("change");
    $('#cpptform-is_instruksi_pulang').prop('checked', false);
});