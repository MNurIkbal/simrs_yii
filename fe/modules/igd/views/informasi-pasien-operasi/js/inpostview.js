/*
* @Author: rizqi_fitrianto
* @Date:   2018-09-05 15:40:28
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-09-05 18:11:15
*/

$(document).ready(function(){
    _tableoperasi = $('#table-tim-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=tim-operasi-view&id='+_id,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama pegawai',
                data: 'nama_pegawai',
            },
            {
                title: 'Posisi tim',
                data: 'posisi',
            },
        ],
    });
    _tableitemoperasi = $('#table-item-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=pelayanan-operasi-view&id='+_id,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Operasi',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Cyto',
                data: 'cyto',
            },
            {
                title: 'Jenis Luka',
                data: 'jenis_luka',
            },
            {
                title: 'Jenis Operasi',
                data: 'golonganoperasi_nama',
            },
            {
                title: 'Jenis Anastesi',
                data: 'jenisanastesi_nama',
            },
        ],
    });
    _tablepenggunaanbmhp = $('#table-penggunaan-bmhp').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=bmhp-operasi-view&id='+_id,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'jenis_alat_bmhp',
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
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=penggunaan-cairan-view&id='+_id,
        columns: [
            {
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
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=alat-ditubuh-view&id='+_id,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'j_alat',
            },
            {
                title: 'Jumlah',
                data: 'jumlah',
            },
            {
                title: 'Lokasi',
                data: 'lokasi_alat',
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
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=pemeriksaan-pelengkap-view&id='+_id,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Pemeriksaan',
                data: 'pemeriksaan_pelengkap',
            },
            {
                title: 'Nama Jaringan',
                data: 'nama_jaringan',
            },
            {
                title: 'Lokasi',
                data: 'ukuran',
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
        ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=konsultasi-tindakan-view&id='+_id,
        columns: [
            {
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
                data: 'nama_pegawai'
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
    
})