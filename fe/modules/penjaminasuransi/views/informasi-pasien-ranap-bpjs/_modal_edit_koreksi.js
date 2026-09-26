$(document).on('click', '#add-diagnosa-2', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-2')
    var tr = '.tr-diagnosa-2'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-2', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-2')
    var parent = $(this).closest('tr');
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-3', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-3')
    var tr = '.tr-diagnosa-3'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-3', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-3')
    var parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-4', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-4')
    var tr = '.tr-diagnosa-4'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-4', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-4')
    var parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-5', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-5')
    var tr = '.tr-diagnosa-5'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-5', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-5')
    var parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-6', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-6')
    var tr = '.tr-diagnosa-6'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-6', function (event) {
    event.preventDefault();
    var diagnosa = $('.diagnosa-6')
    var parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

function newDiagnosa(id, tr) {
    var lastTr = $('' + tr + '').last()
    var classTr = tr.slice(1)
    var kelompok = tr.slice(-1)
    var icd = 'ICD IX'
    var el = '<input type="radio"  class="radio-icdprimer validate-update" name="FormKoreksi[is_icdprimer]" disabled>';
    if (kelompok == 3) {
        icd = 'ICD X'
        el = '';
    }
    var rowSpan = parseInt(id.attr('rowspan')) + 1;
    var newRow = '<tr class="' + classTr + '"><td style="width:20%"> </<td><td style="width:30%"><select class="koreksi-diagnosa" name="koreksi_diagnosa[]" data-type="' + icd + '" data-kelompok="' + kelompok + '" data-icd="" data-asal=""></select></td><td style="width:1%"><button type="button" class="btn btn-danger btn-xsm remove-diagnosa-' + kelompok + '"><i class="fa fa-trash"></i></button></td><td class="hidden" width="1%"><input type="checkbox" class="check-inacbg validate-update" name="FormKoreksi[is_inacbg]" disabled></td><td width="1%" class="hidden">' + el + '</td></tr>';
    id.attr('rowspan', rowSpan)
    lastTr.after(newRow)
}

function removeDiagnosa(id, parent) {
    var rowSpan = parseInt(id.attr('rowspan')) - 1;
    id.attr('rowspan', rowSpan)
    parent.remove()
}
var tempval = '';

function getDiagnosa() {
    $('.koreksi-diagnosa').select2({
        placeholder: 'Pilih Diagnosa',
        minimumInputLength: 3,
        ajax: {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-icd',
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                var notIn = [];
                $('.koreksi-diagnosa').each(function (index) {
                    var val = $(this).val();
                    if (val) {
                        notIn.push(val);
                    }
                });
                params.type_icd = $(this).attr('data-type');
                params.not_in = notIn;
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) {
                return m;
            },
        },
    })
    .on('select2:opening', function(event){
        tempval = $(this).val();
    })
    .on('select2:select', function (event) {
        var val = $(this).val();

        if (tempval !== val) {
            updateStatus = true;
            var hasClass = $(this).closest('tr').hasClass('have-update');

            if (!hasClass) {
                $(this).closest('tr').addClass('have-update');
            }
        }

        var checkBox = $(this).closest('tr').find('.check-inacbg');
        var radio = $(this).closest('tr').find('.radio-icdprimer');
        var text = $(this).select2('data')[0].text;
        if (val) {
            checkBox.attr('disabled', false);
            checkBox.attr('name' , 'FormKoreksi[is_inacbg]['+val+']');
            checkBox.prop('checked', true);
            radio.attr('disabled', false);
            radio.val(val);
            var radioValue = $("input[name='FormKoreksi[is_icdprimer]']:checked").val();
            if (typeof radioValue == 'undefined') {
                radio.prop('checked', true);
            } 
        } else {
            checkBox.attr('disabled', true);
            checkBox.prop('checked', false);
            radio.prop('checked', false);;
        }
    }).on('select2:closing', function (event) {
    });
}


$(document).on('click', '.confirm-update', function(event) {
    var target = $(this).data('target')
    var message = 'Perubahan data diagnosa pasien belum di koreksi, apakah anda akan tetap melanjutkan proses tanpa menyimpan dan mengkoreksi perubahan diagnosa ?';

    if (updateStatus) {
        $.showQuestionDialog('Perhatian !', message, {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            }
        }, function (reaction) {
            if (reaction == 'Yes') {
                if (target == 'back') {
                    history.go(-1);
                } else if(target == 'reset') {
                    location.reload();
                } else if(target == 'eklaim') {
                    goToEklaim();
                }
            }
        });
    } else {
        if (target == 'back') {
            history.go(-1);
        } else if(target == 'reset') {
            location.reload();
        } else if(target == 'eklaim') {
            goToEklaim();
        }
    }
});

$(document).on('change','.validate-update', function() {
    updateStatus = true;
    var hasClass = $(this).closest('tr').hasClass('have-update');

    if (!hasClass) {
        $(this).closest('tr').addClass('have-update');
    }
});
$(document).on('click', '.diagnosa-reset', function() {
    var checkBox = $(this).closest('tr').find('.check-inacbg').prop("checked", false);
    var radio = $(this).closest('tr').find('.radio-icdprimer').prop("checked", false);
    var koreksiDiagnosa = $(this).closest('tr').find('.koreksi-diagnosa');
    koreksiDiagnosa.val('').trigger('change');
});
function goToEklaim() {
    $().docoForm('click', {
        skipConfirm: true,
        skipSuccessNotif: true,
        data: {},
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/proses-eklaim?id='+_id,
        success: function (data) {
            setTimeout(function () {
                $(location).attr('href', '/penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id='+data.id+'&updated='+data.updated);
            }, 1300);
        }
    });
};