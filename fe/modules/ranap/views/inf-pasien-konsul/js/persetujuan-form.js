/*$(document).ready(function () {
    $("#persetujuan").select2 ('container').find ('.select2-search').addClass ('hidden') ; 
});*/
$(document).ready(function() {
    $("#btn-setuju").on("click", function(event) {
        event.preventDefault();
        var data = $("#persetujuan-form").serializeArray();
        $(this).docoForm('click',{
            url: '/ranap/inf-pasien-konsul/setujui',
            data: data,
            success : function(res) {
                var form = $("#persetujuan-form");
                form[0].reset();
                $("#persetujuan").val(0);           
                $("#modal_backdrop").modal("toggle");
                $(".kembali").submit();
                tabel.draw();
            }
        });
    });
});