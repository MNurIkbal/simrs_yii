$(document).ready(function() {
    $('.content-rujukan').prop('hidden', true)

    if($('#rujukan').val() == 1) {
        $('.group-rujukan-partial').show()
        $('.group-rujukan-penuh').hide()
    } else {
        $('.group-rujukan-penuh').show()
        $('.group-rujukan-partial').hide()
    }

    $(".select2PpkRujukan").select2({
        placeholder: "ketik kode atau nama ppk minimal 3 karakter",
        minimumInputLength: 3,
        ajax: {
            url: "/api/bpjs/referensi-faskes-new",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    asal_rujukan: 2,
                    type: 'public'
                }
                return query;
            },
        },
        templateSelection: function (res) {
            ppkRujukan = res.id
            ppkRujukanText = res.text
            return res.text;
        }
    });
    
    // renderPickadate($('#tanggal-rencana-kunjungan'), {
    //     dependElementPicker: $('#btnDatePick'),
    //     // minDate: convertDateByFormat(new Date(), 'd-M-Y'),
    //     moreThanToday: true,
    //     defaultValue: new Date(),
    // })

    $('#tb-spesialis tbody').on( 'click', '.selected-spesialis', function () {
        // let _data = table.row('.selected').data();
        // let _data = table.row(this).data();
        let _data = table.row($(this).parents('tr')).data();
        if(_data) {
            $('#kode_ppkrujukan').val(ppkRujukan)
            $('#dirujukke').val(ppkRujukanText)
            $('#dirujukke_nama').val(ppkRujukanText)
            $('#kode_ppkrujuk').val(ppkRujukan)
            $('#rujukan').val(_data.namaSpesialis)
            $('#kode_spesialis').val(_data.kodeSpesialis)
            $('#spesialis').val(_data.namaSpesialis)
            $('#modal_pencarian_rujukan').modal('toggle');
            $('#tmp_tgl_kunjungan').val($('#tanggal-rencana-kunjungan').val())
            // $("#modal_pencarian_rujukan .close").click()
        }
    });

    $('#btn-simpan-rujuk-partial').click(function (e) { 
        e.preventDefault();
        if(!ppkRujukan){
            docoNotification('error', 'Proses Gagal!', 'PPK rujuk tidak boleh kosong');
            return false;
        }
        $('#kode_ppkrujukan').val(ppkRujukan)
        $('#dirujukke').val(ppkRujukanText)
        $('#dirujukke_nama').val(ppkRujukanText)
        $('#kode_ppkrujuk').val(ppkRujukan)
        $('#tmp_tgl_kunjungan').val($('#tanggal-rencana-kunjungan').val())
        $('#modal_pencarian_rujukan').modal('toggle');
    });
    // $("#btnDatePick").pickadate({ minDate: -0, maxDate: "+6D" });
});

$(document).on("change", "#tanggal-rencana-kunjungan", function () {
    var tgl_rencana = $(this).val();
    $("#tanggal_rencana_kunjungan").val(tgl_rencana);
});

