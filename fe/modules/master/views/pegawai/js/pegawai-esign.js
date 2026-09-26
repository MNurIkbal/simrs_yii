$("#tnc-link").click(function(event) {
    event.preventDefault()
    $("#modal-tnc").modal('show')
})

$('#tnc-checkbox').change(function(event) {
    $('#btn-submit').attr('disabled', !$(this).is(":checked"))
})

$('#tnc-checkbox').trigger('change')

$('#tnc-agree').click(function(event) {
    $('#tnc-checkbox').attr('disabled', false)
}) 

$("#reg-esign-form").submit(function(event) {
    event.preventDefault()
    var formData = new FormData(this)

    $(this).docoForm("submit", {
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        method: "POST",
        isUpload: true,
        success: function(data) {
            setTimeout(function() {
                window.location.href = "/master/pegawai"
            }, 2000)
        },
        error: function() {
            setTimeout(() => {
                let count_error = $('#konfig-form-step-0').find('.error').length
                if (parseInt(count_error) != 0) {
                    $('.button-back').trigger('click')
                }
            }, 100)
        }
    })
})

$("input[name='EsignRegForm[nationality_type]']").on("change", function(e) {
    if($(this).val() == 'WNA') {
        $('#sec-wna').show()
        if(type == 'reenroll') {
            $('#sec-wna-reenroll input').attr('disabled', false)
        }
    } else {
        $('#sec-wna').hide()
        if(type == 'reenroll') {
            $('#sec-wna-reenroll input').attr('disabled', true)
        }
    }
})
$("#identity_type").on("change", function(e) {
    if($(this).val() == 'PASSPORT') {
        $("#sec-kitas-kitap").hide()
    } else {
        $("#sec-kitas-kitap").show()
    }   
})
$("input[name='EsignRegForm[nationality_type]']:checked").trigger("change")
$("#identity_type").trigger("change")

