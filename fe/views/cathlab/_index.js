$(() => {
    $("#operator_id-form").select2InfinityScroll({
        url: urlEmployee
    })

    $("#submit-cathlab").bind('click', () => {

        $("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-cathlab").serializeArray()
        let payload = {
            cathlab_id: typeof cathlabData != 'undefined' && cathlabData.cathlab_id != 'undefined' ? cathlabData.cathlab_id : null,
            operator_id: $("#operator_id-form").val(),
            operator_nama: $("#operator_id-form").val() != null ? $("#operator_id-form").select2('data')[0].text : '',
        }
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || (typeElement == 'checkbox' && item.value != '00')) {
                if (typeof item.value != 'undefined') {
                    payloadKey = originFieldName(item.name)
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
        $("[type='hidden']").prop('disabled', false)

        showLoader()
        $.ajax({
            url: `${url}?pendaftaran_id=${pendaftaranId}&tipe=${tipe}${pasienAdmisiId != '' && pasienAdmisiId != null ? `&pasienadmisi_id=${pasienAdmisiId}` : ''}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: () => {
                docoNotification('success', 'Proses berhasil!', 'Data formulir cathlab berhasil disimpan.')
                // $('#tab-cathlab-koroangiografi').trigger('click')
            },
            complete: () => {
                hideLoader()
            }
        })

    })

    var keyCathlabData = Object.keys(cathlabData)
    if (keyCathlabData.length > 0) {
        keyCathlabData.map((key) => {
            var dataField = cathlabData[key]
            if (typeof dataField == 'boolean') {
                dataField = dataField ? '1' : '0'
            }
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `CathlabForm[${key}]`
                var elementInput = $(`[name="${inputName}"]`)
                if (key == 'operator_id') {
                    var newState = new Option(cathlabData['operator_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                } else if (elementInput.length > 0) {
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

})

var originFieldName = (rawFieldName) => {
    return rawFieldName.replace('CathlabForm[', '').replace(']', '')
}

