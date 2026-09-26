$(document).on('keypress',function(e) {
    $('#tb-pencarian-identitas').hide();
    if(e.which == 13) {
        $("#btn-search__tb-pencarian-identitas").click();
    }
});

function tabelErrorHandling(id) {
    $('#' + id + '_processing').hide();
    $('.dataTables_empty').html('Data tidak ditemukan.');
}

function onClickPilih(ini) {
    var data = table.row($(ini).parents('tr')).data();
    
    var no_rujukan = data.noKunjungan;
    let jenis_pencarian = $("#asal_rujukan_1").val()    

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/pendaftaran/daftar/cek-rujukan',
        data: {
            no_rujukan: no_rujukan,
            jenis_pencarian: jenis_pencarian,
        },
        dataType: 'JSON',
        success: function (res) {
            if (res.metaData.code == 200) {
                $('#no_rujukan').val(no_rujukan );
                $('#modal_pencarian_identitas').modal('hide');
            } 
        },
        error: function (data) {
            if (typeof data.responseJSON.metaData != 'undefined') {
                var message = data.responseJSON.metaData.message;
                docoNotification("warning", message, '');
            }
            tabelErrorHandling('tb-pencarian-identitas');
        }
    });
    
}

$(document).ready(function(){
    $('input[name=jenis_pencarian][value=1]').prop('checked', true).trigger('change');
});

$('#jenis_pencarian').change(function () {
    var base = $("input:radio[name='jenis_pencarian']:checked").val();
    if (base == 1) {
        $('.no_kartu').show();
        $('.nik').hide();
        $('#nik').val(null);
    } else {
        $('.no_kartu').hide();
        $('.nik').show();
        $('#kartu_bpjs').val(null);
    }
});


$(".btn-cari").click(function() {
    $('#tb-pencarian-identitas').show();
    var jenis_pencarian = $("input:radio[name='jenis_pencarian']:checked").val();
    var asal_rujukan = $("#asal_rujukan_1").val();
    var nik = $('#nik').val();
    var no_kartu = $("#kartu_bpjs").val();
    var params = 'nik=' +nik+ '&no_kartu='+no_kartu+'&jenis_pencarian='+jenis_pencarian+'&asal_rujukan='+asal_rujukan;
    $(() => {
        table = $('#tb-pencarian-identitas').docoTabel({
            filter: false,
            sorting: [[1, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            pagination: true,
            destroy: true,
            ajax: {
                url: '/pendaftaran/daftar/get-data-pasien-bpjs?'+params,
                error: function (data) {
                    if (typeof data.responseJSON.metaData != 'undefined') {
                        var message = data.responseJSON.metaData.message;
                        docoNotification("warning", message, '');
                    }
                    tabelErrorHandling('tb-pencarian-identitas');
                }
            },
            select: {
                style: 'os',
                selector: 'td'
            },
            columns: [
                {
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "noKunjungan",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "tglKunjungan",
                    orderable: false,
                },
                {
                    data: "noKartu",
                    orderable: false,
                },
                {
                    data: "nama",
                    orderable: false,
                },
                {
                    data: "nama_perujuk",
                    orderable: false,
                },
                {
                    data: "spesialis",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "aksi",
                    searchable: false,
                    orderable: false,          
                },
            ],
        })
    })
});