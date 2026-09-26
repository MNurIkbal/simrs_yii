$(document).ready(function() {
    var rujukKala = $('input[type=radio][name="PersalinanForm\\[rujuk_kala\\]"]:checked', '#form-keadaan-umum').val();
    var kelompokPegawai = [1, 2, 3]
    $.ajax({
        url: '/igd/end-point/get-pegawai-data',
        data: {
            idKelompok: JSON.stringify(kelompokPegawai)
        },
        success: function(response) {
            const {data} = response
            let _arrData = [];
            $.each(data, function(k,v) {
                _arrData.push({
                    id: k,
                    text: v
                })
            })
            $('#persalinanform-penolong').select2({
                data: _arrData,
            }).val(_penolongId).trigger('change')
        },
        error: function(xhr) {

        }
    })
    if (typeof rujukKala !== undefined && rujukKala != null) {
        $("#rujuk_kala").prop("hidden", false);
        $(".field-persalinanform-alasan_merujuk").addClass("required");
        $(".field-persalinanform-tempat_rujukan").addClass("required");
        $(".field-persalinanform-pendamping").addClass("required");
        $(".field-persalinanform-masalah_persalinan").addClass("required");
    }
});

$("#form-keadaan-umum").on("submit", function(event) {
    $(this).docoForm("submit", {
        success : function(response) {
            $("#partograf-wizard-head-1").click();
        }
    }); 
});

$('input[type=radio][name="PersalinanForm\\[rujuk_kala\\]"]').change(function(event) {
    $("#rujuk_kala").prop("hidden", false);
    $(".field-persalinanform-alasan_merujuk").addClass("required");
    $(".field-persalinanform-tempat_rujukan").addClass("required");
    $(".field-persalinanform-pendamping").addClass("required");
    $(".field-persalinanform-masalah_persalinan").addClass("required");
});