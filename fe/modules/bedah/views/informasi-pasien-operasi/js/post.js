/*
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-15 16:48:04
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 14:35:35
 */
$(document).ready(function () {
    setTimeout(function () {
        $(document).find('.unhide-trigger').trigger('change')
    }, 50)

    $('.stepy-navigator').removeClass('hidden')
    $('.stepy-navigator .button-next').addClass('hidden')
    $('.stepy-navigator').append('<button type="button" class="btn btn-xs btn-labeled btn-info btn-save-post"><b><i class="fa fa-floppy-o"></i></b> Simpan</button>')
    $('.btn-save-post').unbind('click')
    $('.btn-save-post').bind('click', function () {
        $().docoForm('click', {
            data: $('#form-post-operasi').serializeArray(),
            url: $('#form-post-operasi').attr('action'),
            success: function (data) {
                $('#tab-operasi').stepy('step', 3)
                $('.btn-save-post').remove()
            }
        });
    })
    $('.select2').select2();
    $('.txt-timepicker').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });
    _tablepemasanganinfus = $('#table-pemasangan-infus').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachepemasanganinfus,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {
            title: 'Jenis cairan infus',
            data: 'jeniscairan_nama',
        },
        {
            title: 'Tanggal pemasangan',
            data: 'tgl_pemasangan',
        },
        {
            title: 'Jumlah tetesan',
            data: 'jumlah_tetes',
        },
        {
            title: 'Aksi',
            data: 'aksi',
        }
        ],
    });
    $('input[name="PostOperasiForm[is_recovery]"]').change(function () {
        if ($(this).val() == 1) {
            $('#postoperasiform-kembali_ruangan_id').val(null).trigger('change')
            if ($('.recovery-ya').hasClass('hidden')) {
                $('.recovery-ya').removeClass('hidden')
            }
            if (!$('.recovery-tidak').hasClass('hidden')) {
                $('.recovery-tidak').addClass('hidden')
            }
        } else {
            $('.txt-timepicker').val(null)
            if (!$('.recovery-ya').hasClass('hidden')) {
                $('.recovery-ya').addClass('hidden')
            }
            if ($('.recovery-tidak').hasClass('hidden')) {
                $('.recovery-tidak').removeClass('hidden')
            }
        }
    })
    $('input[name="PostOperasiForm[is_skrining_nyeri]').change(function () {
        if ($(this).val() == 1) {
            $('.skala-nyeri').attr('readonly', false);
            $('.lokasi').attr('readonly', false);
            $('.metode-nyeri').attr('readonly', false);
        } else {
            $('.skala-nyeri').val('').attr('readonly', true);
            $('.lokasi').val('').attr('readonly', true);
            $('.metode-nyeri').val('').attr('readonly', true);
        }
    })
    $('input[name="PostOperasiForm[is_pasanginfus]').change(function () {
        if ($(this).val() != 1) {
            if (!$('.pasanginfus-ya').hasClass('hidden')) {
                $('.pasanginfus-ya').addClass('hidden');
            }
            // $('.btn-add-infus').removeClass('disabled').attr('data-toggle','modal')
        } else {
            if ($('.pasanginfus-ya').hasClass('hidden')) {
                $('.pasanginfus-ya').removeClass('hidden');
            }
        }
    })
    $('.unhide-trigger').on('change', function () {
        unhide($(this))
    })
})