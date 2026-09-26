$(() => {
	$('.modal').find(".input-tag").tagsinput()
    $('.modal').find('.default-disabled').prop('disabled', true)
    $('.modal').find("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $("#resikojatuh_id-form").select2InfinityScroll({
        url: '/igd/pemeriksaan-igd/resiko-jatuh-list'
    })

    $('.modal').find("input[type='radio']").bind('change', ({ currentTarget }) => {
        riwayat_jatuh      = isNaN(parseInt($(`input[name="MorseForm[riwayat_jatuh]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[riwayat_jatuh]"]:checked`).val())
        diagnosis_sekunder = isNaN(parseInt($(`input[name="MorseForm[diagnosis_sekunder]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[diagnosis_sekunder]"]:checked`).val())
        alat_bantu         = isNaN(parseInt($(`input[name="MorseForm[alat_bantu]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[alat_bantu]"]:checked`).val())
        catheter           = isNaN(parseInt($(`input[name="MorseForm[catheter]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[catheter]"]:checked`).val())
        kemampuan_berjalan = isNaN(parseInt($(`input[name="MorseForm[kemampuan_berjalan]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[kemampuan_berjalan]"]:checked`).val())
        status_mental      = isNaN(parseInt($(`input[name="MorseForm[status_mental]"]:checked`).val())) ? 0 : parseInt($(`input[name="MorseForm[status_mental]"]:checked`).val())

        let nilaiSkor = riwayat_jatuh + diagnosis_sekunder + alat_bantu + catheter + kemampuan_berjalan + status_mental;
        $('#morseform-total_skor').val(nilaiSkor);
        var kesimpulan;
        if (nilaiSkor >= 0 && nilaiSkor < 25){
            kesimpulan = nilaiSkor +  " (Tidak ada risiko jatuh)";
        }else if(nilaiSkor >= 25 && nilaiSkor < 51){
            kesimpulan = nilaiSkor +  " (Risiko rendah jatuh)";
        }else if (nilaiSkor >= 51){
            kesimpulan = nilaiSkor + " (Risiko tinggi jatuh)";
        }else{
            kesimpulan ="";
        }
        $('#morseform-kesimpulan').val(kesimpulan);
    });

	$("#btn-simpan").bind('click', () => {
        $("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-morse").serializeArray()
        let payload = {
            resikojatuh_id: $("#resikojatuh_id-form").val(),
            resikojatuh_nama: $("#resikojatuh_id-form").val() != null ? $("#resikojatuh_id-form").select2('data')[0].text : '',
        }
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || (typeElement == 'checkbox' && item.value != '00')) {
                if (typeof item.value != 'undefined') {
                    payloadKey = originFieldNameMorse(item.name)
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
            url: `/igd/pemeriksaan-igd/save-morse?pendaftaran_id=${pendaftaranId}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: () => {
                docoNotification('success', 'Proses berhasil!', 'Data morse berhasil disimpan.')
                $('.btn-resiko-jatuh-section .btn-group-section').removeClass('btn-group-section--active')
                $('.btn-resiko-jatuh-section .btn-group-section[data-type="morse"]').addClass('btn-group-section--active')
                $(`input[name='risiko_jatuh']`).val($('#morseform-kesimpulan').val())
            },
            complete: () => {
                hideLoader()
            }
        })
    })

	var keyMorseData = Object.keys(morseData)
    if (keyMorseData.length > 0) {
        keyMorseData.map((key) => {
            var dataField = morseData[key]
            
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `MorseForm[${key}]`
                var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
                var elementInput = $(`[name="${inputName}"]`)

                if (elementRadio.length > 0) {
                    var elementChecked = $(`input[name="${inputName}"][type="radio"][value="${dataField}"]`)
                    if (elementChecked.length > 0) {
                        elementChecked.prop('disabled', false)
                        elementChecked.trigger('click')
                    } 
                }else if (key == 'resikojatuh_id') {
                    var newState = new Option(key == 'resikojatuh_id' ? morseData['resikojatuh_nama'] : morseData['resikojatuh_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                }else if (elementInput.length > 0) {
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
    }
    else{
        var elementChecked = $('.modal').find(`input[type="radio"][value="0"]`)
        elementChecked.prop('disabled', false)
        elementChecked.trigger('click')
        $.uniform.update()
    }

})

var originFieldNameMorse = (rawFieldName) => {
    return rawFieldName.replace('MorseForm[', '').replace(']', '')
}

