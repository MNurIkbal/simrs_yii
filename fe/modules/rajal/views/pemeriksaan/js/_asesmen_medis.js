$(".input-tag").tagsinput()

$("[type='radio'],[type='checkbox']").uniform({
    radioClass: 'choice'
})

$(document).off('click', '.btn-add-diagnosa')
$(document).off('click', '.btn-remove-diagnosa')
$(document).on('click', '.btn-add-diagnosa', ({ currentTarget }) => {
    const elementBtn = $(currentTarget)
    elementBtn.removeClass('btn-success').addClass('btn-danger').removeClass('btn-add-diagnosa').addClass('btn-remove-diagnosa').find('i').removeClass('fa-plus').addClass('fa-trash')
    $("#table-diagnosa-fisik tbody").append(`
        <tr>
            <td>
                <input type="text" name="masalah_diagnosa_medis" class="form-control">
            </td>
            <td>
                <input type="text" name="rencana_laksana_medis" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-success btn-xs btn-action btn-add-diagnosa"><i class="fa fa-plus"></i></button>
            </td>
        </tr>
    `)
})

$("#riwayat_penyakit_keluarga-lainnya").on("click", function (e) {
    if ($("#riwayat_penyakit_keluarga-lainnya").is(':checked') == true) {
        $("#rpl").prop("disabled", false)
        $("#rpl").parent().find('.bootstrap-tagsinput input').prop("disabled", false)
    }
    else {
        $("#rpl").prop("disabled", true)
        $("#rpl").tagsinput("removeAll")
    }
})


$(document).on('click', '.btn-remove-diagnosa', ({ currentTarget }) => {
    if ($("#table-diagnosa-fisik tbody tr").length > 1) {
        const elementBtn = $(currentTarget)
        const elementRow = elementBtn.parents('tr')
        elementRow.remove()
        if ($("#table-diagnosa-fisik tbody tr").length == 1) {
            $("#table-diagnosa-fisik tbody tr button").removeClass('btn-danger').addClass('btn-success').removeClass('btn-remove-diagnosa').addClass('btn-add-diagnosa').find('i').removeClass('fa-trash').addClass('fa-plus')
        }
    }
})
$("#submit-fisik").on("click", function (e) {
    e.preventDefault()
    let serializeArray = $("#form-fisik").serializeArray()
    let payload = {
        daftarDiagnosa: []
    }
    let typeElement = ''
    let nodeElement = ''
    let payloadKey = ''
    serializeArray.map((item) => {
        typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
        nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
        if (typeElement == 'text' || nodeElement == 'textarea' || ((typeElement == 'checkbox' || typeElement == 'radio') && (item.value != '0' && item.value != '00'))) {
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

    $("#table-diagnosa-fisik tbody tr").each((index, elementRow) => {
        const valueDiagnosa = $(elementRow).find('input[name="masalah_diagnosa_medis"]').val()
        const valueRencana = $(elementRow).find('input[name="rencana_laksana_medis"]').val()
        if (valueDiagnosa != '' || valueRencana != '') {
            payload.daftarDiagnosa.push({
                masalah_diagnosa_medis: valueDiagnosa,
                rencana_laksana_medis: valueRencana
            })
        }
    })

    $.ajax({
        url: `/rajal/pemeriksaan/asesmen-medis?id=${id}`,
        method: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: () => {
            docoNotification('success', 'Proses berhasil!', 'Data asesmen keperawatan berhasil disimpan.')
            $('#tab-periksafisik').trigger('click')
        },
        complete: () => {
            hideLoader()
        }
    })

})

var keyAsesmenData = Object.keys(fisikData)
if (keyAsesmenData.length > 0) {
    keyAsesmenData.map((key) => {
        var dataField = fisikData[key]
        if (typeof dataField == 'boolean') {
            dataField = dataField ? '1' : '0'
        }
        if (dataField != null && dataField.constructor != Array) {
            var inputName = `AsesmenMedisForm[${key}]`
            var elementCheckbox = $(`input[name="${inputName}"][type="checkbox"]`)
            var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
            var elementInput = $(`[name="${inputName}"]`)
            if (elementCheckbox.length > 0) {
                var arrayCheckboxValue = []
                elementCheckbox.each((index, checkbox) => {
                    arrayCheckboxValue.push($(checkbox).val())
                })
                var otherElementCheckbox = $(`input[name="${inputName}"][type="text"]`)
                if (dataField.split(',').length == 1) {
                    $(`input[name="${inputName}"][type="checkbox"][value="${dataField}"]`).trigger('click')
                } else {
                    dataField.split(',').map((string) => {
                        if (arrayCheckboxValue.indexOf(string) >= 0) {
                            $(`input[name="${inputName}"][type="checkbox"][value="${string}"]`).trigger('click')
                        } else {
                            if (!$(`input[name="${inputName}"][type="checkbox"][value="lainnya"]`).is(':checked')) {
                                $(`input[name="${inputName}"][type="checkbox"][value="lainnya"]`).trigger('click')
                            }
                            if (key == 'riwayat_penyakit_keluarga') {
                                $("#rpl").tagsinput('add', string)
                            } else {
                                otherElementCheckbox.tagsinput('add', string)
                            }
                        }
                    })
                }
            } else if (elementRadio.length > 0) {
                var elementChecked = $(`input[name="${inputName}"][type="radio"][value="${dataField}"]`)
                if (elementChecked.length > 0) {
                    elementChecked.prop('disabled', false)
                    elementChecked.trigger('click')
                } else {
                    $(`input[name="${inputName}"][type="radio"][value="00"]`).prop('disabled', false)
                    $(`input[name="${inputName}"][type="radio"][value="00"]`).trigger('click')
                    dataField.split(',').map((string) => {
                        $(`input[name="${inputName}"][type="text"]`).tagsinput('add', string)
                    })
                }
            }

        } else if (key == 'daftarDiagnosa' && dataField.length > 0) {
            $("#table-diagnosa-fisik tbody").empty()
            var totalDiagnosa = dataField.length
            dataField.map((diagnosa, indexDiagnosa) => {
                $("#table-diagnosa-fisik tbody").append(`
                    <tr>
                        <td>
                            <input type="text" name="masalah_diagnosa_medis" value="${diagnosa.masalah_diagnosa_medis}" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="rencana_laksana_medis" value="${diagnosa.rencana_laksana_medis}" class="form-control">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-action ${indexDiagnosa + 1 >= totalDiagnosa ? 'btn-success btn-add-diagnosa' : 'btn-danger btn-remove-diagnosa'}"><i class="fa ${indexDiagnosa + 1 >= totalDiagnosa ? 'fa-plus' : 'fa-trash'}"></i></button>
                        </td>
                    </tr>
                `)
            })
        }
    })
    $.uniform.update()
    // $(".doco-number").trigger('change')
}

if ($("#riwayat_penyakit_keluarga-lainnya").is(':checked') == true) {
    $("#rpl").prop("disabled", false)
    $("#rpl").parent().find('.bootstrap-tagsinput input').prop("disabled", false)
} else {
    $("#rpl").prop("disabled", true)
    $("#rpl").tagsinput("removeAll")
}

var originFieldName = (rawFieldName) => {
    return rawFieldName.replace('AsesmenMedisForm[', '').replace(']', '')
}


