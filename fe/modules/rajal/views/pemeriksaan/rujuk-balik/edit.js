
$(document).ready(function(){
    var list_signa = {};
    $('#rujukbalikform-kode_dpjp').select2()
    $('#rujukbalikform-diagnosa').select2()

    $('#rujukbalikform-kode_dpjp').on('change', function (e){
        let data = $('#rujukbalikform-kode_dpjp').select2('data')
        $('#kode_dpjp').val($('#rujukbalikform-kode_dpjp').val());
        $('#rujukbalikform-nama_dokter').val(data[0].text);
    });

    $(".nama_obat").select2({
        placeholder: "Pilih Obat",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-obat-prb",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
        },
    })

    $('.signa').docoPaginationSelec2({
        placeholder: 'Pilih',
        _api: '/rajal/allow/get-list-signa',
        ajax: {
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                }
            },
            results: (data, params) => {
                var more = (params.page * 30) < data.total_count;
                return { results: data.items, more: more };
            },
            processResults: function(res, params) {
                params.page = params.page || 1;
                var arr = [];
                list_signa = {};
                $.each(res.data.signa, function(index, value) {
                    if ( index < res.data.limit) {
                        list_signa[value.signa_id] = {id: value.signa_id, text: value.signa_nama, kode: value.signa_kode, qty: value.qty_obat, iterasi: value.iterasi }
                        arr.push({
                            id: value.signa_id,
                            text: `${value.signa_kode != null ? value.signa_kode : ''} ${value.signa_nama}`,
                            html: `${value.signa_kode != null ? `<b>${value.signa_kode}</b>` : ''} ${value.signa_nama}`
                        })
                    }
                });
                return {
                    results: arr,
                    pagination: {
                        more: res.data.signa.length > res.data.limit
                    }
                };
            },
        },
        templateResult: function(result) {
            return result.html
        },
        templateSelection: function(result) {
            return result.text
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    })

    $('.nama_obat').on('change', function() {
        let data = $(this).select2('data')
        if(typeof data != 'undefined') {
            $(this).closest("td").find("[id$='obatalkes_nama']").val(data[0].text)
        }
    })

    $('.signa').on('change', function() {
        let data = $(this).select2('data')
        if(typeof data != 'undefined') {
            let signa = list_signa[data[0].id];
            $(this).closest("td").find("[id$='signa-text']").val(typeof signa.text == 'undefined' ? null : signa.text)
            $(this).closest("td").find("[id$='signa-kode']").val(typeof signa.kode == 'undefined' ? null : signa.kode)
            $(this).closest("td").find("[id$='qty_signa']").val(typeof signa.kode == 'undefined' ? null : signa.qty)
            $(this).closest("td").find("[id$='iterasi_signa']").val(typeof signa.kode == 'undefined' ? null : signa.iterasi)
        }
    })


    $('#btn-save-rujuk-balik').on('click', function(e) {
        e.preventDefault();
        let _formData = $('#edit-rujuk-balik').serializeArray();
        $().docoForm('click', {
            url: $('#edit-rujuk-balik').attr('action'),
            method: "POST",
            type: "json",
            data: _formData,
            success: function (res) {
                table_rujuk.ajax.reload();
                $('.close-modal-rujuk').trigger('click')
            }
        })
    });
});