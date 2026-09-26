let onTyping = false
var params = 'tindakanpelayanan_id=' + tindakanpelayanan_id + '&pasienmasukpenunjang_id=' + pasienmasukpenunjang_id;
var table_history_expertise;



$(document).ready(function () {
    var table_id = 'tabel-r';
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
        var _wrapKesan = $('<span></span>')
        var _wrapKesimpulan = $('<span></span>')
        var kesan = _wrapKesan.append($("#ck_kesan").val())
        var kesimpulan = _wrapKesimpulan.append($("#ck_kesimpulan").val())
        var _newStyle = 'font-size:12px;font-family:New,Courier,monospaceCourier;text-align:justify;word-spacing:0,001cm';
        kesan.find('span,p').removeAttr('style')
        kesan.find('span,p').attr('style', _newStyle)
        kesimpulan.find('span,p').removeAttr('style')
        kesimpulan.find('span,p').attr('style', _newStyle)

        var _newDiv = `<div style="${_newStyle}">`

        kesan = kesan.html().replace(/<p[^>]*>/g, _newDiv).replace(/<\/p>/g, _newDiv);
        kesimpulan = kesimpulan.html().replace(/<p[^>]*>/g, _newDiv).replace(/<\/p>/g, _newDiv);

        $("#ck_kesan").val(kesan)
        $("#ck_kesimpulan").val(kesimpulan)

        var timer = setTimeout(function () {
            $.ajax({
                url: '/radiologi/expertise/set-input',
                dataType: 'JSON',
                contentType: 'application/json',
                method: 'POST',
                data: JSON.stringify({
                    kesan: kesan,
                    kesimpulan: kesimpulan,

                }),
                success: function () {
                    $("#preview-content").attr('src', originUrl + '&preview=1')


                }
            });
        }, 1000);

        $(this).data('timer', timer);
        $('.cke_wysiwyg_frame').contents().find('body > p').attr('style', 'font-size:12px;font-family:New,Courier,monospaceCourier;word-spacing:0,001cm;text-align:justify;margin-bottom:-16px');

    })
})