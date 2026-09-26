
$(document).ready(function() {
    localStorage.clear();
    table = $("#example").docoTabel({
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        filter: true,
        sorting: [[2,'desc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '/apotek/inf-produksi-obat/get-list-produksi',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                sortable: false
            },
            {
                title: "No.Pemesanan",
                data: "nopemesanan"
            },
            {
                title: "Pemesan",
                data: "pegawai_pemesanan",
                searchable:false,
            },
            {
                title: "Status",
                data: "status_produksi",
            },
            {
                title: "No.Produksi",
                data: "noproduksiobat"
            },
            {
                title: "Tanggal Produksi",
                data: "tglproduksiobat",
            },
            {
                title: "Approval",
                data: "pegawai_approve",
                searchable:false,
            },
            {
                title: "Tanggal Pemesanan",
                data: "tglpemesanan",
            },
        ],
    });
    $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
        [
            8,
            "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' readonly='true' class='form-control endDate'><input type='text' style='display:none'  class='targetDate'></div>"
        ],
        [
            6,
            "<div class='input-group'><input type='text' id='rangeDemoStart2' class='form-control startDate2'><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish2' readonly='true' class='form-control endDate2'><input type='text' style='display:none'  class='targetDate2'></div>"
        ],
        [
            4, status_dropdown
        ],
    ], {
        8:0,
        6:1,
        5:2,
        2:3,
        4:4});
    dateRangeHelper(".startDate",".endDate",".targetDate");
    dateRangeHelper(".startDate2",".endDate2",".targetDate2");
    $(".pickadate").pickadate({
        format: "dd-mm-yyyy"
    });
});

$(document).on('click', '#example tr', function(){
    var _data = table.row('.selected').data();
    if(_data !== undefined) {
        $("#btn-lihat").attr("disabled", false);
        if(_data.status_produksi == "Sudah Verifikasi"){
            $("#btn-define-material").attr("disabled", false);
            $("#btn-define-material").attr("data-target", '/apotek/inf-produksi-obat/define-material?pemesananproduksi_id=' + _data.pemesananproduksiobat_id);
        }else{
            $("#btn-define-material").attr("disabled", true);
        }
    } else {
        $("#btn-define-material").attr("disabled", true);
        $("#btn-lihat").attr("disabled", true);
    }
});

$("#btn-define-material").click(function(){
    let _target = $(this).data("target");
    window.location = _target
})