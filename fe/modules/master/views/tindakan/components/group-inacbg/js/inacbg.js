/* 
    Author : Budi
*/

$(document).ready(function() {
    var tableInacbg;
    tableInacbg = $("#table-inacbg").docoTabel({
        filter: false,
        sorting: [[1, "asc"]],  
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "tindakan/data-detail-tindakan-inacbg?id=" + id,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: '<?= (\Yii::t("fe", "Tindakan"))?>', 
                data: "daftartindakan_nama",
                searchable: false
            },
        ],
    });

    $('#auto-tindakan').select2({
        minimumInputLength: 3,
        ajax: {
            url: url,
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            }
        }
    });

    var tampung_tindakan = {"data":[{id: 6, text: "Adm. Surat Keterangan Kematian", selected: true}],"draw":"2","recordsTotal":1,"recordsFiltered":0};
    
    var emptyTable = '<?= (\Yii::t("fe", "Tidak ada data yang tersedia"))?>';
    var info = '<?= (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"))?>';
    var infoEmpty = '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data"))?>';
    var infoFiltered = '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)"))?>';
    var lengthMenu = '<?= (\Yii::t("fe", "Menampilkan _MENU_ data"))?>';
    var loadingRecords = '<?= (\Yii::t("fe", "Memuat..."))?>';
    var processing = '<?= (\Yii::t("fe", "Memproses..."))?>';
    var search = '<?= (\Yii::t("fe", "Cari:"))?>';
    var zeroRecords = '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan"))?>';
    var first = '<?= (\Yii::t("fe", "Pertama"))?>';
    var last = '<?= (\Yii::t("fe", "Terakhir"))?>';
    var next = '<?= (\Yii::t("fe", "Selanjutnya"))?>';
    var previous = '<?= (\Yii::t("fe", "Sebelumnya"))?>';
    var sortAscending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar"))?>';
    var sortDescending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil"))?>';

    tabel = $("#tabel-tampung-tindakan").docoTabel({
        filter: false,
        paging: false,
        sorting: [[2, "asc"]],
        displayLength: false,
        serverSide: true,
        processing: true,
        scrollX: true,
        ajax:'/master/tindakan/get-cache-tindakan',
        columns: [
            {title: "No", data:"no", searchable: false, orderable: false},
            {title: "Tindakan", data: "text", searchable: false},
            {title: "Hapus", data:"hapus"}, 
        ],
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

     $('#auto-tindakan').on('select2:select', function (e) {
        var data = e.params.data;
        tampung_tindakan.data.push(data);
        
        var recordTotal = tampung_tindakan.recordsTotal;
        tampung_tindakan.recordsTotal = parseInt(recordTotal) + 1;
        $.post("/master/tindakan/cache-tindakan",{tampung_tindakan:data,status_chache:"insert"},function(data){
            tabel.draw();
        });
        
    });
});

var hapusTindakan = function(id){
    $.getJSON('/master/tindakan/hapus-cache-tindakan?id='+id,{},function(data){
        tabel.draw();
    });
}