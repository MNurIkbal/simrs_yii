var table = $("#example").DataTable();
$(document).ready(function () {
    hidePlafonRuangan()
    if (id) {
        $("#plafonbpjsform-plafon").trigger("change");
        $("#plafonbpjsform-plafon_ruangan").trigger("change");
        if(isRuangan) {
            showPlafonRuangan()
        }
    }
    $(".selectInstalasi").select2InfinityScroll({
        url: "/master/plafon-bpjs/filters?type=instalasi",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })

    $(".selectKelas").select2InfinityScroll({
        url: "/master/plafon-bpjs/filters?type=kelas",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    
    var dataInstalasi = {
        id: instalasiId,
        text: instalasi
    };
    var dataKelas = {
        id: kelasPelayananId,
        text: kelasPelayanan
    };
    var optionInstalasi = new Option(dataInstalasi.text, dataInstalasi.id, true, true);
    var optionKelas = new Option(dataKelas.text, dataKelas.id, true, true);

    $('.selectInstalasi').append(optionInstalasi).trigger('change');
    $('.selectKelas').append(optionKelas).trigger('change');
    
    if(listRuangan && listRuanganId) {
        let dataRuangan = {
            list_ruangan: listRuangan,
            list_ruangan_id: listRuanganId
        };
        let ids = dataRuangan.list_ruangan_id.split(',').map(s => s.trim());
        let names = dataRuangan.list_ruangan.split(',').map(s => s.trim());
        ids.forEach((id, i) => {
            if ($('#ruangan_id').find("option[value='" + id + "']").length === 0) {
                let newOption = new Option(names[i], id, true, true);
                $('#ruangan_id').append(newOption);
            }
        });
    }
    $('#ruangan_id').prop('disabled', false).trigger('change')
    $("#plafonbpjsform-is_ruangan").on("change", function(){
        const labelPlafonRuangan = $("label[for='plafonbpjsform-plafon_ruangan']");
        const labelRuangan = $("label[for='ruangan_id']");
        let instalasiId = $(".selectInstalasi").val()
        let kelasPelayananId = $(".selectKelas").val()

        if ($(this).is(':checked')) {
            showPlafonRuangan()
            if (labelPlafonRuangan.find('.required-mark').length === 0) {
                labelPlafonRuangan.append(' <span class="required-mark" style="color:red;">*</span>');
            }
            if (labelRuangan.find('.required-mark').length === 0) {
                labelRuangan.append(' <span class="required-mark" style="color:red;">*</span>');
            }

            if(instalasiId && kelasPelayananId) {
                cekDefaultPlafon(instalasiId, kelasPelayananId);
            }
        }
        else {
            let plafon = $("#plafonbpjsform-plafon")
            let plafonRuangan = $("#plafonbpjsform-plafon_ruangan")

            hidePlafonRuangan()
            labelPlafonRuangan.find('.required-mark').remove();
            labelRuangan.find('.required-mark').remove();
            $('#ruangan_id').val([]).trigger("change")
            
            plafon.prop("readonly", false).removeClass("nullable").val(null).trigger("change")
            plafonRuangan.addClass("nullable").val(null).trigger("change")
        }
    })

    $(".selectKelas").on("change", function(){
        let kelasPelayananId = $(this).val()
        let instalasiId = $(".selectInstalasi").val()
        if ($("#plafonbpjsform-is_ruangan").is(":checked") && instalasiId && kelasPelayananId) {
            cekDefaultPlafon(instalasiId, kelasPelayananId);
        }
    })
    $(".selectInstalasi").on("change", function(){
        let kelasPelayananId = $(".selectKelas").val()
        let instalasiId = $(this).val()
        if ($("#plafonbpjsform-is_ruangan").is(":checked") && instalasiId && kelasPelayananId) {
            cekDefaultPlafon(instalasiId, kelasPelayananId);
        }
    })
});

$(".btn-save").on("click", function (event) {
    event.preventDefault();
    var data = $("#plafon-bpjs-form").serializeArray();
    var _url = "/master/plafon-bpjs/create";
    $(this).docoForm("click", {
        url: _url,
        data: data,
        method: "post",
        success: function (data) {
            $("#modal_backdrop").modal("toggle");
            setTimeout(function () {
                table.draw();
            }, 1000);
        },
    });
});

function hidePlafonRuangan()
{
    $(".field-ruangan_id").css("display", "none")
    $(".field-plafonbpjsform-plafon_ruangan").css("display", "none")
}

function showPlafonRuangan()
{
    $(".field-ruangan_id").css("display", "block")
    $(".field-plafonbpjsform-plafon_ruangan").css("display", "block")
}

function cekDefaultPlafon(instalasiId, kelasPelayananId)
{
    $.ajax({
        url: "/master/plafon-bpjs/cek-default-plafon",
        method: "GET",
        data: {
            instalasi_id: instalasiId,
            kelaspelayanan_id: kelasPelayananId
        },
        success: function (data) {
            if (data && data != 0) {
                $("#plafonbpjsform-plafon_ruangan").removeClass("nullable");

                const $input = $("#plafonbpjsform-plafon");
                $input.prop("readonly", true);
                $input.removeClass('nullable');
                $input.val(data).trigger("change");
                $input.addClass('nullable');
            }
        },
        error: function (xhr) {
            console.log("Gagal mengambil data plafon:", xhr.responseText);
        }
    });
}
