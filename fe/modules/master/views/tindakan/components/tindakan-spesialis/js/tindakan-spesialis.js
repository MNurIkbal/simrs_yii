$(document).ready(function() {
    tableCreateTs = $("#table-create-ts-"+ id).docoTabel({
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
            ajax: "tindakan/get-detail-tindakan-spesialis?id=" + id +"&detail=true",
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
                    title: 'Hapus', 
                    data: 'hapus',
                    searchable: false
                },
            ],
            drawCallback : function (event) {
                $('.btn-hapus-ts').on('click', function(){
                    $(this).docoForm('delete',{
                        success : function (data) {
                            tableCreateTs.draw();
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
            url: '/master/tindakan/add-tindakan-spesialis',
            data: {spesialis_id: id, daftartindakan_id: daftartindakan},
            type: 'POST',
            success: function(response){
                tableCreateTs.draw();
                $('.select-daftartindakan').val('').trigger('change')
                docoNotification('success', 'Sukses', 'Simpan data berhasil')
            },
            error: function(response){
                var result = response.responseJSON.response
                docoNotification('error', 'Silahkan cek inputan', result.data.daftartindakan_id[0])
            }
        })
    }
});
