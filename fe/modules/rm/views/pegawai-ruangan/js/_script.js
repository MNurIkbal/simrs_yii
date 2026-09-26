

let autoPegawai = $('.autoPegawai');
let pegawai_id = $('.pegawai_id');
let nama_pegawai = $('#nama_pegawai');
let kelompok_pegawai = $('#kelompok_pegawai');
let pegawai = { list_pegawai: {}};

$.ajax({
    url: '/rm/pegawai-ruangan/get-data-ajax',
    type: 'json',
    success: function(res) {
        let data = [];
        let response = res.data_pegawai;
        for (var i in response) {
            data.push({ id: response[i].pegawai_id, text: response[i].nama_pegawai});
            pegawai.list_pegawai[response[i].pegawai_id] = response[i];
        }

        autoPegawai.select2({
            data: data,
            type: "GET",
            quietMillis: 50,
            minimumInputLength: 2,
        })
        
        autoPegawai.change(function (e) {
            var id = $(this).val();
            var selected = pegawai.list_pegawai[id];
            if (typeof selected !== 'undefined') {
                pegawai_id.val(selected.pegawai_id);
                nama_pegawai.val(selected.nama_pegawai);
                kelompok_pegawai.val(selected.kelompok_pegawai);
            }
        });

        var _pegawai_id = pegawai_id.val();
        $(".autoPegawai").val(_pegawai_id).trigger('change');
    }
})

$(document).ready(function(){
    $('body').tooltip({selector:'[data-tooltip=tooltip]'});
    $('body').tooltip({selector:'[data-toggle=tooltip]'});

    var tabel = $('#example').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'ruangan_nama', name: 'ruangan_nama'},
            {data: 'nama_pegawai', name: 'nama_pegawai'},
            {data: 'kelompok_pegawai', name: 'kelompok_pegawai'},
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                class: 'text-center'
            }
        ],
    });
    
    var tabel_all = $('#example-all').docoTabel({
        columns : [
            // {data: 'rowNum', name : 'rowNum'},
            {data: 'ruangan_nama', name: 'ruangan_nama'},
            {data: 'nama_pegawai', name: 'nama_pegawai'},
            {data: 'kelompok_pegawai', name: 'kelompok_pegawai'},
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                class: 'text-center'
            }
        ],
    });

    var tabel_session = $('#example-session').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'nama_ruangan', name: 'nama_ruangan'},
            {data: 'nama_pegawai', name: 'nama_pegawai'},
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                class: 'text-center'
            }
        ],
    });

    var _afterSave = function (bool) {
        tabel.reload();
        tabel_session.reload();
        tabel_all.reload();
    }

    $(document).on('click','.delete-session', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            success : function (data) {
                _afterSave();
            }
        });
    })

    $(document).on('click','.data-delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            success : function (data) {
                _afterSave();
            }
        });
    })

    $(document).on('click', '.data-reload', function (e) {
        e.preventDefault();
        _afterSave();
    });

    $('#buttonSave').on('click', function (e) {
        $(this).docoForm("click", {
            success : function(data) {
                location.reload();
            }
        });
    });

    $(document).on('click', '.searchmodal', function(){
        $('#searchmodal').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));
    });

    $(document).on("submit", 'form', function(e){
        e.preventDefault();

        var serializedData = $(this).serialize();
        var url = '/rm/pegawai-ruangan/add-session';

        $.ajax({
            type: 'POST',
            url: url,
            data: serializedData,
            success: function(response) {
                tabel_session.reload();
                tabel_all.reload();
            },
            error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseJSON.message);
            }
        });
    });
});




