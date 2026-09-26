/* 
    Author : Wahyu
*/

$(document).ready(function() {
    // alert(id_enkrip)
    var emptyTable = '<?= (\Yii::t("fe", "Tidak ada data yang tersedia")) ?>';
    var info = '<?= (\Yii::t("fe", "")) ?>';
    // var info = '<?= (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) ?>';
    var infoEmpty = '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) ?>';
    var infoFiltered = '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)")) ?>';
    var lengthMenu = '<?= (\Yii::t("fe", "Menampilkan _MENU_ data")) ?>';
    var loadingRecords = '<?= (\Yii::t("fe", "Memuat...")) ?>';
    var processing = '<?= (\Yii::t("fe", "Memproses...")) ?>';
    var search = '<?= (\Yii::t("fe", "Cari:")) ?>';
    var zeroRecords = '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan")) ?>';
    var first = '<?= (\Yii::t("fe", "Pertama")) ?>';
    var last = '<?= (\Yii::t("fe", "Terakhir")) ?>';
    var next = '<?= (\Yii::t("fe", "Selanjutnya")) ?>';
    var previous = '<?= (\Yii::t("fe", "Sebelumnya")) ?>';
    var sortAscending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) ?>';
    var sortDescending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) ?>';

    tabel = $("#tabel-detail-tindakan-bmhp-"+id_enkrip).docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        paging:true,
        serverSide: true,
        processing: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax:'/master/tindakan/get-data-detail-tindakan-bmhp?id='+id_enkrip,
        columns: [
            {title: "No", data:"rowNum", searchable: false, orderable: false},
            {title: "Group", data: "nama_grup", searchable: false, orderable: false},
            {title: "Obat/Alkes", data: "obatalkes_nama", searchable: false},
            {title: "Satuan Input", data: "satuaninput_nama", searchable: false},
            {title: "QTY", data: "qty_input", searchable: false},
            {title: "QTY Konversi", data: "qty_konversi", searchable: false}
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
});