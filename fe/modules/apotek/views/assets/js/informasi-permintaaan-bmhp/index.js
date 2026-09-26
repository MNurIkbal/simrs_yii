$(document).ready(function() {
    $('.data-filter').click(function() {
        tabel.draw();
    });

    tabel = $('#example').docoTabel({
        processing: true,
        serverSide: true,
        filter: true,
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        sorting: [[2, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/apotek/informasi-permintaan-bmhp/get-data',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                width: '50px',
                defaultContent: ''
            },
            {
                width: '50px',
                title: 'No.',
                data: 'rowNum',
                searchable: false, orderable: false
            },
            { title:'Tanggal Permintaan', data: 'tgl_permintaan'},
            { title:'Ruangan Tujuan', data: 'ruangan_asal', name: 'ruangan_asal_id'},
            { title:'No. Pendaftaran', data: 'no_pendaftaran'},
            { title:'Nama Pasien', data: 'nama_pasien'}
        ],
    });
    $('.dataTables_filter').hide();

    $(".filter-form").datatableBootstrapFilter(tabel, [
        [
            2,
            "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' readonly='true' class='form-control endDate'><input type='text' style='display:none'  class='targetDate'></div>"
        ],
        [
            3, ruangan_dropdown
        ],
    ], {2:0,3:1,4:2,5:3});
    dateRangeHelper(".startDate",".endDate",".targetDate");

});

