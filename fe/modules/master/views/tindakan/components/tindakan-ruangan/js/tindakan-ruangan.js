/* 
    Author : Randy Vianda Putra (aweutist)
*/

$(document).ready(function() {
    tableCreateTr = $("#table-create-tr-"+ id).docoTabel({
            filter: false,
            sorting: [[3, "ASC"]],
            drawCallback: function() {
                $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
            },
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollY: "400px",
            scrollCollapse: true,
            ajax: "tindakan/get-detail-tindakan-ruangan?id=" + id +"&detail=true",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Kode tindakan', 
                    data: 'daftartindakan_kode',
                    searchable: false
                },
                {
                    title: 'Nama tindakan', 
                    data: 'daftartindakan_nama',
                    searchable: false
                },
                {
                    title: 'Nama tindakan', 
                    data: 'daftartindakan_nama',
                    searchable: false
                },
                {
                    title: 'Default', 
                    data: 'default',
                    searchable: false
                },
                {
                    title: 'Hapus', 
                    data: 'hapus',
                    searchable: false
                },
            ],
            drawCallback : function (event) {
                $('.check-tindakan-ruangan').on('click', function(){
                    if($(this).is(':checked')){
                        updateDefault(true, $(this).attr('action'))
                    }else{
                        updateDefault(false, $(this).attr('action'))
                    }
                });
                $('.btn-hapus-tr').on('click', function(){
                    $(this).docoForm('delete',{
                        success : function (data) {
                            tableCreateTr.draw();
                        }
                    });
                });
            }
        });
    $(".select-daftartindakan").select2({
        placeholder: "",
        minimumInputLength: 3,
        ajax: {
            url: "/master/tindakan/get-list-tindakan",
            dataType: "json",
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },

        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });
});
$('.select-daftartindakan').on('change', function(){
    var daftartindakan = $(this).val()
    if(daftartindakan){
        $.ajax({
            url: '/master/tindakan/add-tindakan-ruangan',
            data: {ruangan_id: id, daftartindakan_id: daftartindakan},
            type: 'POST',
            success: function(response){
                tableCreateTr.draw();
                $('.select-daftartindakan').val('').trigger('change')
                docoNotification('success', 'Sukses', 'Simpan data berhasil')
            },
            error: function(response){
                var result = response.responseJSON.response
                docoNotification('error', 'Silahkan cek inputan', result.data.daftartindakan_id[0])
            }
        })
    }
})

var updateDefault = function(_default, _url){
    var _msg = "Set default "
    if(!_default){
        _msg = "Unset default "
    }
    $.ajax({
        url: _url,
        data: {is_default: _default},
        type: 'POST',
        success: function(response){
            console.log(_msg, response)
            tableCreateTr.draw()
            docoNotification('success', 'Sukses', _msg+'berhasil')
        },
        error: function(response){
            console.log(_msg, response)
            var result = response.responseJSON.response
            docoNotification('error', 'Terjadi kesalahan', _msg+'gagal')
        }
    })
}
