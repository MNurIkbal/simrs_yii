/*
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-05 15:40:28
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 18:11:15
 */

$(document).ready(function() {
    // _tableoperasi = $('#table-tim-operasi').docoTabel({
    //     filter: false,
    //     displayLength: 10,
    //     paging: false,
    //     processing: true,
    //     serverSide: true,
    //     info: false,
    //     ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=pegawaioperasi&id=' + _id,
    //     columns: [{
    //             title: 'No',
    //             data: 'rowNum',
    //             searchable: false,
    //             orderable: false
    //         },
    //         {
    //             title: 'Nama pegawai',
    //             data: 'pegawai_nama',
    //         },
    //         {
    //             title: 'Posisi tim',
    //             data: 'posisi_tim_nama',
    //         },
    //     ],
    // });
    _tableitemoperasi = $('#table-item-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=itemoperasi&id=' + _id,
        columns: [{
            title: 'No',
            data: 'str_null',
            searchable: false,
            orderable: false
        }, 
        {
            title: 'Tindakan Operasi',
            data: 'str_null',
            visible: false
        },
        {
            title: 'Kegiatan Operasi',
            data: 'kegiatan_operasi_header',
        },
        {
            title: 'Golongan Operasi',
            data: 'str_null',
        },
        {
            title: 'Nama Pegawai',
            data: 'str_null',
        },
        {
            title: 'Posisi Tim',
            data: 'str_null',
        }, ],
        rowCallback: (row, data) => {
            $(row).addClass(`pegawai-${data.pegawai_id} dt-${data.daftartindakan_id}`)
            $(row).attr('style', 'background:#d7f7f0')
            $('td:eq(1)', row).attr( 'colspan', '3');
            $('td:eq(2)', row).css( 'display','none');
            $('td:eq(3)', row).css( 'display','none' );
        },
        drawCallback: (data) => {
            let datas = data.json.data;
            $.each(datas, function (n, row) {
                _tr = $(`.dt-${row.daftartindakan_id}`);
                _timOperasi = JSON.parse(row.tim_operasi);
                _html = '';
                $.each(_timOperasi, function (key, val) {
                    _html += `
                        <tr>
                            <td> ${key+1}</td>
                            <td> ${row.kegiatanoperasi_nama} </td>
                            <td> ${row.golonganoperasi_nama} </td>
                            <td> ${val.pegawai_nama} </td>
                            <td> ${val.posisi_tim_nama} </td>
                        </tr>
                    `;
                });
                
                $(_html).insertAfter(_tr);
             })
        }
    });
    _tablepenggunaanbmhp = $('#table-penggunaan-bmhp').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=penggunaanbmhp&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'obatalkes_nama',
            },
            {
                title: 'Persediaan',
                data: 'persediaan',
            },
            {
                title: 'Tambahan',
                data: 'tambahan',
            },
            {
                title: 'Terpakai',
                data: 'terpakai',
            },
            {
                title: 'Sisa',
                data: 'sisa',
            },
            {
                title: 'Ditagihkan',
                data: 'ditagihkan',
            },
        ],
    });
    _tablepenggunaancairan = $('#table-penggunaan-cairan').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=penggunaancairan&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Kegiatan',
                data: 'kegiatan',
            },
            {
                title: 'Cairan Masuk',
                data: 'cairan_masuk',
            },
            {
                title: 'Cairan Keluar',
                data: 'cairan_keluar',
            },
            {
                title: 'Keterangan',
                data: 'keterangan',
            },
        ],
    });
    _tablealatditubuh = $('#table-alat-ditubuh').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=alatditubuh&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'jenis_alat_nama',
            },
            {
                title: 'Jumlah',
                data: 'jumlah',
            },
            {
                title: 'Lokasi',
                data: 'lokasi',
            },
        ],
    });
    _tablepemeriksaanpelengkap = $('#table-pemeriksaan-pelengkap').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=pemeriksaanpelengkap&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Pemeriksaan',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Nama Jaringan',
                data: 'nama_jaringan',
            },
            {
                title: 'Lokasi',
                data: 'qty',
            },
        ],
    });
    _tablekonsultindakan = $('#table-konsul-tindakan').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=konsultindakan&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Tindakan',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Nama Dokter',
                data: 'dokter_nama'
            },
            {
                title: 'Bagian Tubuh',
                data: 'bagian_tubuh',
            },
            {
                title: 'Alasan',
                data: 'alasan',
            }
        ],
    });
    _tableinstrumen = $('#table-set-instrumen').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=instrumen&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'obatalkes_nama',
            },
            {
                title: 'Persediaan',
                data: 'persediaan',
            },
            {
                title: 'Tambahan',
                data: 'tambahan',
            },
            {
                title: 'Terpakai',
                data: 'terpakai',
            },
            {
                title: 'Sisa',
                data: 'sisa',
            },
        ],
    });
    _tabletindakanluarbedah = $('#tindakan-luar-bedah-table').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-view?type=tindakanluarbedah&id=' + _id,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Tindakan',
                data: 'tindakanluarbedah_nama',
            },
            {
                title: 'Qty',
                data: 'qtytindakan'
            },
        ]
    });

})