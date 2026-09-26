$(() => {
	$(".input-tag").tagsinput()
    $('.default-disabled').prop('disabled', true)
    $("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $("#dokter_id-form").select2InfinityScroll({
        url: '/igd/pemeriksaan-igd/dokter-list'
    })
    $("#perawat_id-form").select2InfinityScroll({
        url: '/igd/pemeriksaan-igd/perawat-list'
    })

    $("input[type='checkbox'],input[type='radio']").bind('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const inputName = element.prop('name')
        const otherElement = $(`input[name='${inputName}'][value='00']`)
        if (otherElement.length > 0) {
            $(`#other-${otherElement.data('fieldname')}`).prop('disabled', !otherElement.is(':checked'))
            if (!otherElement.is(':checked')) {
                $(`#other-${otherElement.data('fieldname')}`).val('')
                if ($(`#other-${otherElement.data('fieldname')}`).parent().find('.bootstrap-tagsinput').length > 0) {
                    $(`#other-${otherElement.data('fieldname')}`).tagsinput("removeAll")
                }
            } else {
                if ($(`#other-${otherElement.data('fieldname')}`).parent().find('.bootstrap-tagsinput').length > 0) {
                    $(`#other-${otherElement.data('fieldname')}`).parent().find('.bootstrap-tagsinput input').prop('disabled', false)
                }
            }
        }
    })
    $("[data-dependent]").bind('change', ({ currentTarget }) => {
        dependentHandler(currentTarget, ({ otherElement, elementStringChild }) => {
            $(elementStringChild).prop('disabled', !otherElement.is(':checked'))
            if (!otherElement.is(':checked')) {
                if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
                    $(`${elementStringChild}[type="text"]`).tagsinput("removeAll")
                }
                $(`${elementStringChild}[type="text"]`).val('')
                $(`${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`).prop('checked', false)
                $(`${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`).trigger('change')
            } else {
                if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
                    $(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput input').prop('disabled', false)
                }
            }
            $.uniform.update()
        })
    })

    $(".is_alergi-check").bind('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const dataElement = element.data()
        $(`input[name='TriaseForm[alergi_${dataElement.type}]']`).prop('disabled', !element.is(':checked')).val('')
    })

	$("#submit-triase").bind('click', () => {
        let hasil = ''
        if($(".jalan_nafas_resusitasi").is(':checked') == true || $(".pernafasan_resusitasi").is(':checked') == true || $(".sirkulasi_resusitasi").is(':checked') == true || $(".disability_resusitasi").is(':checked') == true){
            hasil = 'resusitasi';
        }
        else if($(".jalan_nafas_emergent").is(':checked') == true || $(".pernafasan_emergent").is(':checked') == true || $(".sirkulasi_emergent").is(':checked') == true || $(".disability_emergent").is(':checked') == true){
            hasil = 'emergent';
        }
        else if($(".jalan_nafas_urgent").is(':checked') == true || $(".pernafasan_urgent").is(':checked') == true || $(".sirkulasi_urgent").is(':checked') == true || $(".disability_urgent").is(':checked') == true){
            hasil = 'urgent';
        }
        else if($(".jalan_nafas_less_urgent").is(':checked') == true || $(".pernafasan_less_urgent").is(':checked') == true || $(".sirkulasi_less_urgent").is(':checked') == true || $(".disability_less_urgent").is(':checked') == true){
            hasil = 'less_urgent';
        }
        else if($(".jalan_nafas_non_urgent").is(':checked') == true || $(".pernafasan_non_urgent").is(':checked') == true || $(".sirkulasi_non_urgent").is(':checked') == true || $(".disability_non_urgent").is(':checked') == true){
            hasil = 'non_urgent';
        }
        else if($(".jalan_nafas_false_emergency").is(':checked') == true || $(".pernafasan_false_emergency").is(':checked') == true || $(".sirkulasi_false_emergency").is(':checked') == true || $(".disability_false_emergency").is(':checked') == true){
            hasil = 'false_emergency';
        }

        $("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-triase").serializeArray()
        let payload = {
            triase_id: typeof triaseData != 'undefined' && triaseData.triase_id != 'undefined' ? triaseData.triase_id : null,
            old_triase_id: typeof triaseData != 'undefined' && triaseData.old_triase_id != 'undefined' ? triaseData.old_triase_id : null,
            dokter_id: $("#dokter_id-form").val(),
            dokter_nama: $("#dokter_id-form").val() != null ? $("#dokter_id-form").select2('data')[0].text : '',
            perawat_id: $("#perawat_id-form").val(),
            perawat_nama: $("#perawat_id-form").val() != null ? $("#perawat_id-form").select2('data')[0].text : '',
            gcseye_id: $("#gcseye_id-form").val(),
            gcsverbal_id: $("#gcsverbal_id-form").val(),
            gcsmotorik_id: $("#gcsmotorik_id-form").val(),
            is_kapitis: $("#is_kapitis-form").val(),
            hasil_triase: hasil,
            kamartempattidur_id: $('#triaseform-kamartempattidur_id').val(),
            old_kamartempattidur_id: $('#triaseform-old_kamartempattidur_id').val()
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
            url: `/igd/pemeriksaan-igd/save-triase?pendaftaran_id=${pendaftaranId}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: () => {
                docoNotification('success', 'Proses berhasil!', 'Data formulir triase berhasil disimpan.')
                $('#tab-formulir-triase').trigger('click')
            },
            complete: () => {
                hideLoader()
            }
        })
        
    })

	$("#cetak-triase").bind('click', () => {
        let hasil = ''
        if($(".resusitasi-group").is(':checked') == true || $(".jalan_nafas_resusitasi").is(':checked') == true || $(".pernafasan_resusitasi").is(':checked') == true || $(".sirkulasi_resusitasi").is(':checked') == true || $(".disability_resusitasi ").is(':checked') == true){
            hasil = 'resusitasi';
        }
        else if($(".emergent-group").is(':checked') == true || $(".jalan_nafas_emergent").is(':checked') == true || $(".pernafasan_emergent").is(':checked') == true || $(".sirkulasi_emergent").is(':checked') == true || $(".disability_emergent").is(':checked') == true){
            hasil = 'emergent';
        }
        else if($(".urgent-group").is(':checked') == true || $(".jalan_nafas_urgent").is(':checked') == true || $(".pernafasan_urgent").is(':checked') == true  || $(".sirkulasi_urgent").is(':checked') == true || $(".disability_urgent").is(':checked') == true){
            hasil = 'urgent';
        }
        else if($(".less_urgent-group").is(':checked') == true || $(".jalan_nafas_less_urgent").is(':checked') == true || $(".pernafasan_less_urgent").is(':checked') == true || $(".sirkulasi_less_urgent").is(':checked') == true || $(".disability_less_urgent").is(':checked') == true){
            hasil = 'less_urgent';
        }
        else if($(".non_urgent-group").is(':checked') == true || $(".jalan_nafas_non_urgent").is(':checked') == true || $(".pernafasan_non_urgent").is(':checked') == true || $(".sirkulasi_non_urgent").is(':checked') == true  || $(".disability_non_urgent").is(':checked') == true){
            hasil = 'non_urgent';
        }
        else if($(".jalan_nafas_false_emergency").is(':checked') == true || $(".pernafasan_false_emergency").is(':checked') == true || $(".sirkulasi_false_emergency").is(':checked') == true  || $(".disability_false_emergency").is(':checked') == true){
            hasil = 'false_emergency';
        }

        $("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-triase").serializeArray()
        let payload = {
            triase_id: typeof triaseData != 'undefined' && triaseData.triase_id != 'undefined' ? triaseData.triase_id : null,
            old_triase_id: typeof triaseData != 'undefined' && triaseData.old_triase_id != 'undefined' ? triaseData.old_triase_id : null,
            dokter_id: $("#dokter_id-form").val(),
            dokter_nama: $("#dokter_id-form").val() != null ? $("#dokter_id-form").select2('data')[0].text : '',
            perawat_id: $("#perawat_id-form").val(),
            perawat_nama: $("#perawat_id-form").val() != null ? $("#perawat_id-form").select2('data')[0].text : '',
            gcseye_id: $("#gcseye_id-form").val(),
            gcsverbal_id: $("#gcsverbal_id-form").val(),
            gcsmotorik_id: $("#gcsmotorik_id-form").val(),
            is_kapitis: $("#is_kapitis-form").val(),
            hasil_triase: hasil,
            kamartempattidur_id: $('#triaseform-kamartempattidur_id').val(),
            old_kamartempattidur_id: $('#triaseform-old_kamartempattidur_id').val(),
            nomor: typeof pendaftaranId != 'undefined' ? pendaftaranId : null,
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
        $("[type='hidden']").prop('disabled', false);

        var d = new Date(),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
    
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
    
        var created_date = [year, month, day].join('-');
        
        showLoader();
        var req = new XMLHttpRequest();
        req.open("POST", `/igd/pemeriksaan-igd/cetak-formulir-triase`, true);
        
        req.responseType = "blob";
        
        req.setRequestHeader("Content-type", "application/json");
        req.send(JSON.stringify(payload));
                
        req.onload = function (event) {
           var blob = req.response;
           console.log(blob.size);
           var link=document.createElement('a');
           link.href=window.URL.createObjectURL(blob);
           link.download="formulir-cetak-triase-" + created_date + ".pdf";
           link.click();
           hideLoader();
        };
        
    })

	var keyTriaseData = Object.keys(triaseData)
    if (keyTriaseData.length > 0) {
        keyTriaseData.map((key) => {
            var dataField = triaseData[key]
            if (typeof dataField == 'boolean') {
                dataField = dataField ? '1' : '0'
            }
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `TriaseForm[${key}]`
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
                                $(`input[name="${inputName}"][type="checkbox"][value="00"]`).trigger('click')
                                otherElementCheckbox.tagsinput('add', string)
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
                }else if (key == 'dokter_id' || key == 'perawat_id') {
                    var newState = new Option(key == 'dokter_id' ? triaseData['dokter_nama'] : triaseData['perawat_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                }else if (elementInput.length > 0) {
                    if (elementInput.prop('tagName').toLowerCase() == 'textarea') {
                        elementInput.text(dataField)
                    } else if (elementInput.parent().find('.bootstrap-tagsinput').length > 0) {
                        if(typeof dataField === 'string') {
                            dataField.split(',').map((string) => {
                                elementInput.tagsinput('add', string)
                            })                            
                        }
                    } else {
                        elementInput.val(dataField)
                    }
                }
                if (key.match(/alergi_obat|alergi_lainnya/g) != null && dataField != null && dataField != '') {
                    $(`[name='${key}_check']`).trigger('click')
                }

                $.uniform.update()
            } 
            $('#triaseform-alergi_obat').val(triaseData['alergi_obat'])
			$('#triaseform-alergi_lainnya').val(triaseData['alergi_lainnya'])
        })
        $(".doco-number").trigger('change')
    }

    updateRenderResultLabelTriase();
})

var checkedFieldValue = (fieldName) => {
    return typeof $(`input[name="TriaseForm[${fieldName}]"]:checked`).val() != 'undefined' ? $(`input[name="TriaseForm[${fieldName}]"]:checked`).val() : ''
}
var fieldValue = (fieldName) => {
    return $(`input[name="TriaseForm[${fieldName}]"]`).val()
}

var dependentHandler = (currentTarget, callback) => {
    const element = $(currentTarget)
    const inputName = element.prop('name')
    let dataDependent = {}
    try {
        dataDependent = element.data('dependent')
    } catch (error) {
        dataDependent = {}
    }
    if (typeof dataDependent.id != 'undefined' || typeof dataDependent.class != 'undefined') {
        let elementStringChild = typeof dataDependent.id != 'undefined' && dataDependent.id != '' ? `#${dataDependent.id}` : ''
        if (typeof dataDependent.class != 'undefined' && dataDependent.class != '') {
            elementStringChild = `${elementStringChild != '' ? `${elementStringChild},` : ''}.${dataDependent.class}`
        }
        dataDependent.id = typeof dataDependent.id != 'undefined' ? dataDependent.id : ''
        dataDependent.class = typeof dataDependent.class != 'undefined' ? dataDependent.class : ''
        dataDependent.onValue = typeof dataDependent.onValue != 'undefined' ? dataDependent.onValue : '1'
        if (elementStringChild != '') {
            let activeOn = dataDependent.onValue
            const otherElement = $(`input[name='${inputName}'][value='${activeOn}']`)
            callback({
                inputName,
                activeOn,
                otherElement,
                elementStringChild,
                element,
                dataDependent,
                trueValue: otherElement.prop(':checked')
            })
        }
    }
}

var originFieldName = (rawFieldName) => {
    return rawFieldName.replace('TriaseForm[', '').replace(']', '')
}

$("#cb-obat-alergi").on("click", function(e){
    if($("#cb-obat-alergi").is(':checked') == true){
        $("#triaseform-alergi_obat").prop("disabled",false)
        $("#triaseform-alergi_obat").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
    }
    else
    {
        $("#triaseform-alergi_obat").prop("disabled",true)
        $("#triaseform-alergi_obat").tagsinput("removeAll")
    }
})
if($("#cb-obat-alergi").is(':checked') == true){
    $("#triaseform-alergi_obat").prop("disabled",false)
    $("#triaseform-alergi_obat").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
}
else
{
    $("#triaseform-alergi_obat").prop("disabled",true)
    $("#triaseform-alergi_obat").tagsinput("removeAll")
}

$("#cb-lainnya-alergi").on("click", function(e){
    if($("#cb-lainnya-alergi").is(':checked') == true){
        $("#triaseform-alergi_lainnya").prop("disabled",false)
        $("#triaseform-alergi_lainnya").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
    }
    else
    {
        $("#triaseform-alergi_lainnya").prop("disabled",true)
        $("#triaseform-alergi_lainnya").tagsinput("removeAll")
    }
})
if($("#cb-lainnya-alergi").is(':checked') == true){
    $("#triaseform-alergi_lainnya").prop("disabled",false)
    $("#triaseform-alergi_lainnya").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
}
else
{
    $("#triaseform-alergi_lainnya").prop("disabled",true)
    $("#triaseform-alergi_lainnya").tagsinput("removeAll")
}

$(".btn-triage-option").bind("click", ({ currentTarget }) => {
    let triageRules = typeof configRules[$(currentTarget).parents('td').data('triage_group')][$(currentTarget).val()] != 'undefined' ? configRules[$(currentTarget).parents('td').data('triage_group')][$(currentTarget).val()] : null;
    if ( triageRules != null ) {
        $.each( triageRules, (key, rules) => {
            if (key == 'remove-all-except') {
                if ( rules == true) {
                    $(`td[data-triage_group='${$(currentTarget).parents('td').data('triage_group')}'] input[value!='${$(currentTarget).val()}']`)
                        .prop('checked',false);
                } else {
                    let _elementException = `td[data-triage_group='pernapasan_extra'] `
                    let _arrElemException = [`input[type='checkbox'][value!='${$(currentTarget).val()}']`]
                    $.each( rules, (index, triageItem) => {
                        _arrElemException.push(`input[type='checkbox'][value!='${triageItem}']`)
                    }) 
                    _elementException = _elementException + _arrElemException.join(``)
                    $(_elementException).prop('checked',false);
                }
            } else if (key == 'allow-select-same-line') {
                //just for make the options didnt catch by else triageRules condition
            } else if (key == 'remove-not-sibling') {
                $(currentTarget).parents('tr').find(`input[type='checkbox']`)
                    .not(currentTarget)
                    .prop('checked',false);
            }else {
                $.each(rules, (index, triageItem) => {
                    if (key == 'add') {
                        $(`td[data-triage_group] input[value='${triageItem}']`).prop('checked',true);
                    } else if (key == 'remove') {
                        $(`td[data-triage_group] input[value='${triageItem}']`).prop('checked',false);
                    }
                })
            }
        })
        $.uniform.update()
    }    

    let kategoriTriase = ''
    kategoriColor = ''
    configWaktuRespon = []
    Object.keys(configData.waktu_respon).forEach((eachKey) => {
        configWaktuRespon.push(eachKey)
    })
    updateRenderResultLabelTriase();
});

$(".btn-bed-option").bind("click", ({ currentTarget }) => {
    $(currentTarget).addClass("btn-info --selected");
    $(currentTarget)
        .parent()
        .find("button")
        .not(currentTarget)
        .removeClass("btn-info --selected");
    $("#triaseform-kamartempattidur_id").val($(currentTarget).data("bed-id"));
});

function updateRenderResultLabelTriase() {
    let kategoriTriase = ''
    let kategoriColor = ''
    configWaktuRespon = []
    Object.keys(configData.waktu_respon).forEach((eachKey) => {
        configWaktuRespon.push(eachKey)
    })
    const tempTriase = getKategoriTriase();
    const isResusitasi = tempTriase == 'resusitasi';
    const isEmergent = tempTriase == 'emergent';
    const isUrgent = tempTriase == 'urgent';
    const isLessUrgent = tempTriase == 'less_urgent';
    const isNoUrgent = tempTriase == 'non_urgent';
    if (isResusitasi) {
        kategoriTriase = configWaktuRespon[0];
        kategoriColor = "#F44336";
    } else if (isEmergent) {
        kategoriTriase = configWaktuRespon[1];
        kategoriColor = "#FC8338";
    } else if (isUrgent) {
        kategoriTriase = configWaktuRespon[2];
        kategoriColor = "gold";
    } else if (isLessUrgent) {
        kategoriTriase = configWaktuRespon[3];
        kategoriColor = "#33ff3b";
    } else if (isNoUrgent) {
        kategoriTriase = configWaktuRespon[4];
        kategoriColor = "#4CAF50";
    } else {
        kategoriTriase = "-";
        kategoriColor = "black";
    }
    $("#kategori-triase").text(kategoriTriase.replace(/_/g, " ").toUpperCase()).css("color", kategoriColor);
}

function getKategoriTriase() {
    let valGcs = $("#hasil_gcs-form").val();
    valGcs = parseInt(valGcs);
    const isEmptyValGcs = !valGcs || valGcs == 0;
    if (isEmptyValGcs) valGcs = null;
    let kategoriGcs = null;
    let prevVal = 0;

    const isResusitasi = $(".resusitasi-group").is(':checked') || kategoriGcs == 'resusitasi';
    const isEmergent = $(".emergent-group").is(':checked') || kategoriGcs == 'emergent';
    const isUrgent = $(".urgent-group").is(':checked') || kategoriGcs == 'urgent';
    const isLessUrgent = $(".less_urgent-group").is(':checked') || kategoriGcs == 'less_urgent';
    const isNoUrgent = $(".non_urgent-group").is(':checked') || kategoriGcs == 'non_urgent';
    if (isResusitasi) {
        return 'resusitasi';
    } else if (isEmergent) {
        return 'emergent';
    } else if (isUrgent) {
        return 'urgent';
    } else if (isLessUrgent) {
        return 'less_urgent';
    } else if (isNoUrgent) {
        return 'non_urgent';
    } else {
        return null;
    }
}