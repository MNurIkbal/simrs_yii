$(() => {
	$('.modal').find(".input-tag").tagsinput()
    $('.modal').find('.default-disabled').prop('disabled', true)
    $('.modal').find("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $('.modal').find("input[type='radio']").bind('change', ({ currentTarget }) => {
        usia              = isNaN(parseInt($(`input[name="HumptyDumptyForm[usia]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[usia]"]:checked`).val())
        jenis_kelamin     = isNaN(parseInt($(`input[name="HumptyDumptyForm[jenis_kelamin]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[jenis_kelamin]"]:checked`).val())
        diagnosis         = isNaN(parseInt($(`input[name="HumptyDumptyForm[diagnosis]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[diagnosis]"]:checked`).val())
        gangguan_kognitif = isNaN(parseInt($(`input[name="HumptyDumptyForm[gangguan_kognitif]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[gangguan_kognitif]"]:checked`).val())
        faktor_lingkungan = isNaN(parseInt($(`input[name="HumptyDumptyForm[faktor_lingkungan]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[faktor_lingkungan]"]:checked`).val())
        anastesi          = isNaN(parseInt($(`input[name="HumptyDumptyForm[anastesi]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[anastesi]"]:checked`).val())
        medika_mentosa    = isNaN(parseInt($(`input[name="HumptyDumptyForm[medika_mentosa]"]:checked`).val())) ? 0 : parseInt($(`input[name="HumptyDumptyForm[medika_mentosa]"]:checked`).val())

        let nilaiSkor = usia + jenis_kelamin + diagnosis + gangguan_kognitif + faktor_lingkungan + anastesi + medika_mentosa;
        $('#humptydumptyform-total_skor').val(nilaiSkor);
    });

	$("#btn-simpan").bind('click', () => {
        $('.modal').find("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-dumpty").serializeArray()
        let payload = {}
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || (typeElement == 'checkbox' && item.value != '00')) {
                if (typeof item.value != 'undefined') {
                    payloadKey = originFieldNameDumpty(item.name)
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

        if($('#humptydumptyform-total_skor').val() == ''){
            docoNotification('error', 'Proses Gagal!', 'Data Humpty Dumpty belum diisi.') 
        }
        else{
            showLoader()
            $.ajax({
                url: `save-dumpty?pendaftaran_id=${pendaftaranId}`,
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: () => {
                    docoNotification('success', 'Proses berhasil!', 'Data dumpty berhasil disimpan.')
                    $('.btn-resiko-jatuh-section .btn-group-section').removeClass('btn-group-section--active')
                    $('.btn-resiko-jatuh-section .btn-group-section[data-type="humpty-dumpty"]').addClass('btn-group-section--active')
                    $(`input[name='risiko_jatuh']`).val($('#humptydumptyform-total_skor').val())
                },
                complete: () => {
                    hideLoader()
                }
            })
        }
    })

	var keyDumptyData = Object.keys(dumptyData)
    if (keyDumptyData.length > 0) {
        keyDumptyData.map((key) => {
            var dataField = dumptyData[key]
            
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `HumptyDumptyForm[${key}]`
                var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
                var elementInput = $(`[name="${inputName}"]`)

                if (elementRadio.length > 0) {
                    var elementChecked = $(`input[name="${inputName}"][type="radio"][value="${dataField}"]`)
                    if (elementChecked.length > 0) {
                        elementChecked.prop('disabled', false)
                        elementChecked.trigger('click')
                    } 
                }
                
                $.uniform.update()
            }           
        })
    }

})

var originFieldNameDumpty = (rawFieldName) => {
    return rawFieldName.replace('HumptyDumptyForm[', '').replace(']', '')
}

