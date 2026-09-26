$(() => {
	$('.modal').find(".input-tag").tagsinput()
    $('.modal').find('.default-disabled').prop('disabled', true)
    $('.modal').find("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $('.modal').find("input[type='radio']").bind('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const inputName = element.prop('name')
        var str = inputName; 
        var res = str.replace("is", "skor");
        var skor = 0;
        var tr = $(currentTarget).parents("tr");
        var className = tr.attr("class");
        if(tr.hasClass("scoreone")) {
            res = tr.data('scoreinput');
            res = `SydneyForm[${res}]`;
        } else if (className !== "") {
            disabledTab(className);
        }


        if ($(`input[name='${inputName}']:checked`).val() == "0-tidak") {
            if(tr.hasClass("scoreone")) {
                var scoreinput = tr.data('scoreinput');
                skor = 0;
                $(`[data-scoreinput=${scoreinput}] input:checked`).each(function(idx, elem){
                    if($(elem).val().indexOf("-") > 0) {
                        skor = 0;
                    } else {
                        skor = $(elem).val();
                        return false;
                    }
                });
            } else {
                skor = 0;
            }
        }
        else if ($(`input[name='${inputName}']:checked`).val() == "0-ya") {
            if(tr.hasClass("scoreone")) {
                var scoreinput = tr.data('scoreinput');
                skor = 0;
                $(`[data-scoreinput=${scoreinput}] input:checked`).each(function(idx, elem){
                    if($(elem).val().indexOf("-") > 0) {
                        skor = 0;
                    } else {
                        skor = $(elem).val();
                        return false;
                    }
                });
            } else {
                skor = 0;
            }

        }
        else{
            skor = $(`input[name='${inputName}']:checked`).val()
        }
        $('.modal').find(`input[name='${res}']`).val(skor)
        hitungSkor()
    });

	$("#btn-simpan").bind('click', () => {
        $('.modal').find("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-sydney").serializeArray()
        let payload = {}
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || (typeElement == 'checkbox' && item.value != '00')) {
                if (typeof item.value != 'undefined') {
                    payloadKey = originFieldNameSydney(item.name)
                    if (typeof payload[payloadKey] != 'undefined') {
                        payload[payloadKey] += `${payload[payloadKey] != '' ? ',' : ''}${item.value}`
                    } else {
                        payload[payloadKey] = item.value
                    }
                } else {
                    payload[payloadKey] = ''
                }
            }
        })
        $('.modal').find("[type='hidden']").prop('disabled', false)

        showLoader()
        $.ajax({
            url: `/igd/pemeriksaan-igd/save-sydney?pendaftaran_id=${pendaftaranId}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: () => {
                docoNotification('success', 'Proses berhasil!', 'Data sydney berhasil disimpan.')
                $('.btn-resiko-jatuh-section .btn-group-section').removeClass('btn-group-section--active')
                $('.btn-resiko-jatuh-section .btn-group-section[data-type="sydney"]').addClass('btn-group-section--active')
                $(`input[name='risiko_jatuh']`).val($('#sydneyform-total_skor').val())
            },
            complete: () => {
                hideLoader()
            }
        })
        
    })

	var keySydneyData = Object.keys(sydneyData)
    if (keySydneyData.length > 0) {
        keySydneyData.map((key) => {
            var dataField = sydneyData[key]
            if (typeof dataField == 'boolean') {
                dataField = dataField ? '1' : '0'
            }
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `SydneyForm[${key}]`
                var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
                var elementInput = $(`[name="${inputName}"]`)

                if (elementRadio.length > 0) {
                    var elementChecked = $(`input[name="${inputName}"][type="radio"][value="${dataField}"]`)
                    if (elementChecked.length > 0) {
                        elementChecked.prop('disabled', false)
                        elementChecked.trigger('click')
                    } 
                }
                else if (elementInput.length > 0) {
                    if (elementInput.prop('tagName').toLowerCase() == 'textarea') {
                        elementInput.text(dataField)
                    } else if (elementInput.parent().find('.bootstrap-tagsinput').length > 0) {
                        dataField.split(',').map((string) => {
                            elementInput.tagsinput('add', string)
                        })
                    } else {
                        elementInput.val(dataField)
                    }
                }
                $.uniform.update()
            }           
        })
        $(".doco-number").trigger('change')
    }
    else{
    	var elementChecked = $('.modal').find(`input[type="radio"][value="0-tidak"]`)
		elementChecked.prop('disabled', false)
		elementChecked.trigger('click')
        $.uniform.update()
    }

    disabledTab("_transfer");
    disabledTab("_mobilitas");
    function disabledTab(className) {
        var checkHasYa = false;
        var elemTrue = null;
        $("."+className+" input:checked").each(function(index, element){ 
            if (element.value != "0-tidak") {
                checkHasYa = true;
                elemTrue = element;
            } 
        });
        if (checkHasYa) {
            $("."+className+" input").attr("disabled", true);
            $(elemTrue).parents("."+className).find("input").removeAttr("disabled");
        } else {
            $("."+className+" input").removeAttr("disabled");
        }
    }
})

var originFieldNameSydney = (rawFieldName) => {
    return rawFieldName.replace('SydneyForm[', '').replace(']', '')
}

var hitungSkor = function () {
	skor_karena_jatuh       = isNaN(parseInt($('#sydneyform-skor_karena_jatuh').val())) ? 0 : parseInt($('#sydneyform-skor_karena_jatuh').val())
	skor_dua_bulan_terakhir = isNaN(parseInt($('#sydneyform-skor_dua_bulan_terakhir').val())) ? 0 : parseInt($('#sydneyform-skor_dua_bulan_terakhir').val())

	skor_delirium           = isNaN(parseInt($('#sydneyform-skor_delirium').val())) ? 0 : parseInt($('#sydneyform-skor_delirium').val())
	skor_disorientasi       = isNaN(parseInt($('#sydneyform-skor_disorientasi').val())) ? 0 : parseInt($('#sydneyform-skor_disorientasi').val())
	skor_agitasi            = isNaN(parseInt($('#sydneyform-skor_agitasi').val())) ? 0 : parseInt($('#sydneyform-skor_agitasi').val())

	skor_kacamata           = isNaN(parseInt($('#sydneyform-skor_kacamata').val())) ? 0 : parseInt($('#sydneyform-skor_kacamata').val())
	skor_buram              = isNaN(parseInt($('#sydneyform-skor_buram').val())) ? 0 : parseInt($('#sydneyform-skor_buram').val())
	skor_glaucoma           = isNaN(parseInt($('#sydneyform-skor_glaucoma').val())) ? 0 : parseInt($('#sydneyform-skor_glaucoma').val())

	skor_berkemih           = isNaN(parseInt($('#sydneyform-skor_berkemih').val())) ? 0 : parseInt($('#sydneyform-skor_berkemih').val())

	skor_mandiri            = isNaN(parseInt($('#sydneyform-skor_mandiri').val())) ? 0 : parseInt($('#sydneyform-skor_mandiri').val())
	skor_bantuan_sedikit    = isNaN(parseInt($('#sydneyform-skor_bantuan_sedikit').val())) ? 0 : parseInt($('#sydneyform-skor_bantuan_sedikit').val())
	skor_bantuan_nyata      = isNaN(parseInt($('#sydneyform-skor_bantuan_nyata').val())) ? 0 : parseInt($('#sydneyform-skor_bantuan_nyata').val())
	skor_bantuan_total      = isNaN(parseInt($('#sydneyform-skor_bantuan_total').val())) ? 0 : parseInt($('#sydneyform-skor_bantuan_total').val())
	skor_mobilitas_mandiri  = isNaN(parseInt($('#sydneyform-skor_mobilitas_mandiri').val())) ? 0 : parseInt($('#sydneyform-skor_mobilitas_mandiri').val())
	skor_mobilitas_bantuan  = isNaN(parseInt($('#sydneyform-skor_mobilitas_bantuan').val())) ? 0 : parseInt($('#sydneyform-skor_mobilitas_bantuan').val())
	skor_kursi_roda         = isNaN(parseInt($('#sydneyform-skor_kursi_roda').val())) ? 0 : parseInt($('#sydneyform-skor_kursi_roda').val())
	skor_imobilisasi        = isNaN(parseInt($('#sydneyform-skor_imobilisasi').val())) ? 0 : parseInt($('#sydneyform-skor_imobilisasi').val())
    
    // let nilaiSkor = skor_karena_jatuh + skor_dua_bulan_terakhir + skor_delirium + skor_kacamata + skor_buram + skor_glaucoma + skor_berkemih + skor_mandiri + skor_bantuan_sedikit + skor_bantuan_nyata + skor_bantuan_total + skor_mobilitas_mandiri + skor_mobilitas_bantuan + skor_kursi_roda + skor_imobilisasi;
    // $('#sydneyform-total_skor').val(nilaiSkor);

    riwayat_jatuh = (skor_karena_jatuh > 0 || skor_dua_bulan_terakhir > 0) ? 6 : 0;
    status_mental = (skor_delirium > 0 || skor_disorientasi > 0 || skor_agitasi > 0) ? 14 : 0;
    penglihatan   = (skor_kacamata > 0 || skor_buram > 0 || skor_glaucoma > 0) ? 1 : 0;
    Transfer      = skor_mandiri + skor_bantuan_sedikit + skor_bantuan_nyata + skor_bantuan_total;
    Mobilitas     = skor_mobilitas_mandiri + skor_mobilitas_bantuan + skor_kursi_roda + skor_imobilisasi;
    trans_mobilitas = (Transfer + Mobilitas) < 4 ? 0 : 7;
    let nilaiSkor = riwayat_jatuh + status_mental + penglihatan + skor_berkemih + trans_mobilitas;
    $('#sydneyform-total_skor').val(nilaiSkor);
}