$(document).ready(function () {
    $('#addmission_number').select2({
        minimumInputLength: 3,
        width: '100%',
        placeholder: 'No Pendaftaran / No SEP',
        ajax: {
            url: '/penjamin-asuransi/informasi-dashboard-integrasi/get-pendaftaran',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term
                }
                return query;
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) {
                return m;
            }
        }
    }).on('select2:select', function (e) {
        e.preventDefault();
        let noPendaftaran = $(this).val();

        if (!noPendaftaran) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: "/penjamin-asuransi/informasi-dashboard-integrasi/cob-pasien-content",
            data: {
                noPendaftaran: noPendaftaran
            },
            success: function (response) {
                $("#loadContent").html(response);
            },
            error: function (error) {
                console.log(error)
                docoNotification('warning', 'Peringatan', error);
            }
        });        
    });
});