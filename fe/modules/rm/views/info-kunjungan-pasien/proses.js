const ICD_X = 'ICD X';
const ICD_IX = 'ICD IX';
const DIAG_TERAPI = 6; //ICD IX

$(document).on('click', '#add-diagnosa-1', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-1')
    let tr = '.tr-diagnosa-1'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-1', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-1')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-2', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-2')
    let tr = '.tr-diagnosa-2'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-2', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-2')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-3', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-3')
    let tr = '.tr-diagnosa-3'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-3', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-3')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-4', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-4')
    let tr = '.tr-diagnosa-4'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-4', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-4')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-5', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-5')
    let tr = '.tr-diagnosa-5'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-5', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-5')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

$(document).on('click', '#add-diagnosa-6', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-6')
    let tr = '.tr-diagnosa-6'
    newDiagnosa(diagnosa, tr)
    getDiagnosa()
})

$(document).on('click', '.remove-diagnosa-6', function (event) {
    event.preventDefault();
    let diagnosa = $('.diagnosa-6')
    let parent = $(this).closest('tr')
    removeDiagnosa(diagnosa, parent)
})

function newDiagnosa(id, tr) {
    let lastTr = $('' + tr + '').last()
    let classTr = tr.slice(1)
    let kelompok = tr.slice(-1)
    let icd = ICD_X
    let index = parseInt(lastTr.attr('data-row')) + 1;

    if (kelompok == DIAG_TERAPI) {
        icd = ICD_IX
    }
    let rowSpan = parseInt(id.attr('rowspan')) + 1;
    let newRow = '<tr class="' + classTr + '" data-row="' + index +'"><td style="width:20%"> </<td><td style="width:30%"><select class="koreksi-diagnosa" name="koreksi_diagnosa[]" data-type="' + icd + '" data-kelompok="' + kelompok + '" data-icd="" data-asal="'+ index +' - -"></select></td><td style="width:1%"><button type="button" class="btn btn-danger btn-xsm remove-diagnosa-' + kelompok + '"><i class="fa fa-trash validate-update"></i></button></td></tr>';
    id.attr('rowspan', rowSpan)
    lastTr.after(newRow)
}

function removeDiagnosa(id, parent) {
    let rowSpan = parseInt(id.attr('rowspan')) - 1;
    id.attr('rowspan', rowSpan)
    parent.remove()
}

let tempval = '';

function getDiagnosa() {
    $('.koreksi-diagnosa').select2({
        placeholder: 'Pilih Diagnosa',
        minimumInputLength: 3,
        ajax: {
            url: '/rm/info-kunjungan-pasien/get-icd',
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
   }).on('select2:closing', function (event) {
   });
}

$(document).on('click', '.diagnosa-reset', function() {
    var koreksiDiagnosa = $(this).closest('tr').find('.koreksi-diagnosa');
    koreksiDiagnosa.val('').trigger('change');
});