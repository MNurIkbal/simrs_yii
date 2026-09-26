var table;

var curdate = "<?= date('d-M-Y') ?>";
$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }
        ],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"kasir/inf-retur-tagihan/get-data",
        columns: [
            { //0
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false,
                width: "10%"
            },
            { //1
                title: "<?= \Yii::t('fe', 'Tanggal Retur') ?>",
                data: "tgl_returpelayanan"
            },
            { //2
                title: "<?= \Yii::t('fe', 'No. Kwitansi Retur') ?>",
                data: "no_kwitansi"
            },
            { //3
                title: "<?= \Yii::t('fe', 'No. Pendaftaran') ?>",
                data: "no_pendaftaran"
            },
            { //4
                title: "<?= \Yii::t('fe', 'No. Rekam Medik') ?>",
                data: "no_rekam_medik",
                searchable: false,
            },
            { //5
                title: "<?= \Yii::t('fe', 'Nama Pasien') ?>",
                data: "nama_pasien",
                searchable: false,
            },
            { //6
                title: "<?= \Yii::t('fe', 'Jumlah Retur') ?>",
                data: "total_biayaretur",
                searchable: false,
                class: 'text-right'
            },
        ]
    })

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [[
                    1,
                    "<div class='input-group'><input type='text' id='rangeDemoStart' value='"+curdate+"' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value='"+curdate+"' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>"
                ]], {
        1:0,
        2:1,
        3:2,
    });

    dateRangeHelper(".startDate",".endDate",".targetDate");
});