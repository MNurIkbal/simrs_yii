var listTriaseTb;
$(() => {
    listTriaseTb = $('#list-triase-tb').docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/igd/pemeriksaan-igd/get-list-triase',
        columns: [
            {
                title: 'No.',
                data: null,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = listTriaseTb.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                title: 'Waktu Input',
                data: 'tgl_triase',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY HH:mm')
                },
                searchable: false,
                orderable: false,
            },
            {
                title: 'Pegawai',
                data: 'pegawai_nama',
                searchable: false,
                orderable: false,
            },
            {
                title: 'No. Bed',
                data: 'no_tempattidur',
                searchable: false,
                orderable: false,
                render: function(data, type, row) {
                    return (row.is_doa ? `<span class="badge badge-default" style="background-color: black; color: black; height: 15px; width: 15px; margin-top: 5px">-</span> ` : '') + (data ? data : '-')
                }
            },
            {
                title: 'Aksi',
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return '<button class="btn btn-info btn-xs btn-labeled btn-pilih-triase" data-triase_id="' + data.triase_id + '"><b><i class="fa fa-check"></i></b>Pilih</button>'
                }
            },
        ],
        initComplete: function (settings) {
            $('.btn-pilih-triase').unbind();
            $('.btn-pilih-triase').bind('click', ({currentTarget}) => {
                $("#modal_backdrop").find(".close").click();
                $("#content-formulir-triase").docoLoad({
                    url: "/igd/pemeriksaan-igd/formulir-triase?id="+pendaftaran_id+"&triase_id="+$(currentTarget).data('triase_id'),
                    dataType: 'html',
                    success : function(data) {}
                });
            });
        }
    });
})
