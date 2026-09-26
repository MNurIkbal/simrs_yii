/*
* @Author: afil
* @Date:   2018-01-05 10:11:50
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-09-03 17:01:41
*/

/**
 *
 * keperluan js index
 *
 */

var tabel = $('#data-jeniskasuspenyakit').docoTabel({
    columns : [
        {data: 'rowNum', name : 'rowNum'},
        {data: 'ruangan.ruangan_nama', name : 'ruangan_m.ruangan_nama'},
        {data: 'jenisKasusPenyakit.jeniskasuspenyakit_nama', name:'jeniskasuspenyakit_m.jeniskasuspenyakit_nama'},
        {data: 'jenisKasusPenyakit.jeniskasuspenyakit_namalainnya',name : 'jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya'},
        {data: 'aksi',name : 'aksi'}
    ],
    colNoOrder : [0, 2, 3, 4]
});

var _test = function (bool) {
    if (bool) {
        tabel.reload(false);
    } else {
        tabel.reload();
    }
}

var _afterSave = function (bool) {
    // $("#confirm-dialog").modal().hide();
    tabel.reload();
    // tabel_update.reload();
    // tabel_create.reload();
}

var form = false;
$(document).on('click','.data-delete', function(event) {
    event.preventDefault();
    if (form) {
        return false;
    } else {
        form = true;
        $(this).docoForm('delete',{
            success : function (data) {
                form = true;
                tabel.reload();

                // console.log("testing");
                // $("#confirm-dialog").modal().hide();
                // _afterSave()
            }
        });
    }
});

$(document).on('click','.data-aktifasi', function(event) {
    $(this).docoForm('delete',{
        success : function (data) {
            _afterSave()
        }
    });
});

$('.reset-filter').on('click', function (e) {
    e.preventDefault();
    tabel.reset();
});

$('.select2', $('form.form-filter')).change(function (event) {
    event.preventDefault();
    tabel.reload();
});

$('form.form-filter').on('submit', function (e) {
    e.preventDefault();
    tabel.reload();
});

/**
 *
 * keperluan form tambah/ubah
 *
 */

var tabel_update = $('#dataKasuspenyakitruangan').docoTabel({
    columns : [
        {data: 'instalasi'},
        {data: 'nama_ruangan'},
        {data: 'nama_jenis'},
        {data: 'nama_jenislainnya'},
        {data: 'aksi'}
    ],
    colNoOrder : [0, 1, 2, 3, 4],
    bInfo : false,
    bLengthChange : false,
});

var tabel_create = $('#dataKasuspenyakitruanganCreate').docoTabel({
    columns : [
        {data: 'instalasi'},
        {data: 'nama_ruangan'},
        {data: 'nama_jenis'},
        {data: 'nama_jenislainnya'},
        {data: 'aksi'}
    ],
    colNoOrder : [0, 1, 2, 3, 4],
    bInfo : false,
    bLengthChange : false,
});

$('#buttonRefresh').on('click', function (e) {
    location.reload();
});

$('#buttonSave').on('click', function (e) {
    $(this).docoForm("click", {
        success : function(data) {
            location.reload();
        }
    });
});

$('#ajax-form').docoForm('submit', {
    success : function(data) {
        this.formInput[0].reset();
        _afterSave()
    }
});

$(document).on('click', '.delete-kasus', function (e) {
    $(this).docoForm("delete", {
        success : function(data) {
            _afterSave()
        }
    });
});