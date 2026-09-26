var asesmenData = data.asesmenmedis
var secSurveyRules = {
    parent: {
        name: 'AsesmenMedisIgdForm',
        type: 'checkbox',
        child: [
            {
                name: 'survey_mata',
                uncheckAllConditions: 'tidak_ada_kelainan'
            },
            {
                name: 'survey_kepala',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '.kepala--dependent',
                        type: 'radio'
                    },
                    {
                        identifier: '#survey_kepala_lacerasi--form',
                        value: ''
                    },
                    {
                        identifier: '#survey_kepala_battle_sign--form',
                        value: ''
                    },
                    {
                        identifier: '#survey_kepala_lainnya--form',
                        value: ''
                    },
                ]
            },
            {
                name: 'survey_mulut',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '.luka_dalam--dependent',
                        type: 'radio'
                    },
                    {
                        identifier: '#survey_mulut_lainnya--form',
                        value: ''
                    },
                ]
            },
            {
                name: 'survey_leher',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '#other-survey_leher',
                        type: 'input-tags'
                    }
                ]
            },
            {
                name: 'survey_telinga',
                uncheckAllConditions: 'tidak_ada_kelainan'
            },
            {
                name: 'survey_extremitas',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '.survey_extremitas_pulsasi--dependent',
                        type: 'radio'
                    }
                ]
            },
            {
                name: 'survey_medulla_spinalis',
                uncheckAllConditions: 'tidak_ada_kelainan'
            },
            {
                name: 'survey_kolumna_vertebralis',
                uncheckAllConditions: 'tidak_ada_kelainan'
            },
            {
                name: 'survey_pelvis',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: 'other-survey_pelvis',
                        type: 'input-tags',
                    }
                ]
            },
            {
                name: 'survey_abdomen',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '#asesmenmedisigdform-survey_abdomen_bising_usus',
                        value: 0
                    },
                    {
                        identifier: '#asesmenmedisigdform-survey_abdomen_nyeri',
                        value: ''
                    },
                    {
                        identifier: '#asesmenmedisigdform-survey_abdomen_memas',
                        value: ''
                    },
                ]
            },
            {
                name: 'survey_dada',
                uncheckAllConditions: 'tidak_ada_kelainan',
                defaultValue: [
                    {
                        identifier: '.survey_dada--dependent',
                        type: 'radio'
                    },
                    {
                        identifier: '.survey_dada1--dependent',
                        type: 'radio'
                    },
                    {
                        identifier: '.survey_dada_bunyi_jantung--dependent',
                        type: 'radio'
                    },
                    {
                        identifier: '.survey_nyeri_dada--dependent',
                        value: ''
                    },
                ]
            }
        ]
    }
}
// Kebutuhan Secondary Survey
$("[name='AsesmenMedisIgdForm[survey_kepala]'][type='checkbox']").dependentMultipleHandler({
    formHandler: [
        {
            value: "kepala",
            class: "kepala--dependent",
        },
        {
            value: "lacerasi",
            id: "survey_kepala_lacerasi--form",
        },
        {
            value: "battle_sign",
            id: "survey_kepala_battle_sign--form",
        },
        {
            value: "lainnya",
            id: "survey_kepala_lainnya--form",
        },
    ],
});
$("[name='AsesmenMedisIgdForm[survey_mulut]'][type='checkbox']").dependentMultipleHandler({
    formHandler: [
        {
            value: "luka_dalam",
            class: "luka_dalam--dependent",
        },
        {
            value: "lainnya",
            id: "survey_mulut_lainnya--form",
        },
    ],
});
$("[name='AsesmenMedisIgdForm[survey_extremitas]'][type='checkbox']").dependentMultipleHandler({
    formHandler: [
        {
            value: "pulsasi",
            class: "survey_extremitas_pulsasi--dependent",
        },
    ],
});
$("[name='AsesmenMedisIgdForm[survey_dada]'][type='checkbox']").dependentMultipleHandler({
    formHandler: [
        {
            value: "dada",
            class: "survey_dada--dependent",
        },
        {
            value: "dada1",
            class: "survey_dada1--dependent",
        },
        {
            value: "nyeri_dada",
            class: "survey_nyeri_dada--dependent",
        },
        {
            value: "bunyi_jantung",
            class: "survey_dada_bunyi_jantung--dependent",
        },
    ],
});
$("[name='AsesmenMedisIgdForm[survey_abdomen]'][type='checkbox']").dependentMultipleHandler({
    formHandler: [
        {
            value: "memas",
            class: "survey_memas--dependent",
        },
        {
            value: "nyeri",
            class: "survey_abdomen_nyeri--dependent",
        },
        {
            value: "lainnya",
            class: "survey_abdomen_lainnya--dependent",
        },
        {
            value: "bising_usus",
            class: "survey_abdomen_bising_usus--dependent",
        },
    ],
});
// $("[name='AsesmenMedisIgdForm[survey_mata]'][type='checkbox']").bind('change', ({delegateTarget}) => {
//     if ( $(delegateTarget).val() == 'tidak_ada_kelainan') {
//         $.each($("[name='AsesmenMedisIgdForm[survey_mata]'][type='checkbox']"), (k, v) => {
//             if ( $(v).val() != 'tidak_ada_kelainan' && $(`[name='AsesmenMedisIgdForm[survey_mata]'][value='${$(v).val()}']`).is(':checked')) {
//                 $(`[name='AsesmenMedisIgdForm[survey_mata]'][value='${$(v).val()}']`).prop('checked', false).uniform()
//             }
//         })
//     } else {
//         if ( $("[name='AsesmenMedisIgdForm[survey_mata]'][value='tidak_ada_kelainan']").is(':checked') ) {
//             $("[name='AsesmenMedisIgdForm[survey_mata]'][value='tidak_ada_kelainan']").prop('checked', false).uniform()
//         }
//     }
// })

