var asesmenData = data.pagt

$(() => {
    let defaultValueAssigned = []
    setRiwayatDietTotal();
    $(".phonenumber").on('keyup', ({ currentTarget }) => {
        $(currentTarget).val($(currentTarget).val().replace(/[^0-9.]/g, ""))
    })
    $(".input-tag").tagsinput()
    $('.default-disabled').prop('disabled', true)
    // $("[type='radio'],[type='checkbox']").not('.monev-form').uniform({
    //     radioClass: 'choice'
    // })

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
            }

            // if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
            //     $(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput input').prop('disabled', otherElement.is(':checked'))
            // }
            $.uniform.update()
        })
    })
    $(".btn-triage button").bind('click', ({ currentTarget }) => {
        $(currentTarget).addClass('btn-triage--active')
        $(currentTarget).parent().find('button').not(currentTarget).removeClass('btn-triage--active')
    })

    $("#btn-save-pagt").bind('click', () => {
        $("[type='hidden']").prop('disabled', true)
        $('[id^=status_gizi-]').prop('disabled', false)
        let serializeArray = $("#form-pagt").serializeArray()
        $('[id^=status_gizi-]').prop('disabled', true)
        let payload = {
            pendaftaran_id: typeof asesmenData.pendaftaran_id != 'undefined' ? asesmenData.pendaftaran_id : null,
            pagt_monev: {}
        }
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            payloadKey = originFieldName(item.name)
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || typeElement == 'checkbox') {
                if (typeof item.value != 'undefined') {
                    if ($(`[name="${item.name}"]`).siblings('.bootstrap-tagsinput').length == 0 || ($(`[name="${item.name}"]`).siblings('.bootstrap-tagsinput').length > 0 && item.value != '00')) { // mencegah value 00 untuk bootstrap input tag
                        if (typeof payload[payloadKey] != 'undefined') {
                            payload[payloadKey] += `${payload[payloadKey] != '' ? ',' : ''}${item.value}`
                        } else {
                            payload[payloadKey] = item.value
                        }
                    }
                } else {
                    payload[payloadKey] = ''
                }
            } else if (nodeElement == 'select') {
                payload[payloadKey] = $(`[name='${item.name}']`).val().replace(/-- Pilih --/g, '')
            }

            if (payloadKey.match(/PagtMonevForm/g) != null) {
                payload.pagt_monev[payloadKey.replace('PagtMonevForm[', '')] = payload[payloadKey]
                delete payload[payloadKey]
            }
        })

        $("[type='hidden']").prop('disabled', false)
        $("input[disabled]").not('.resiko-jatuh-form,.textbox-resiko-jatuh-form,[type="hidden"]').each((indexFormDisabled, formDisabled) => {
            if (typeof payload[originFieldName($(formDisabled).prop('name'))] == 'undefined') {
                payload[originFieldName($(formDisabled).prop('name'))] = ''
            }
        })
        console.log(payload)
        showLoader()
        $.ajax({
            url: `/gizi/asesmen-gizi/save-pagt?id=${pendaftaran_id}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: () => {
                docoNotification('success', 'Proses berhasil!', 'Data pagt berhasil disimpan.')
                $('#tab-pagt').trigger('click')
            },
            complete: () => {
                hideLoader()
            }
        })
    })

    var keyAsesmenData = Object.keys(asesmenData)
    if (keyAsesmenData.length > 0) {
        keyAsesmenData.map((key) => {
            var dataField = asesmenData[key]
            if (typeof dataField == 'boolean') {
                dataField = dataField ? '1' : '0'
            }
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `PagtForm[${key}]`
                var elementCheckbox = $(`input[name="${inputName}"][type="checkbox"]`)
                var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
                var elementInput = $(`[name="${inputName}"]`)
                if (dataField != '' && (elementInput.prop('disabled') || (elementCheckbox.length > 0 && elementCheckbox.prop('disabled')) || (elementRadio.length > 0 && elementRadio.prop('disabled')))) {
                    elementInput.prop('disabled', false)
                }
                if (elementCheckbox.length > 0) {
                    var arrayCheckboxValue = []
                    elementCheckbox.each((index, checkbox) => {
                        arrayCheckboxValue.push($(checkbox).val())
                    })
                    var otherElementCheckbox = $(`input[name="${inputName}"][type="text"]`)
                    if (dataField.split(',').length == 1) {
                        $(`input[name="${inputName}"][type="checkbox"][value="${dataField}"]`).trigger('change')
                    } else {
                        dataField.split(',').map((string) => {
                            if (arrayCheckboxValue.indexOf(string) >= 0) {
                                $(`input[name="${inputName}"][type="checkbox"][value="${string}"]`).trigger('click')
                            } else {
                                // $(`input[name="${inputName}"][type="checkbox"][value="00"]`).trigger('click')
                                otherElementCheckbox.tagsinput('add', string)
                            }
                        })
                    }
                } else if (elementRadio.length > 0) {
                    var elementChecked = $(`input[name="${inputName}"][type="radio"][value="${dataField}"]`)
                    $(`input[name="${inputName}"][type="text"]`).tagsinput("removeAll")
                    if (elementChecked.length > 0) {
                        elementChecked.prop('disabled', false)
                        elementChecked.trigger('click')
                    } else {
                        $(`input[name="${inputName}"][type="radio"][value="00"]`).prop('disabled', false)
                        $(`input[name="${inputName}"][type="radio"][value="00"]`).trigger('click')
                        dataField.split(',').map((string) => {
                            if(string != "00"){
                                $(`input[name="${inputName}"][type="text"]`).tagsinput('add', string)
                            }
                        })
                    }
                } else if (key == 'dokter_id' || key == 'perawat_id') {
                    var newState = new Option(key == 'dokter_id' ? asesmenData['dokter_nama'] : asesmenData['perawat_nama'], dataField, true, true);
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
                    if (elementInput.prop('tagName').toLowerCase() == 'select') {
                        elementInput.trigger('change')
                    }
                }

                $.uniform.update()
            }
        })
        $(".doco-number").trigger('change')
    }
    $(".gizi").on('keyup',function(e){
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        setRiwayatDietTotal();
    });

    $(".monev").keyup(function(e){
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        setMonevTotal();
    });

    $('#pagtform-imt_dewasa').change(function(e) {
        let imt = parseFloat($(this).val().replaceAll(',', '.'))
        sign = 'BK';
        $.each(data.databmi, function( index, value ) {
            if(parseFloat(imt) >= parseFloat(value.bmi_minimum)) {
                sign = value.bmi_sign;
            }
        })
        switch(sign) {
            case 'BK':
                statusGizi = 'gizi_kurang';
                break;
            case 'BN':
                statusGizi = 'normal';
                break;
            case 'KB':
                statusGizi = 'gizi_lebih';
                break;
            case 'O1':
                statusGizi = 'obesitas';
                break;
            case 'O2':
                statusGizi = 'obesitas';
                break;
            default:
                statusGizi = 'gizi_kurang';
                break;
        }
        $('#status_gizi-' + statusGizi).prop('disabled', false).prop('checked', true).prop('disabled', true).trigger('change')
    })

    $('#pagtform-tb,#pagtform-bb').keyup(function(e) {
        imtCount();
    }).trigger('keyup');

    $('[id^=status_gizi-]').prop('disabled', true).trigger('change')
})


var checkedFieldValue = (fieldName) => {
    return typeof $(`input[name="PagtForm[${fieldName}]"]:checked`).val() != 'undefined' ? $(`input[name="PagtForm[${fieldName}]"]:checked`).val() : ''
}
var fieldValue = (fieldName) => {
    return $(`input[name="PagtForm[${fieldName}]"]`).val()
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
        // let otherField
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
                trueValue: otherElement.is(':checked')
            })
        }
    }
}

var originFieldName = (rawFieldName) => {
    return rawFieldName.replace('PagtForm[', '').replace(']', '')
}

$("#bentuk_makanan-sonde_voeding").on("click", function(e){
    if($("#bentuk_makanan-sonde_voeding").is(':checked') == true){
        $("#bentuk_makanan_saji--form").prop("disabled",false)
        $("#bentuk_makanan_hari--form").prop("disabled",false)
    }
    else
    {
        $("#bentuk_makanan_saji--form").prop("disabled",true)
        $("#bentuk_makanan_saji--form").val("0")
        $("#bentuk_makanan_hari--form").prop("disabled",true)
        $("#bentuk_makanan_hari--form").val("0")
    }
})
if($("#bentuk_makanan-sonde_voeding").is(':checked') == true){
    $("#bentuk_makanan_saji--form").prop("disabled",false)
    $("#bentuk_makanan_hari--form").prop("disabled",false)
}
else
{
    $("#bentuk_makanan_saji--form").prop("disabled",true)
    $("#bentuk_makanan_saji--form").val("0")
    $("#bentuk_makanan_hari--form").prop("disabled",true)
    $("#bentuk_makanan_hari--form").val("0")
}

$("#cara_pemberian-vitamin-mineral").on("click", function(e){
    console.log('tess')
    if($("#cara_pemberian-vitamin-mineral").is(':checked') == true){
        $("#cara_pemberian_vitamin--form").prop("disabled",false)
        $("#cara_pemberian_vitamin--form").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
    }
    else
    {
        $("#cara_pemberian_vitamin--form").prop("disabled",true)
        $("#cara_pemberian_vitamin--form").tagsinput("removeAll")
    }
})
if($("#cara_pemberian-vitamin-mineral").is(':checked') == true){
    $("#cara_pemberian_vitamin--form").prop("disabled",false)
    $("#cara_pemberian_vitamin--form").parent().find('.bootstrap-tagsinput input').prop("disabled",false)
}
else
{
    $("#cara_pemberian_vitamin--form").prop("disabled",true)
    $("#cara_pemberian_vitamin--form").tagsinput("removeAll")
}

var imtCount = () => {

    let tb = $('#pagtform-tb').val() ? parseFloat($('#pagtform-tb').val()) : null;
    let bb = $('#pagtform-bb').val() ? parseFloat($('#pagtform-bb').val()) : null;
    // hitung bmi / imt
    if (bb && tb) {
        imt = (bb / ((tb/100) * (tb/100))).toFixed(2);
        $('#pagtform-imt_dewasa').val(imt.toString().replaceAll('.', ','));
        $('#pagtform-imt_dewasa').trigger('change');
    }
}

var setRiwayatDietTotal = () => {
    let makan_pagi_energi = $('#pagtform-makan_pagi_energi').val() ? parseFloat($('#pagtform-makan_pagi_energi').val()) : 0;
    let selingan_pagi_energi = $('#pagtform-selingan_pagi_energi').val() ? parseFloat($('#pagtform-selingan_pagi_energi').val()) : 0;
    let makan_siang_energi = $('#pagtform-makan_siang_energi').val() ? parseFloat($('#pagtform-makan_siang_energi').val()) : 0;
    let selingan_sore_energi = $('#pagtform-selingan_sore_energi').val() ? parseFloat($('#pagtform-selingan_sore_energi').val()) : 0;
    let makan_malam_energi = $('#pagtform-makan_malam_energi').val() ? parseFloat($('#pagtform-makan_malam_energi').val()) : 0;
    let selingan_malam_energi = $('#pagtform-selingan_malam_energi').val() ? parseFloat($('#pagtform-selingan_malam_energi').val()) : 0;
    let total_energi = $("#pagtform-total_energi");
    let energi = 0.0;
    energi = (makan_pagi_energi+selingan_pagi_energi+makan_siang_energi+selingan_sore_energi+makan_malam_energi+selingan_malam_energi);
    total_energi.val(energi);

    let makan_pagi_protein = $('#pagtform-makan_pagi_protein').val() ? parseFloat($('#pagtform-makan_pagi_protein').val()) : 0;
    let selingan_pagi_protein = $('#pagtform-selingan_pagi_protein').val() ? parseFloat($('#pagtform-selingan_pagi_protein').val()) : 0;
    let makan_siang_protein = $('#pagtform-makan_siang_protein').val() ? parseFloat($('#pagtform-makan_siang_protein').val()) : 0;
    let selingan_sore_protein = $('#pagtform-selingan_sore_protein').val() ? parseFloat($('#pagtform-selingan_sore_protein').val()) : 0;
    let makan_malam_protein = $('#pagtform-makan_malam_protein').val() ? parseFloat($('#pagtform-makan_malam_protein').val()) : 0;
    let selingan_malam_protein = $('#pagtform-selingan_malam_protein').val() ? parseFloat($('#pagtform-selingan_malam_protein').val()) : 0;
    let total_protein = $("#pagtform-total_protein");
    let protein = 0.0;
    protein = (makan_pagi_protein+selingan_pagi_protein+makan_siang_protein+selingan_sore_protein+makan_malam_protein+selingan_malam_protein);
    total_protein.val(protein);

    let makan_pagi_lemak = $('#pagtform-makan_pagi_lemak').val() ? parseFloat($('#pagtform-makan_pagi_lemak').val()) : 0;
    let selingan_pagi_lemak = $('#pagtform-selingan_pagi_lemak').val() ? parseFloat($('#pagtform-selingan_pagi_lemak').val()) : 0;
    let makan_siang_lemak = $('#pagtform-makan_siang_lemak').val() ? parseFloat($('#pagtform-makan_siang_lemak').val()) : 0;
    let selingan_sore_lemak = $('#pagtform-selingan_sore_lemak').val() ? parseFloat($('#pagtform-selingan_sore_lemak').val()) : 0;
    let makan_malam_lemak = $('#pagtform-makan_malam_lemak').val() ? parseFloat($('#pagtform-makan_malam_lemak').val()) : 0;
    let selingan_malam_lemak = $('#pagtform-selingan_malam_lemak').val() ? parseFloat($('#pagtform-selingan_malam_lemak').val()) : 0;
    let total_lemak = $("#pagtform-total_lemak");
    let lemak = 0.0;
    lemak = (makan_pagi_lemak+selingan_pagi_lemak+makan_siang_lemak+selingan_sore_lemak+makan_malam_lemak+selingan_malam_lemak);
    total_lemak.val(lemak);

    let makan_pagi_kh = $('#pagtform-makan_pagi_kh').val() ? parseFloat($('#pagtform-makan_pagi_kh').val()) : 0;
    let selingan_pagi_kh = $('#pagtform-selingan_pagi_kh').val() ? parseFloat($('#pagtform-selingan_pagi_kh').val()) : 0;
    let makan_siang_kh = $('#pagtform-makan_siang_kh').val() ? parseFloat($('#pagtform-makan_siang_kh').val()) : 0;
    let selingan_sore_kh = $('#pagtform-selingan_sore_kh').val() ? parseFloat($('#pagtform-selingan_sore_kh').val()) : 0;
    let makan_malam_kh = $('#pagtform-makan_malam_kh').val() ? parseFloat($('#pagtform-makan_malam_kh').val()) : 0;
    let selingan_malam_kh = $('#pagtform-selingan_malam_kh').val() ? parseFloat($('#pagtform-selingan_malam_kh').val()) : 0;
    let total_kh = $("#pagtform-total_kh");
    let kh = 0.0;
    kh = (makan_pagi_kh+selingan_pagi_kh+makan_siang_kh+selingan_sore_kh+makan_malam_kh+selingan_malam_kh);
    total_kh.val(kh);
}

var setMonevTotal = () => {
    let oral_energi = $('#pagtmonevform-oral_energi').val() ? parseFloat($('#pagtmonevform-oral_energi').val()) : 0;
    let enteral_energi = $('#pagtmonevform-enteral_energi').val() ? parseFloat($('#pagtmonevform-enteral_energi').val()) : 0;
    let parenteral_energi = $('#pagtmonevform-parenteral_energi').val() ? parseFloat($('#pagtmonevform-parenteral_energi').val()) : 0;
    let total_asupan_energi = $("#pagtmonevform-total_asupan_energi");
    let total_energi = 0.0;
    total_energi = (oral_energi+enteral_energi+parenteral_energi);
    total_asupan_energi.val(total_energi);

    let oral_protein = $('#pagtmonevform-oral_protein').val() ? parseFloat($('#pagtmonevform-oral_protein').val()) : 0;
    let enteral_protein = $('#pagtmonevform-enteral_protein').val() ? parseFloat($('#pagtmonevform-enteral_protein').val()) : 0;
    let parenteral_protein = $('#pagtmonevform-parenteral_protein').val() ? parseFloat($('#pagtmonevform-parenteral_protein').val()) : 0;
    let total_asupan_protein = $("#pagtmonevform-total_asupan_protein");
    let total_protein = 0.0;
    total_protein = (oral_protein+enteral_protein+parenteral_protein);
    total_asupan_protein.val(total_protein);

    let oral_lemak = $('#pagtmonevform-oral_lemak').val() ? parseFloat($('#pagtmonevform-oral_lemak').val()) : 0;
    let enteral_lemak = $('#pagtmonevform-enteral_lemak').val() ? parseFloat($('#pagtmonevform-enteral_lemak').val()) : 0;
    let parenteral_lemak = $('#pagtmonevform-parenteral_lemak').val() ? parseFloat($('#pagtmonevform-parenteral_lemak').val()) : 0;
    let total_asupan_lemak = $("#pagtmonevform-total_asupan_lemak");
    let total_lemak = 0.0;
    total_lemak = (oral_lemak+enteral_lemak+parenteral_lemak);
    total_asupan_lemak.val(total_lemak);

    let oral_kh = $('#pagtmonevform-oral_kh').val() ? parseFloat($('#pagtmonevform-oral_kh').val()) : 0;
    let enteral_kh = $('#pagtmonevform-enteral_kh').val() ? parseFloat($('#pagtmonevform-enteral_kh').val()) : 0;
    let parenteral_kh = $('#pagtmonevform-parenteral_kh').val() ? parseFloat($('#pagtmonevform-parenteral_kh').val()) : 0;
    let total_asupan_kh = $("#pagtmonevform-total_asupan_kh");
    let total_kh = 0.0;
    total_kh = (oral_kh+enteral_kh+parenteral_kh);
    total_asupan_kh.val(total_kh);
}
