/*
* @Author: Sigit
* @Date:   2018-12-14 16:58:57
*/
$("#btn-batal").on("click", function(event) {
    event.preventDefault();

    $(this).docoForm('click', {
        url: "/gizi/inf-permintaan-makan/batal",
        data: $("#form").serializeArray(),
        method: "POST",
        success : function(data) {
            location.reload();
        }
    });
});

$("#btn-pembatalan").on("click", function(event) {
    event.preventDefault();
    if ($("#div-pembatalan").hasClass("hidden")) {
        $("#div-pembatalan").removeClass("hidden");
        $(this).addClass('hidden');
    } else {
        $("#div-pembatalan").addClass("hidden");
    }
});
$("#btn-batal-simpan").on("click", function(e){
    e.preventDefault();
    if (!$("#div-pembatalan").hasClass("hidden")) {
        $("#div-pembatalan").addClass("hidden");
        $("#btn-pembatalan").removeClass('hidden');
    }
})