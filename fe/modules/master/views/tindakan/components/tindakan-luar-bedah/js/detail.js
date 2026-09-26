/* 
    Author : Ripan
*/

$(document).ready(function() {
    tabel = $("#tabel-detail-tindakan-luar-bedah-"+dec_id).docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        paging:true,
        serverSide: true,
        processing: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax:'/master/tindakan/get-data-detail-tindakan-luar-bedah?id='+dec_id,
        columns: [
            {
                title: "No", 
                data:"rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: "Tindakan Di Luar Bedah", 
                data: "tindakanluarbedah_nama", 
            },
            {
                title: "Qty", 
                data: "qty", 
            },
            {
                title: "Ditagihkan", 
                data: "ditagihkan", 
                searchable: false, 
            },
        ],
        language: {
            emptyTable: '<?= (\Yii::t("fe", "Tidak ada data yang tersedia")) ?>',
            info: '<?= (\Yii::t("fe", "")) ?>',
            infoEmpty: '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) ?>',
            infoFiltered: '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)")) ?>',
            lengthMenu: '<?= (\Yii::t("fe", "Menampilkan _MENU_ data")) ?>',
            loadingRecords: '<?= (\Yii::t("fe", "Memuat...")) ?>',
            processing: '<?= (\Yii::t("fe", "Memproses...")) ?>',
            search: '<?= (\Yii::t("fe", "Cari:")) ?>',
            zeroRecords: '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan")) ?>',
            paginate: {
                first: '<?= (\Yii::t("fe", "Pertama")) ?>',
                last: '<?= (\Yii::t("fe", "Pertama")) ?>',
                next: '<?= (\Yii::t("fe", "Selanjutnya")) ?>',
                previous: '<?= (\Yii::t("fe", "Sebelumnya")) ?>'
            },
            aria: {
                sortAscending: '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) ?>',
                sortDescending: '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) ?>'
            }
        }
    });
});