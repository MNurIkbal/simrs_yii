$('.btn-esign-revoke').removeClass('btn-toolbar')

$('.btn-esign-revoke').on('click', function(e) {
    let table = $($(this).data('table')).DataTable()
    let selectedRow = table.row('.selected').data()
    if('primary' in selectedRow) {
        let { type, href, wrapper, width } = $(this).data()
        let wrapperParents = wrapper.split(' ')[0]
        let selectedRow = table.row('.selected').data()

        $(wrapperParents).find('.modal-dialog').css('width', width)
        showLoader('Memuat Halaman...')

        $(wrapper).docoLoad({
            url: href + selectedRow.primary,
            dataType: 'html',
            success: function (data) {
                $(wrapper).parents('.modal').modal('show')
                $('#revoke_reason').select2()
                hideLoader()
            },
            error: function (r) {
                data = JSON.parse(r.responseText)
                docoNotification('error', 'Terjadi Kesalahan', data.message)
                hideLoader()
            }
        })
    } else {
      docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!')
    }
})


$(document).on('click', '#btn-save-revoke', function(e) {
    var formData = $("#revoke-esign-form").serializeArray()
    const data = {};
    $.each(formData, function(index, value) {
      data[value.name] = value.value;
    });


    $().docoForm("click", {
        data: data,
        url: $("#revoke-esign-form").attr("action"),
        method: "POST",
        success: function(data) {
            docoNotification('success', 'Sukses', data.message)
            $(".close-modal-esign").click()
        },
        error: function(r) {
            data = JSON.parse(r.responseText)
            docoNotification('error', 'Terjadi Kesalahan', data.message)
        }
    })
})