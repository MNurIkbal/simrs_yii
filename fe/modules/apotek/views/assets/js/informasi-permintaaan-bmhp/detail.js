$(document).ready(function() {
    $('.data-filter').click(function() {
        tabel.draw();
    });

    tabel = $('#example').docoTabel({
        processing: true,
        serverSide: true,
        filter: true,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/apotek/informasi-permintaan-bmhp/get-detail?id='+id,
        columns: [
            {
                width: '50px',
                title: 'No.',
                data: 'rowNum',
                searchable: false, orderable: false
            },
            { title:'Tanggal Permintaan', data: 'tgl_permintaan'},
            { title:'Nama Obat', data: 'obatalkes_nama'},
            { title:'Qty', data: 'qty_obat'},
            { title:'Satuan', data: 'satuan_input'},
            { title:'Status', data: 'status'}
        ],
    });
    $('.dataTables_filter').hide();

    $(document).on('click', '.data-approve', function() {
        $(this).docoForm('click', {
            url: '/apotek/informasi-permintaan-bmhp/approve-bmhp?id='+id,
            title: 'Sukses',
            method: 'POST',
            type: 'json',
            success: function() {
                docoNotification('success', 'Proses Berhasil', 'Permintaan BMHP berhasil di approve')
                setTimeout(function(){
                    window.location.href = '/apotek/informasi-permintaan-bmhp/#';
                }, 1500);
            },
            error: function(response) {
                var responseText = response.responseJSON.response.message;
                docoNotification('error', 'Proses Gagal', responseText);
            }
        });
    });

});
