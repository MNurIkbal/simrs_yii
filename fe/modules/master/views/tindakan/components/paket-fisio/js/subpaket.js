// Global Var
var { id_enc, is_mcu } = phpVars
var tabelSubFisio;

var tabelSubFisio;
$(document).ready(function () {
    const urlAjax = `/master/tindakan/paket-fisio-get-paket-detail?id=${id_enc}`
    var tampung_tindakan = {
        "data": [{
            id: 6,
            text: "Adm. Surat Keterangan Kematian",
            selected: true
        }],
        "draw": "2",
        "recordsTotal": 1,
        "recordsFiltered": 0
    };
    var emptyTable = 'Tidak ada data yang tersedia';
    // var info = 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data';
    var info = '';
    var infoEmpty = 'Menampilkan 0 sampai 0 dari 0 data';
    var infoFiltered = '(disaring dari _MAX_ total data)';
    var lengthMenu = 'Menampilkan _MENU_ data';
    var loadingRecords = 'Memuat...';
    var processing = 'Memproses...';
    var search = 'Cari:';
    var zeroRecords = 'Tidak ada data yang ditemukan';
    var first = 'Pertama';
    var last = 'Terakhir';
    var next = 'Selanjutnya';
    var previous = 'Sebelumnya';
    var sortAscending = ': aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar';
    var sortDescending = ': aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil';
    tabelSubFisio = $(`#table-sub-${id_enc}`).docoTabel({
        filter: false,
        sorting: [
            [1, "asc"]
        ],
        paging: true,
        serverSide: true,
        processing: true,
        scrollY: false,
        scrollCollapse: false,
        ajax: urlAjax,
        columns: [{
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false,
            width: '1%'
        },
        {
            title: "Kode Tindakan",
            data: "daftartindakan_kode",
            searchable: false,
            orderable: false,
        },
        {
            title: "Nama Tindakan",
            data: "daftartindakan_nama",
            searchable: false,
            orderable: false,
        }],
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            paginate: {
                first: first,
                last: last,
                next: next,
                previous: previous
            },
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        }
    });
});