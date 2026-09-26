var { premedicationList } = pageVars;
var isUpdatePremed = 0;
var regOther = 134;

$(document).ready(() => {
    var regional_other = $('.anestesi_regional_other');
    var anestesi_regional = $('input[name="AnestesiForm[anestesi_regional]"]');
    // Set visible regional other value
    $('#regional_other').val(regional_other.val());

    // Set hidden regional other value
    $('#regional_other').on('change', function() {
        if ($(this).val() != regional_other.val()) {
            regional_other.val($(this).val());
        }
    })

    // Reset other on change
    anestesi_regional.on('change', function() {
        var regVal = $('input[name="AnestesiForm[anestesi_regional]"]:checked').val();
        if (regVal != regOther) {
            $('#regional_other').val('');
        }
    });

    loadPremedication(premedicationList);

    $("#btn-save-anesthetic").on("click", function () {
        var data = $("#anesthetic-form *").serializeArray();
        data.push({
            name: 'detailAnestesi',
            value: JSON.stringify(premedicationList)
        });
        data.push({
            name: 'isUpdatePremed',
            value: isUpdatePremed
        });

        $().docoForm('click', {
            skipConfirm: false,
            skipConfirmMessage: true,
            data: data,
            url: '/bedah/informasi-pasien-anestesi/anestesi',
            success: function (response) {
                // Reset isUpdate
                isUpdatePremed = 0;
            }
        });
    });
});

function loadPremedication (_obj) {
    var no = 0;
    var row = "";
    if (Object.keys(_obj).length > 0) {
        $.each(_obj, function (k, v) {
            if (v !== undefined) {
                var time_delivery = removeNullStr(v.time_delivery);

                row += '<tr class="row-data-premedication">';
                row += '<td>' + v.obatalkes_nama + '</td>';
                row += '<td>' + v.dose + '</td>';
                row += '<td>' + time_delivery + '</td>';
                row += '<td><a class="btn btn-danger btn-sm btn-remove-premedication" data-key="' + k + '"><i class="fa fa-trash"></i></a></td>';
                row += '</tr>';
            }
        })
    } else {
        row += '<tr class="row-default"> <td class="text-center" colspan="4">Belum ada data yang ditambahkan</td> </tr>'
    }

    $('#tb-premedication').find('tbody tr').remove()
    $('#tb-premedication').find('tbody').append(row)
}

$(document).on('click', '.btn-remove-premedication', function () {
    var key = $(this).data('key')
    if (typeof premedicationList[key] !== undefined) {
        delete premedicationList[key];
        isUpdatePremed = 1;
        loadPremedication(premedicationList);
    }
});

function removeNullStr(string)
{
    result = string;
    if (string == 'null' || string == null) {
        result = '';
    }
    return result;
}
