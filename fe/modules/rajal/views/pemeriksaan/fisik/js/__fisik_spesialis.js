
$(document).ready(function () {
    var _form = $("#form-fisik-spesialis").serializeArray();
    $(document).on("click", "#submit-fisik-spesialis", function() {
        console.log(_form)
        return false
        $(this).docoForm("click",{
            url: "/rajal/pemeriksaan/simpan-fisik-spesialis",
            method: "POST",
            data: _form,
            skipConfirm: true,
            success : function(data) {
                
            }
        });
    });
});