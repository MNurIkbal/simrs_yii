$(document).ready(function(){

    if (!is_nurse) {
        $("#pemberian-infus-form :input").prop("disabled", true);
      }
    $("#pemberianinfusmodel-instruksitindakan_id").select2()
    $("#pemberianinfusmodel-instruksitindakanbmhp_id").select2({
        placeholder: " -- Pilih Obat -- ",
        multiple: true
    })
    $("#pemberianinfusmodel-instruksitindakanbmhp_id").val(null).trigger('change')

    $("#pemberianinfusmodel-instruksitindakan_id").on('change', function() {
        let selecteds = $("#pemberianinfusmodel-instruksitindakan_id").select2('data')
        let selected = selecteds[0]
        data = selected.data
    })
    $("#pemberianinfusmodel-instruksitindakanbmhp_id").on('change', function() {
        $('#instruksitindakanbmhp_id').val(' ').trigger('change')
    })

    $("#pemberianinfusmodel-volume").on('change', function() {
        updateJumlahTetesan()
    })

    $("#pemberianinfusmodel-durasi").on('change', function() {
        updateJumlahTetesan()
    })

    function updateJumlahTetesan() {
        if($("#pemberianinfusmodel-volume").val() && $("#pemberianinfusmodel-durasi").val()) {
            let volume = $("#pemberianinfusmodel-volume").val().replaceAll(",", ".")
            let durasi = $("#pemberianinfusmodel-durasi").val().replaceAll(",", ".")
            let jumlah_tetesan = volume / durasi
            jumlah_tetesan = jumlah_tetesan.toFixed(2)
            $("#pemberianinfusmodel-jumlah_tetesan").val(jumlah_tetesan.toString().replaceAll(".", ","))
        }
    }

    $(() => {
        $("#btn-save-diet-pasien").bind("click", () => {
            let _form = $("#diet-pasien-form").serializeArray();
            $().docoForm("click", {
                data: _form,
                skipScrollUp: false,
                url: $("#diet-pasien-form").attr("action"),
                success: function (data) {
                    $("#modal-lab").find(".close").click();
                },
            });
        });
    });


    $('#save-pemberian-infus').on('click', function(e) {
        e.preventDefault()
        let dataArray = $('#pemberian-infus-form').serializeArray()
        let payload = [];
        for(let data of dataArray) {
            if(data.name.endsWith("[durasi]") || data.name.endsWith("[volume]") || data.name.endsWith("[jumlah_tetesan]")) {
                data.value = data.value.replaceAll(",", ".")
            }
            payload.push(data)
        }

        $().docoForm("click", {
            skipScrollUp: false,
            url: $('#pemberian-infus-form').attr('action'),
            data: payload,
            success: function (data) {
                reloadForm()
            },
        });
    })
})