let onTyping = false
var params = 'tindakanpelayanan_id=' + tindakanpelayanan_id + '&pasienmasukpenunjang_id=' + pasienmasukpenunjang_id;
var table_history_expertise;

$(document).ready(function () {
    var table_id = 'tabel-r';	
    setTimeout(function () {	
        $("#ck_kesan,#ck_kesimpulan").trigger('change')	
    },1)
    table_history_expertise = $('#' + table_id).docoTabel({
        filter: false,
        displayLength: 10,
        select: {
            selector: 'tr'
        },
        sorting: [
            [1, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: baseUrl + 'radiologi/expertise/get-riwayat-expertise?' + params,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        { data: 'tgl_hasilrad' },
        { data: 'kesan' },
        { data: 'kesimpulan' },
        { data: 'aksi' },
        ]
    });
});

$(() => {
    $("#ck_kesan,#ck_kesimpulan").bind('change', () => {

        clearTimeout($(this).data('timer'))
        var timer = setTimeout(function () {
            $.ajax({
                url: '/radiologi/expertise/set-input',
                dataType: 'JSON',
                contentType: 'application/json',
                method: 'POST',
                data: JSON.stringify({
                    kesan: $("#ck_kesan").val(),
                    kesimpulan: $("#ck_kesimpulan").val(),
                    tgl_hasil: $('#inputexpertiseform-tgl_hasilrad').val(),
                }),
                success: function () {
                    $("#preview-content").attr('src', originUrl + '&preview=1')
                }
            });
        }, 2000);

        $(this).data('timer', timer);
    })
})