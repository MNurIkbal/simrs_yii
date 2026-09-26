/*
* @Author: afil
* @Date:   2018-01-09 14:49:25
* @Last Modified by:   afil
* @Last Modified time: 2018-01-12 10:14:12
*/

/**
 *
 * keperluan js index
 *
 */

var tabel = $('#data-jeniskasuspenyakitdiagnosa').docoTabel({
    columns : [
        {data: 'rowNum', name : 'rowNum'},
        {data: 'jeniskasuspenyakit_nama', name : 'jeniskasuspenyakit_nama'},
        {data: 'diagnosa_kode', name:'diagnosa_kode'},
        {data: 'diagnosa_nama',name : 'diagnosa_nama'},
        {data: 'aksi',name : 'aksi'}
    ],
    colNoOrder : [0, 4],
    fnDrawCallback: function( oSettings ) {
        // $(".switch-aktif").bootstrapSwitch('size', 'mini');
        console.log('datatable-callback');
    }
});

var _test = function (bool) {
    if (bool) {
        tabel.reload(false);
    } else {
        tabel.reload();
    }
}

var _afterSave = function (bool) {
    tabel.reload();
    tabel_update.reload();
    tabel_create.reload();
    $(".select2-selection__rendered").text("");
}

$(document).on('click','.data-delete', function(event) {
    event.preventDefault();
    $(this).docoForm('delete',{
        success : function (data) {
            _afterSave()
        }
    });
});

$(document).on('click','.data-aktifasi', function(event) {
    event.preventDefault();
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

var tabel_update = $('#dataKasuspenyakitdiagnosa').docoTabel({
    columns : [
        {data: 'jeniskasuspenyakit_nama'},
        {data: 'diagnosa_nama'},
        {data: 'aksi'}
    ],
    colNoOrder : [2],
    bInfo : false,
    bLengthChange : false,
});

var tabel_create = $('#dataKasuspenyakitdiagnosaCreate').docoTabel({
    columns : [
        {data: 'jeniskasuspenyakit_nama'},
        {data: 'diagnosa_nama'},
        {data: 'aksi'}
    ],
    colNoOrder : [2],
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

// $('#ajax-form').submit( function (event) {
//     event.preventDefault();
//     event.stopImmediatePropagation();

//     $('#ajax-form').docoForm('submit', {
//         success : function(data) {
//             this.formInput[0].reset();
//             _afterSave()
//         }
//     });

//     return false;
// });

$(document).on('click', '.delete-kasus', function (e) {
    $(this).docoForm("delete", {
        success : function(data) {
            _afterSave()
        }
    });
});