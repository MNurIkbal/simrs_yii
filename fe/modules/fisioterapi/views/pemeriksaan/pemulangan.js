$(document).ready(function () {
    $('#pasienpulangform-tglpasienpulang').val(defaultDate)
    $('#btn-save-pulang').on('click', function (e) {
        e.preventDefault();
        $('#pasienpulangform-carakeluar_id').prop('disabled', false)
        var dataPost = $("#form-pemulanganpasien").serializeArray()
        $(this).docoForm("click", {
            url: '/fisioterapi/pemeriksaan/pemulangan-pasien',
            data: dataPost,
            confirmMessage: 'Apakah anda yakin untuk menyimpan data ini ?',
            method: 'post',
            success: function (response) {
                $('#btn-save-pulang').prop('disabled', true)
                $('#pasienpulangform-carakeluar_id').prop('disabled', true)
                setTimeout(function () {
                    window.location.href = "/fisioterapi/informasi-pasien-fisioterapi"
                }, 1000);
            }
        });
    })
    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY",
        },
        onStart: function () {
            var date = new Date();
            this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()])
        },
    });

})