$(() => {
    if(is_perawat == true){
        $('#form-asesmen-medis :input').prop('disabled', true);
        $('#asesmenmedisigdform-alergi').tagsinput("removeAll")
    }
    $("#asesmenmedisigdform-tekanandarah_keluar").mask('000/000')
    /*----------  Berat Badan dan IMT Start  ----------*/
    $(".imt_field").keyup(function (e) {
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

        let tb = $('#asesmenmedisigdform-tinggi_badan').val() ? parseFloat($('#asesmenmedisigdform-tinggi_badan').val().replace(',', '.')) : null;
        let bb = $('#asesmenmedisigdform-berat_badan').val() ? parseFloat($('#asesmenmedisigdform-berat_badan').val().replace(',', '.')) : null;
        let kategori_imt = $("#asesmenmedisigdform-imt_kategori");
        let bodymassindex_id = $("#asesmenmedisigdform-bodymassindex_id");
        let field_imt = $("#asesmenmedisigdform-imt");
        let field_bbideal = $("#asesmenmedisigdform-bb_ideal");
        let bbideal = 0.0;
        let imt = 0.0;
        let imt_kategori = '';
        let bodymassindex = '';

        // hitung bmi / imt
        if (bb && tb) {
            imt = (bb / ((tb / 100) * (tb / 100))).toFixed(2);

            $.each(data_bmi, function (index, value) {
                /*if ((imt >= value['bmi_minimum']) && (imt <= value['bmi_maksimum'])){
                    imt_kategori = value['bmi_defenisi'];
                    bodymassindex = value['bodymassindex_id'];
                    console.log(value);
                    return false; //break
                }*/
                if (parseFloat(imt) >= parseFloat(value.bmi_minimum) && parseFloat(imt) <= parseFloat(value.bmi_maksimum)) {
                    imt_kategori = value.bmi_defenisi;
                    bodymassindex = value.bodymassindex_id;
                    return false;
                }
            });

            // hitung berat badan ideal
            if (jeniskelamin == 15) {
                bbideal = parseFloat((tb - 100) - (0.1 * (tb - 100))).toFixed(2);
            } else {
                bbideal = parseFloat((tb - 100) - (0.15 * (tb - 100))).toFixed(2);
            }
        }

        field_imt.val(imt.toString().replace('.', ','));
        kategori_imt.val(imt_kategori);
        bodymassindex_id.val(bodymassindex);
        field_bbideal.val(bbideal.toString().replace('.', ','));
    });
    /*----------  Berat Badan dan IMT end  ----------*/

    // let defaultValueAssigned = []
    // $(".phonenumber").on('keyup', ({ currentTarget }) => {
    //     $(currentTarget).val($(currentTarget).val().replace(/[^0-9.]/g, ""))
    // })
    $(".input-tag").tagsinput()
    $('.default-disabled').prop('disabled', true)
    $("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $(".box-scale-line__btn").bind('click', ({ currentTarget, originalEvent }) => {
        const parentBoxScale = $(currentTarget).parents('.box-scale')
        parentBoxScale.find(".box-scale-line__point").removeClass('box-scale-line--selected')
        $(currentTarget).parent().addClass('box-scale-line--selected')
        const dataPercentage = $(currentTarget).parent().data('percentage')
        parentBoxScale.find('.box-scale-line__hidePercentage').css('width', `${100 - parseInt(dataPercentage)}%`)

        const dataInfo = $(currentTarget).parent().data('info');
        $(currentTarget).closest('.box-scale').find('.box-scale-info').children().css("background-color", "");
        $('.' + dataInfo).css("background", $(currentTarget).css("background-color"));
        if (originalEvent !== undefined) saveChanges()
    })

    $("input[type='checkbox']").on('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const inputName = element.prop('name')

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
    $(".fisik").each(function(){
        const name = $(this)[0].id
        const split = name.split("-");
        if($(this).find("input:radio:checked").val() == 0){
          $(`.${split[1]}-lainnya`).attr("readonly", false);
        }else{
          $(`.${split[1]}-lainnya`).val("");
          $(`.${split[1]}-lainnya`).attr("readonly", true);
        }
    });

    $(".fisik").bind("change",({ currentTarget }) => {
        const element = $(currentTarget);
        const name = element[0].id
        const split = name.split("-");
        if(element.find("input:radio:checked").val() == 0){
        $(`.${split[1]}-lainnya`).attr("readonly", false);
        }else{
        $(`.${split[1]}-lainnya`).val("");
        $(`.${split[1]}-lainnya`).attr("readonly", true);
        }
    });
    $("[data-dependent]").bind('change', ({ currentTarget }) => {
        dependentHandler(currentTarget, ({ otherElement, elementStringChild }) => {
            $(elementStringChild).prop('disabled', !otherElement.is(':checked'))
            if (!otherElement.is(':checked')) {
                if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
                    $(`${elementStringChild}[type="text"]`).tagsinput("removeAll")
                }
                console.log('hapussss')
                $(`${elementStringChild}[type="text"]`).val('')
                $(`${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`).prop('checked', false)
                $(`${elementStringChild}[type="checkbox"],${elementStringChild}[type="radio"]`).trigger('change')
            } //else {
            //     if ($(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput').length > 0) {
            //         $(`${elementStringChild}[type="text"]`).parent().find('.bootstrap-tagsinput input').prop('disabled', false)
            //     }
            // }
            $.uniform.update()
        })
    })

    $("#diagnosaForm").select2InfinityScroll({
        url: '/igd/pemeriksaan-igd/diagnosa-list'
    })
    // $("#diagnosaForm").select2({
    //     data: [{ id: '', text: '-- Pilih --' }].concat(data.diagnosa)
    // })
    $("#gcseyeasmedForm").select2({
        data: data.metodegcs.E
    }).prepend('<option value="" selected="selected">-- Pilih --</option>')
    $("#gcsverbalasmedForm").select2({
        data: data.metodegcs.V
    }).prepend('<option value="" selected="selected">-- Pilih --</option>')
    $("#gcsmotorikasmedForm").select2({
        data: data.metodegcs.M
    }).prepend('<option value="" selected="selected">-- Pilih --</option>')

    $("#gcseyeasmedForm,#gcsverbalasmedForm,#gcsmotorikasmedForm").bind('change', () => {
        const totalScoreEye = typeof $("#gcseyeasmedForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcseyeasmedForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreVerbal = typeof $("#gcsverbalasmedForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcsverbalasmedForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreMotorik = typeof $("#gcsmotorikasmedForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcsmotorikasmedForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreGcs = totalScoreEye + totalScoreVerbal + totalScoreMotorik
        let resultGcs = '-'
        data.gcs.map((item) => {
            if (totalScoreGcs >= item.gcs_nilaimin && totalScoreGcs <= item.gcs_nilaimax) {
                resultGcs = `${item.text}`
            }
        })
        $("input[name='keterangan_gcs']").val(resultGcs)
        $("#asesmenmedisigdform-hasil_gcs").val(totalScoreGcs)
    })

    if ($(`input[name="AsesmenMedisIgdForm[skrining_nyeri]"]:checked`).val() == '1') {
        $("#nyeri-wrapper").show()
        $("#pilih-wrapper").show()
    } else {
        $("#nyeri-wrapper").hide()
        $("#pilih-wrapper").hide()
    }

    $("[name='AsesmenMedisIgdForm[skrining_nyeri]']").bind('change', ({ currentTarget }) => {
        if ($(currentTarget).val() == '1') {
            $("#nyeri-wrapper").show()
            $("#pilih-wrapper").show()
        } else {
            $("#nyeri-wrapper").hide()
            $("#pilih-wrapper").hide()
        }
    })

    if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'dewasa') {
        // $("[data-type='skala_nyeri_anak'] .box-scale-line--selected").data('percentage') = null
        $("#dewasa-wrapper").show()
        $("#anak-wrapper").hide()
    } else if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'anak') {
        // $("[data-type='skala_nyeri'] .box-scale-line--selected").data('percentage') = null
        $("#dewasa-wrapper").hide()
        $("#anak-wrapper").show()
    } else {
        $("#dewasa-wrapper").hide()
        $("#anak-wrapper").hide()
    }

    $("[name='AsesmenMedisIgdForm[pilih_skala]']").bind('change', ({ currentTarget }) => {
        if ($(currentTarget).val() == 'dewasa') {
            $("#dewasa-wrapper").show()
            $("#anak-wrapper").hide()
        } else if ($(currentTarget).val() == 'anak') {
            $("#dewasa-wrapper").hide()
            $("#anak-wrapper").show()
        } else {
            $("#dewasa-wrapper").hide()
            $("#anak-wrapper").hide()
        }
    })

    // Secondary Survey Extremitas

    $('.survey-extremitas-check[type="checkbox"]').bind("change", ({ currentTarget }) => {
        const _elm = $(`#${$(currentTarget).attr("data-target")}`);
        if ($(currentTarget).is(":checked")) {
            _elm.prop("disabled", false);
        } else {
            _elm.val("").prop("disabled", true);
        }
    }
    );

    $("#btn-save-asesmen-medis").bind('click', () => {
        var diagnosisVal = $("#formDiagnosis").val()
        var diagnosis = []
        diagnosisVal.forEach((item, index) => {
            var newItem = {};
            if(item.includes('_')){
                var part1 = item.split('_')
                index = part1[0]
                item = part1[1]
                newItem.id = index
                var part2 = item.split(' - ')
                newItem.kode = part2[0]
                newItem.nama = part2[1]
            }
            newItem.text = item.trim();
            diagnosis.push(newItem);
        });

        $("[type='hidden']").prop('disabled', true)
        $("#asesmenmedisigdform-hasil_gcs").prop('disabled', false)
        let serializeArray = $("#form-asesmen-medis").serializeArray()
        let payload = {
            pendaftaran_id: typeof asesmenData.pendaftaran_id != 'undefined' ? asesmenData.pendaftaran_id : null,
            diagnosis: diagnosis,
            is_kapitis: "0",
        }
        payload['periksatubuh'] = tmpData
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            payloadKey = originFieldName(item.name)
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || typeElement == 'checkbox') {
                if (typeof item.value != 'undefined') {
                    if (typeof payload[payloadKey] != 'undefined') {
                        payload[payloadKey] += `${payload[payloadKey] != '' ? ',' : ''}${item.value}`
                    } else {
                        payload[payloadKey] = item.value
                    }
                } else {
                    payload[payloadKey] = ''
                }
            } else if (nodeElement == 'select' && payloadKey != 'diagnosis') {
                payload[payloadKey] = item.value
            }

        })
        // payload['asesmen_auto'] = 0
        // payload['asesmen_allo'] = 0
        // if ($('#allo_anamnesa').is(':checked')) {
        //     payload['asesmen_allo'] = 1
        // }
        // if ($('#auto_anamnesa').is(':checked')) {
        //     payload['asesmen_auto'] = 1
        // }

        if ($(`input[name="AsesmenMedisIgdForm[skrining_nyeri]"]:checked`).val() == '0') {
            payload['skala_nyeri'] = 11
            payload['skala_nyeri_anak'] = 11
        } else {
            if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'dewasa') {
                if ($("[data-type='skala_nyeri'] .box-scale-line--selected").length > 0) {
                    payload['skala_nyeri'] = parseInt($("[data-type='skala_nyeri'] .box-scale-line--selected").data('percentage')) / 10
                    payload['skala_nyeri_anak'] = 11
                }
            } else if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'anak') {
                if ($("[data-type='skala_nyeri_anak'] .box-scale-line--selected").length > 0) {
                    payload['skala_nyeri_anak'] = parseInt($("[data-type='skala_nyeri_anak'] .box-scale-line--selected").data('percentage')) / 10
                    payload['skala_nyeri'] = 11
                }
            }
        }

        payload['resolusi_image'] = $('.image-frame').width() + "x" + $('.image-frame').height();
        if (payload['cara_datang'] == 0) {
            payload['cara_datang_diantar'] = ''
        }
        $("[type='hidden']").prop('disabled', false)
        $("#asesmenmedisigdform-hasil_gcs").prop('disabled', true)
        if(payload['diagnosis'].length == 0){
            docoNotification('warning', 'Proses gagal!', 'Diagnosis tidak boleh kosong.')
            $('#formDiagnosis').select2('focus');
        }else {
            showLoader()
            $.ajax({
                url: `/igd/pemeriksaan-igd/save-asesmen-medis`,
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: () => {
                    docoNotification('success', 'Proses berhasil!', 'Data asesmen medis berhasil disimpan.')
                    pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pasien_pemeriksaan js
                    $('#tab-asesmen-medis').find('a').trigger('click')
                },
                complete: () => {
                    hideLoader()
                }
            })
        }
        
    })

    var keyAsesmenData = Object.keys(asesmenData)
    if (keyAsesmenData.length > 0) {
        keyAsesmenData.map((key) => {
            var dataField = asesmenData[key]
            if (typeof dataField == 'boolean') {
                dataField = dataField ? '1' : '0'
            }
            if (dataField != null && dataField.constructor != Array) {
                var inputName = `AsesmenMedisIgdForm[${key}]`
                var elementCheckbox = $(`input[name="${inputName}"][type="checkbox"]`)
                var elementRadio = $(`input[name="${inputName}"][type="radio"]`)
                var elementInput = $(`[name="${inputName}"]`)
                if (dataField != '' && (elementInput.prop('disabled') || (elementCheckbox.length > 0 && elementCheckbox.prop('disabled')) || (elementRadio.length > 0 && elementRadio.prop('disabled')))) {
                    elementInput.prop('disabled', false)
                }
                if (key == 'is_kapitis' && dataField == "1") {
                    $("#asesmenmedisigdform-is_kapitis").prop('checked', true)
                } else if (elementCheckbox.length > 0) {
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
                } else if (key == 'diagnosa_id') {
                    var newState = new Option(asesmenData['diagnosa_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                } else if (key == 'nilai_nutrisi') {
                    $("#score-section").text(dataField)
                } else if (key == 'skala_nyeri') {
                    if (dataField == 0) {
                        $(`[data-type="skala_nyeri"] [data-percentage="${dataField}"]`).children().trigger('click')
                    } else {
                        $(`[data-type="skala_nyeri"] [data-percentage="${dataField}0"]`).children().trigger('click')
                    }
                } else if (key == 'skala_nyeri_anak') {
                    if (dataField == 0) {
                        $(`[data-type="skala_nyeri_anak"] [data-percentage="${dataField}"]`).children().trigger('click')
                    } else {
                        $(`[data-type="skala_nyeri_anak"] [data-percentage="${dataField}0"]`).children().trigger('click')
                    }
                } else if (key == 'kategori_triase_disaster') {
                    $(`.triage-disaster [data-value="${dataField}"]`).trigger('click')
                } else if (key == 'kategori_triase_sehari') {
                    $(`.triage-sehari [data-value="${dataField}"]`).trigger('click')
                } else if (key == 'nilai_luka_bakar') {
                    $("[name='AsesmenMedisIgdForm[nilai_luka_bakar]']").val('')
                    const numberInput = asesmenData.persen_luka_bakar != null ? asesmenData.persen_luka_bakar.replace(/derajat_/g, '') : ''
                    if (numberInput != '') {
                        $(`#nilai_luka_bakar-${numberInput}`).val(dataField)
                    }
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

    let tb = $('#asesmenmedisigdform-tinggi_badan').val() ? parseFloat($('#asesmenmedisigdform-tinggi_badan').val()) : null;
    let bb = $('#asesmenmedisigdform-berat_badan').val() ? parseFloat($('#asesmenmedisigdform-berat_badan').val()) : null;
    let field_imt = $("#asesmenmedisigdform-imt");
    let imt = 0.0;

    // hitung bmi / imt
    if (bb && tb) {
        imt = (bb / ((tb / 100) * (tb / 100))).toFixed(2);
    }

    field_imt.val(imt);

    if (is_draft) {
        $("#draft").show()
    } else {
        $("#draft").hide()
    }

    $("#form-asesmen-medis").on("change", () => {
        saveChanges()
    })
})


/////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function () {
    var opsi = detailBagianTubuh[$('.bagian-tubuh').val()];
    $('.bagian-tubuh-detail').append(populateOpsi(opsi));
    if ($('#asesmenmedisigdform-tinggi_badan').val() != '') {
        $('#asesmenmedisigdform-tinggi_badan').val($('#asesmenmedisigdform-tinggi_badan').val().replace('.', ','))
    }
    if ($('#asesmenmedisigdform-berat_badan').val() != '') {
        $('#asesmenmedisigdform-berat_badan').val($('#asesmenmedisigdform-berat_badan').val().replace('.', ','))
    }
    $('.imt_field').keyup();

    $('#btn-print-asesmen-dokter').on('click', function (e) {
        e.preventDefault();
        var url = "/igd/pemeriksaan-igd/cetak-asmed-rd?id=" + pendaftaran_id;
        window.open(url, '_blank');
    });

    $.each(secSurveyRules.parent.child, (index, ruleItems) => {
        let {name, type} = secSurveyRules.parent
        let _elem = `[name='${name}[${ruleItems.name}]']`
        $(`${_elem}[type='${type}']`).bind('change', ({delegateTarget}) => {
            if ( $(delegateTarget).val() == ruleItems.uncheckAllConditions) {
                $.each( $(`${_elem}[type='${type}']`), (k, v) => {
                    if ( $(v).val() != ruleItems.uncheckAllConditions && $(`${_elem}[value='${$(v).val()}']`).is(':checked')) {
                        $(`${_elem}[value='${$(v).val()}']`).prop('checked', false).uniform()

                        if ( $(`#${ruleItems.name}-${$(v).val()}--form`).length ) {
                            $(`#${ruleItems.name}-${$(v).val()}--form`).val('').attr('disabled', true)
                        }

                        if ( ruleItems.defaultValue != undefined) {
                            $.each( ruleItems.defaultValue, (k, v) => {
                                let _identifier = v.identifier
                                if ( v.type != undefined && v.type == 'input-tags') {
                                    $(`${_identifier}`).tagsinput('removeAll')
                                    $(`${_identifier}`).prop('disabled', true)
                                } else if( v.type != undefined && v.type == 'radio' ) {
                                    $(`${_identifier}`).prop('disabled', true)
                                    $(`${_identifier}`).prop('checked', false);

                                    $.uniform.update()
                                }else {
                                    $(`${_identifier}`).val(v.value).attr('disabled', true)
                                }
                            })
                        }
                    }
                })
            } else {
                if ( $(`${_elem}[value='${ruleItems.uncheckAllConditions}']`).is(':checked') ) {
                    $(`${_elem}[value='${ruleItems.uncheckAllConditions}']`).prop('checked', false).uniform()
                }
            }
        })
    })

    var asesmenAuto = data.asesmenmedis.asesmen_auto;
    var asesmenAllo = data.asesmenmedis.asesmen_allo;
    if ((asesmenAuto == undefined || asesmenAuto == '' || asesmenAuto == null || asesmenAuto == '0') && 
        (asesmenAllo == undefined || asesmenAllo == '' || asesmenAllo == null || asesmenAllo == '0')) {
        $('#allo_or_auto-0-asmed').prop('checked', false);
        $('#allo_or_auto-1-asmed').prop('checked', false);
    } else if (asesmenAuto == undefined || asesmenAuto == '' || asesmenAuto == null || asesmenAuto == '0') {
      $('#allo_or_auto-1-asmed').prop('checked', true);
      $('#allo_or_auto-1-asmed').trigger('change');
    } else if (asesmenAllo == undefined || asesmenAllo == '' || asesmenAllo == null || asesmenAllo == '0') {
      $('#allo_or_auto-0-asmed').prop('checked', true);
      $('#allo_or_auto-0-asmed').trigger('change');
    }

    pageFormId = $('#form-asesmen-medis :not([readonly]):not(\'.disable-get-change\')'); // get form id page | declare di pasien_pemeriksaan js
    pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pasien_pemeriksaan js
})
var populateOpsi = function (opsi) {
    var txtopsi = '';
    $.each(opsi, function (k, v) {
        txtopsi += '<option value="' + k + '">' + v + '</option>'
    })
    return txtopsi
}

/*----------  Anatomi tubuh start  ----------*/
var sumbuX = 0;
var sumbuY = 0;



$('.tag').keydown(function (e) {
    if (e.keyCode == 27) {
        $('.add-caption').val('');
        $('.bagian-tubuh').val('');
        $('.bagian-tubuh-detail').find('option').remove();
        $('.tag').attr({
            style: 'display:none;',
        });
        $('.tag').data('show', 1);
        return false;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
    }
});

$('.image-frame').on('click', function (event) {
    event.preventDefault();
    var posX = (event.pageX - $(this).offset().left),
        posY = (event.pageY - $(this).offset().top) - 10,
        tag = $('.tag');
    sumbuX = posX;
    sumbuY = posY;
    if (tag.data('show') != 1) {
        tag.attr({
            style: 'display:none;',
        });
        tag.data('show', 1);
    } else {
        tag.attr({
            style: 'top: ' + (posY + 20) + 'px; left: ' + posX + 'px;width:500px;z-index:3;position:absolute;'
        });
        tag.data('show', 2);
    }
});

var addCaption = function (e) {
    e.preventDefault();
    var date = new Date()
    var m = (date.getMonth() + 1)
    var d = (date.getDate())
    var now = date.getFullYear() + '-' + (('' + m).length < 2 ? '0' : '') + m + '-' + (('' + d).length < 2 ? '0' : '') + d + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
    var tabel = $('.tabel-anggotatubuh');
    var _contentParent = $(this).closest('.well-sm');
    var valBagian = $('.bagian-tubuh').val();
    var valBagianDetail = $('.bagian-tubuh-detail').val();
    if (e.keyCode == 13 || e.keyCode == 27) {
        if (e.keyCode == 13 && (/[\w\d]+/.test($(this).val())) && valBagian != '') {
            var bagian = typeof bagianTubuh[valBagian] != 'undefined' ? bagianTubuh[valBagian] : '-';
            var bagianDetail = typeof detailBagianTubuh[valBagian][valBagianDetail] != 'undefined' ? detailBagianTubuh[valBagian][valBagianDetail] : '-';
            tmpData[counter] = {
                counters: counter + 1,
                bagian: bagian,
                bagianDetail: bagianDetail,
                bagiantubuh_id: valBagian,
                bagiantubuhdetail_id: valBagianDetail,
                koordinat_y: sumbuY,
                koordinat_x: sumbuX,
                created_date: now,
                catatan_tubuh: $(this).val()
            }
            addRow();
            counter++;
            saveChanges()
        }
        $(this).val('');
        $('.bagian-tubuh').val('');
        $('.bagian-tubuh-detail').val('');
        $('.tag').attr({
            style: 'display:none;',
        });
        $('.tag').data('show', 1);
        return false;
    }
}

var addRow = function () {
    var _tabel = $('.tabel-anggotatubuh');
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').hide();
    }
    var clone = $('.default-row').clone();
    _tabel.find('tbody').html('<tr class=\"default-row\" style=\"display:none\">' + clone.html() + '</tr>');
    var i = 1;
    if (Object.keys(tmpData).length == 0) {
        _tabel.find('tbody').html('<tr class=\"text-center\"><td colspan="6">Data Kosong</td></tr>');
    }
    $.each(tmpData, function (key, items) {
        // $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ items.counters +'</span></div>');
        $('.image-frame').append('<div style=\"top: ' + items.koordinat_y + 'px; left: ' + items.koordinat_x + 'px;z-index:3;position:absolute;\" class=\"tag-image counter-' + items.counters + '\"><span class=\"badge bg-warning-400\">' + i + '</span></div>');
        var html = '';
        html += '<tr>';
        // html += '<td>'+ items.counters +'</td>';
        html += '<td>' + i + '</td>';
        html += '<td>' + convertDateByFormat(items.created_date, "d-m-y h:i") + '</td>';
        html += '<td>' + items.bagian + '</td>';
        html += '<td>' + ((items.bagianDetail != null) ? items.bagianDetail : '-') + '</td>';
        html += '<td>' + items.catatan_tubuh + '</td>';
        html += '<td><button style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"' + items.counters + '\">' +
            '<i class=\"fa fa-trash\"></i></button></td>';
        html += '</tr>';
        _tabel.find('tbody').append(html);

        i++;
    })
}

var delRow = function (event) {
    event.preventDefault();
    var _this = $(this);
    // console.log(_this); return;
    // var _counter = _this.data('counter') + 1;
    var _counter = _this.data('counter');
    var _trParent = _this.closest('tr');
    var _tabel = $('.tabel-anggotatubuh');
    _trParent.remove();
    var index = -1;

    $.each(tmpData, function (key, item) {
        if (item.counters == _counter) {
            index = key;
        }
    });

    delete tmpData[index];
    // delete tmpData[_counter];


    // $.each(tmpData, function (key, items) {
    //     if (_counter < key) {
    //         // delete tmpData[key];
    //         tmpData[(key - 1)] = items;
    //         tmpData[(key - 1)]['counters'] = items.counters - 1;
    //     }
    // });

    // $('.counter-' + _counter).remove();
    $('.tag-image').remove();
    addRow();
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').show();
    }
    saveChanges()
    // counter--;
}

$(document).off('click', '.hapus-item')
$(document).on('click', '.hapus-item', delRow);

$('.add-caption').on('keyup', addCaption);

$('.bagian-tubuh').on('change', function () {
    var opsi = detailBagianTubuh[$('.bagian-tubuh').val()]
    $('.bagian-tubuh-detail').find('option').remove().end().append(populateOpsi(opsi))
})

var checkedFieldValue = (fieldName) => {
    return typeof $(`input[name="AsesmenMedisIgdForm[${fieldName}]"]:checked`).val() != 'undefined' ? $(`input[name="AsesmenMedisIgdForm[${fieldName}]"]:checked`).val() : ''
}
var fieldValue = (fieldName) => {
    return $(`input[name="AsesmenMedisIgdForm[${fieldName}]"]`).val()
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
    return rawFieldName.replace('AsesmenMedisIgdForm[', '').replace(']', '')
}

// $("#allo_anamnesa").on("click", function (e) {
//     if ($("#allo_anamnesa").is(':checked') == true) {
//         $("#asesmen_allo_anamnesa--form").prop("disabled", false)
//         $("#asesmen_allo_anamnesa--form").parent().find('.bootstrap-tagsinput').prop("disabled", false)
//     } else {
//         $("#asesmen_allo_anamnesa--form").prop("disabled", true)
//         $("#asesmen_allo_anamnesa--form").tagsinput("removeAll")
//     }
// })
// if ($("#allo_anamnesa").is(':checked') == true) {
//     $("#asesmen_allo_anamnesa--form").prop("disabled", false)
//     $("#asesmen_allo_anamnesa--form").parent().find('.bootstrap-tagsinput').prop("disabled", false)
// } else {
//     $("#asesmen_allo_anamnesa--form").prop("disabled", true)
//     $("#asesmen_allo_anamnesa--form").tagsinput("removeAll")
// }

$("#cara_datang_diantar").on("click", function (e) {
    if ($("#cara_datang_diantar").is(':checked') == true) {
        $("#cara-datang-form").prop("disabled", false)
        $("#cara-datang-form").parent().find('.bootstrap-tagsinput input').prop("disabled", false)
    } else {
        $("#cara-datang-form").prop("disabled", true)
        $("#cara-datang-form").tagsinput("removeAll")
    }
})
if ($("#cara_datang_diantar").is(':checked') == true) {
    $("#cara-datang-form").prop("disabled", false)
    $("#cara-datang-form").parent().find('.bootstrap-tagsinput input').prop("disabled", false)
} else {
    $("#cara-datang-form").prop("disabled", true)
    $("#cara-datang-form").tagsinput("removeAll")
}

function saveChanges() {
    $("[type='hidden']").prop('disabled', true)
    let updatedData = $("#form-asesmen-medis").serializeArray();
    $("[type='hidden']").prop('disabled', false)

    let diagnosis = $("#formDiagnosis").val()
    let diagnosis_raw = []
    diagnosis.forEach((item, index) => {
        var newItem = {};
        if(item.includes('_')){
            var part1 = item.split('_')
            index = part1[0]
            item = part1[1]
            newItem.id = index
            var part2 = item.split(' - ')
            newItem.kode = part2[0]
            newItem.nama = part2[1]
        }
        newItem.text = item.trim();
        diagnosis_raw.push(newItem);
    });

    let skalaNyeri = [{name: "skala_nyeri", value:11},{name: "skala_nyeri_anak", value:11}]
    if ($(`input[name="AsesmenMedisIgdForm[skrining_nyeri]"]:checked`).val() == '0') {
        skalaNyeri[0].value = 11
        skalaNyeri[1].value = 11
    } else {
        if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'dewasa') {
            if ($("[data-type='skala_nyeri'] .box-scale-line--selected").length > 0) {
                skalaNyeri[0].value = parseInt($("[data-type='skala_nyeri'] .box-scale-line--selected").data('percentage')) / 10
                skalaNyeri[1].value = 11
            }
        } else if ($(`input[name="AsesmenMedisIgdForm[pilih_skala]"]:checked`).val() == 'anak') {
            if ($("[data-type='skala_nyeri_anak'] .box-scale-line--selected").length > 0) {
                skalaNyeri[1].value = parseInt($("[data-type='skala_nyeri_anak'] .box-scale-line--selected").data('percentage')) / 10
                skalaNyeri[0].value = 11
            }
        }
    }

    let valueObject = [];
    
    $.each(tmpData, function (indexInArray, valueOfElement) { 
        valueObject.push(valueOfElement)
    });

    updatedData.push(...skalaNyeri, {
        name : "diagnosis",
        value: JSON.stringify(diagnosis)
    }, {
        name : "diagnosis_raw",
        value: JSON.stringify(diagnosis_raw)
    }, {
        name : "pendaftaran_id",
        value: pendaftaran_id
    }, {
        name : "AsesmenMedisIgdForm[periksatubuh]",
        value:  JSON.stringify(Object.assign({}, valueObject))
    }, {
        name : "AsesmenMedisIgdForm[cara_datang_diantar]']",
        value: $("[name='AsesmenMedisIgdForm[cara_datang_diantar]']").val()
    }, {
        name : "AsesmenMedisIgdForm[asesmen_allo_anamnesa]']",
        value: $("[name='AsesmenMedisIgdForm[asesmen_allo_anamnesa]']").val()
    })

    $.ajax({
        url: `/igd/pemeriksaan-igd/set-cache-asmed`,
        method: 'GET',
        data: updatedData,
        success: (data) => {
            if (data.is_draft) {
                $("#draft").show()
            } else {
                $("#draft").hide()
            }
        },
    })
}