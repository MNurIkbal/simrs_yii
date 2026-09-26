$(() => {
    $(".datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
        earliest: new Date(),
        latest: new Date(new Date().setHours(23, 59, 59, 999))
    });

    $(".phonenumber").on('keyup', ({ currentTarget }) => {
        $(currentTarget).val($(currentTarget).val().replace(/[^0-9.]/g, ""))
    })
    $(".input-tag").tagsinput()
    $('.default-disabled').prop('disabled', true)
    $("[type='radio'],[type='checkbox']").uniform({
        radioClass: 'choice'
    });

    $("#pegawaidokter_id-form").select2InfinityScroll({
        url: '/rajal/pemeriksaan/dokter-list'
    })
    $("#pegawaiperawat_id-form").select2InfinityScroll({
        url: '/rajal/pemeriksaan/perawat-list'
    })
    $("#suku_id-form").select2InfinityScroll({
        url: '/rajal/pemeriksaan/suku-list'
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

    $(".alergi-check").bind('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const dataElement = element.data()
        $(`input[name='ModelAsesmenKeperawatanAdhy[alergi_${dataElement.type}]']`).prop('disabled', !element.is(':checked'))
        if($(`input[name='AsesmenKeperawatan[alergi_${dataElement.type}]']`).prop('disabled')){
            $(`input[name='AsesmenKeperawatan[alergi_${dataElement.type}]']`).val('');
        }
    })
    $('input[name="ModelAsesmenKeperawatanAdhy[kebutuhan_edukasi]"][value="tindakan_keperawatan"]').bind('change', ({ currentTarget }) => {
        $('#kebutuhan_edukasi_keperawatan-form').prop('disabled', !$(currentTarget).is(':checked'))
        if (!$(currentTarget).is(':checked')) {
            $('#kebutuhan_edukasi_keperawatan-form').val('')
        }
    })
    $(".is_nyeri-checkbox").bind('change', ({ currentTarget }) => {
        const element = $(currentTarget)
        const inputName = element.prop('name')
        const otherElement = $(`input[name='${inputName}']:checked`).val()
        if (otherElement == '1') {
            $(`#nyeri-wrapper`).show()
        } else {
            $(`#nyeri-wrapper`).hide()
        }
    })

    $(".nutrisi-check").bind('change', () => {
        const firstQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_1a]"]:checked').val() != 'undefined' ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_1a]"]:checked').data('score')) : 0
        const secondQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_1b]"]:checked').val() != 'undefined' ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_1b]"]:checked').data('score')) : 0
        const thirdQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_2]"]:checked').val() != 'undefined' ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[nutrisi_2]"]:checked').data('score')) : 0
        const totalScore = firstQuestion + secondQuestion + thirdQuestion
        $("#score-section").text(totalScore)
    })

    $(".strongkids-check").bind("change", () => {
    const firstQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[strongkids_kurus]"]:checked').val() != "undefined" ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[strongkids_kurus]"]:checked').data("score")) : 0;
    const secondQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[strongkids_turunbb]"]:checked').val() != "undefined" ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[strongkids_turunbb]"]:checked').data("score")) : 0;
    const thirdQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[strongkids_keadaan_beresiko]"]:checked').val() != "undefined" ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[strongkids_keadaan_beresiko]"]:checked').data("score")) : 0;
    const fourthQuestion = typeof $('input[name="ModelAsesmenKeperawatanAdhy[strongkids_kondisikhusus]"]:checked').val() != "undefined" ? parseInt($('input[name="ModelAsesmenKeperawatanAdhy[strongkids_kondisikhusus]"]:checked').data("score")) : 0;
    const totalScore = firstQuestion + secondQuestion + thirdQuestion + fourthQuestion;
    $("#strongkids-score-section").text(totalScore);
    });

    $(".strongkids-check").trigger("change");
    $(document).off('click', '.btn-add-diagnosa')
    $(document).off('click', '.btn-remove-diagnosa')
    $(document).on('click', '.btn-add-diagnosa', ({ currentTarget }) => {
        const elementBtn = $(currentTarget)
        elementBtn.removeClass('btn-success').addClass('btn-danger').removeClass('btn-add-diagnosa').addClass('btn-remove-diagnosa').find('i').removeClass('fa-plus').addClass('fa-trash')
        $("#table-diagnosa tbody").append(`
            <tr>
                <td>
                    <input type="text" name="diagnosa" class="form-control">
                </td>
                <td>
                    <input type="text" name="tujuan_terukur" class="form-control">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-success btn-xs btn-action btn-add-diagnosa"><i class="fa fa-plus"></i></button>
                </td>
            </tr>
        `)
    })

    $(document).on('click', '.btn-remove-diagnosa', ({ currentTarget }) => {
        if ($("#table-diagnosa tbody tr").length > 1) {
            const elementBtn = $(currentTarget)
            const elementRow = elementBtn.parents('tr')
            elementRow.remove()
            if ($("#table-diagnosa tbody tr").length == 1) {
                $("#table-diagnosa tbody tr button").removeClass('btn-danger').addClass('btn-success').removeClass('btn-remove-diagnosa').addClass('btn-add-diagnosa').find('i').removeClass('fa-trash').addClass('fa-plus')
            }
        }
    })

    $(".box-scale-line__btn").bind('click', ({ currentTarget }) => {
        $(".box-scale-line__point").removeClass('box-scale-line--selected')
        $(currentTarget).parent().addClass('box-scale-line--selected')
        const dataPercentage = $(currentTarget).parent().data('percentage')
        $('.box-scale-line__hidePercentage').css('width', `${100 - parseInt(dataPercentage)}%`)
    })

    $("#submit-anamnesa").bind('click', () => {
        // Check skrining gizi
        let errorMessage = ''
        const checkGizi = $(".nutrisi-check:checked").length
        const checkStrongKids = $(".strongkids-check:checked").length
        console.log(errorMessage != '')
        if (errorMessage != '') {
            docoNotification('warning', 'Silakan cek kembali form', errorMessage)
            return false
        }
        $("[type='hidden']").prop('disabled', true)
        let serializeArray = $("#form-asesmen").serializeArray()
        let payload = {
            anamesa_id: typeof asesmenData.anamesa_id != 'undefined' ? asesmenData.anamesa_id : null,
            nilai_nutrisi: $("#score-section").text(),
            pegawaidokter_id: $("#pegawaidokter_id-form").val(),
            dokter_nama: $("#pegawaidokter_id-form").val() != null ? $("#pegawaidokter_id-form").select2('data')[0].text : '',
            pegawaiperawat_id: $("#pegawaiperawat_id-form").val(),
            perawat_nama: $("#pegawaiperawat_id-form").val() != null ? $("#pegawaiperawat_id-form").select2('data')[0].text : '',
            suku_id: $("#suku_id-form").val(),
            daftarDiagnosa: []
        }
        let typeElement = ''
        let nodeElement = ''
        let payloadKey = ''
        serializeArray.map((item) => {
            typeElement = $(`input[name='${item.name}']`).not("[type='hidden']").prop('type')
            nodeElement = $(`[name='${item.name}']`).not("[type='hidden']").length > 0 ? $(`[name='${item.name}']`).not("[type='hidden']").prop('tagName').toLowerCase() : ''
            if (typeElement == 'radio' || typeElement == 'text' || nodeElement == 'textarea' || (typeElement == 'checkbox' && item.value != '00') || item.value == '00' ) {
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
        if ($(".box-scale-line--selected").length > 0) {
            payload['skala_nyeri'] = parseInt($(".box-scale-line--selected").data('percentage')) / 10
        } else {
            payload['skala_nyeri'] = 0
        }
        $("#table-diagnosa tbody tr").each((index, elementRow) => {
            const valueDiagnosa = $(elementRow).find('input[name="diagnosa"]').val()
            const valueTujuan = $(elementRow).find('input[name="tujuan_terukur"]').val()
            if (valueDiagnosa != '' || valueTujuan != '') {
                payload.daftarDiagnosa.push({
                    diagnosa_keperawatan: valueDiagnosa,
                    tujuan_terukur: valueTujuan
                })
            }
        })
        $("[type='hidden']").prop('disabled', false)
        // showLoader()
        $.ajax({
            url: `/rajal/pemeriksaan/save-asesmen-keperawatan-adhy?pendaftaran_id=${pendaftaranId}&status=${status}`,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: (response) => {
                const {data} = response
                $('#patient-history-tab').find('.riwayat-penyakit-keluarga-text').text(data.riwayat_penyakit_keluarga_list)
                $('#patient-history-tab').find('.riwayat-sosial-ekonomi-text').text(data.status_ekonomi)
                $('#patient-history-tab').find('.status-merokok-text').text( data.status_merokok ? 'Ya' : 'Tidak')

                docoNotification('success', 'Proses berhasil!', 'Data asesmen keperawatan berhasil disimpan.')
                $('#tab-anamnesa').trigger('click')
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
                var inputName = `ModelAsesmenKeperawatanAdhy[${key}]`
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
                } else if (key == 'pegawaidokter_id' || key == 'pegawaiperawat_id') {
                    var newState = new Option(key == 'pegawaidokter_id' ? asesmenData['dokter_nama'] : asesmenData['perawat_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                } else if (key == 'suku_id') {
                    var newState = new Option(asesmenData['suku_nama'], dataField, true, true);
                    elementInput.append(newState).trigger('change')
                } else if (key == 'nilai_nutrisi') {
                    $("#score-section").text(dataField)
                } else if (key == 'skala_nyeri') {
                    $(`[data-percentage="${dataField}0"]`).children().trigger('click')
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
                if (key.match(/alergi_makanan|alergi_obat|alergi_lainnya/g) != null && dataField != null && dataField != '') {
                    $(`[name='${key}_check']`).prop('checked', true).trigger('change');
                }

                $.uniform.update()
            } else if (key == 'daftarDiagnosa' && dataField.length > 0) {
                $("#table-diagnosa tbody").empty()
                var totalDiagnosa = dataField.length
                dataField.map((diagnosa, indexDiagnosa) => {
                    $("#table-diagnosa tbody").append(`
                        <tr>
                            <td>
                                <input type="text" name="diagnosa" value="${diagnosa.diagnosa_keperawatan}" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="tujuan_terukur" value="${diagnosa.tujuan_terukur}" class="form-control">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-action ${indexDiagnosa + 1 >= totalDiagnosa ? 'btn-success btn-add-diagnosa' : 'btn-danger btn-remove-diagnosa'}"><i class="fa ${indexDiagnosa + 1 >= totalDiagnosa ? 'fa-plus' : 'fa-trash'}"></i></button>
                            </td>
                        </tr>
                    `)
                })
            }
        })
        $(".doco-number").trigger('change')
    }
})

var checkedFieldValue = (fieldName) => {
    return typeof $(`input[name="ModelAsesmenKeperawatanAdhy[${fieldName}]"]:checked`).val() != 'undefined' ? $(`input[name="ModelAsesmenKeperawatanAdhy[${fieldName}]"]:checked`).val() : ''
}
var fieldValue = (fieldName) => {
    return $(`input[name="ModelAsesmenKeperawatanAdhy[${fieldName}]"]`).val()
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
    return rawFieldName.replace('ModelAsesmenKeperawatanAdhy[', '').replace(']', '')
}

$('#tab-awal').on('click', function (e) {
    $('#content-anamnesa').docoLoad({
        url:
            '/rajal/pemeriksaan/anamnesa?id=' +
            pendaftaran_id +
            '&pasien_id=' +
            pasien_id +
            '&pegawai_id=' +
            pegawai_id +
            '&status=0',
        dataType: 'html',
        success: function (data) {},
    })
})

$('#tab-ulang').on('click', function (e) {
    $('#content-anamnesa').docoLoad({
        url:
            '/rajal/pemeriksaan/anamnesa?id=' +
            pendaftaran_id +
            '&pasien_id=' +
            pasien_id +
            '&pegawai_id=' +
            pegawai_id +
            '&status=1',
        dataType: 'html',
        success: function (data) {},
    })
})


$('#btn-print-asesmen-perawat').on('click', function (e) {
    e.preventDefault();
    var url = "/rajal/pemeriksaan/cetak-anamnesa?pendaftaran_id=" + pendaftaran_id;
    window.open(url, '_blank');
});

$(".btn-verifikasi-gizi").not('.disabled').on("click", function (event) {
    event.preventDefault();
    $(this).docoForm('click', {
    url: '/rajal/pemeriksaan/verifikasi-skrining-gizi?id=' + pendaftaran_id,
    dataType: 'html',
    success: function (data) {
        location.reload();
    }
    });
});
