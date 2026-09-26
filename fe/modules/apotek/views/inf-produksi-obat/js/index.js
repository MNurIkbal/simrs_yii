
$(document).ready(function() {
    // Generate Table
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
        ajax: '/apotek/inf-produksi-obat/get-data',
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
                title: "Tanggal Pemesanan",
                data: "tglpemesanan",
            },
            {
                title: "No.Pemesanan",
                data: "nopemesanan"
            },
            {
                title: "Status",
                data: "status_pemesanan",
            },
            {
                title: "Tanggal Verifikasi",
                data: "tgl_aprove",
                searchable: false,
            },
            {
                title: "No.Produksi",
                data: "noproduksi",
                searchable: false,
            },
            {
                title: "Pemesan",
                data: "pegawai_pemesanan",
                searchable:false,
            },
            {
                title: "Obat",
                data: "obat",
                searchable:false,
            },
            {
                title: "Catatan",
                data: "catatan",
                searchable: false,
            }
        ],
    
        drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body'
        });
        }
    });
    $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' readonly='true' class='form-control endDate'><input type='text' style='display:none'  class='targetDate'></div>"
        ],
        [
            4, status_dropdown
        ]
    ], {2:0,3:1,4:2});
    dateRangeHelper(".startDate",".endDate",".targetDate");
});

$("#data-batal").on("click",function (event) {
    event.preventDefault();
    var id = table.row('.selected').data();
    $(this).docoForm("click", {
      url : "/apotek/inf-produksi-obat/update-status?status=batal&id="+id.primary,
      confirmTitle: i18next.t("Konfirmasi"),
      confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
      method: "POST",
      success: function (data) {
        $('.data-varifikasi').prop('disabled', true);
        table.ajax.reload(null,false)
      },
    });
});


$("#data-varifikasi").on("click",function (event) {
    event.preventDefault();
    var id = table.row('.selected').data();
    $(this).docoForm("click", {
      url : "/apotek/inf-produksi-obat/update-status?status=varifikasi&id="+id.primary,
      confirmTitle: i18next.t("Konfirmasi"),
      confirmMessage: i18next.t("Apa anda yakin ingin verifikasi data ini?"),
      method: "POST",
      success: function (data) {
        $('.data-batal').prop('disabled', true);
        table.ajax.reload(null,false)
      },
    });
});

$(document).on('click', '#example tr', function(){
    var _data = table.row('.selected').data();
    if(_data !== undefined) {
        $("#btn-cetak-produksi").prop("disabled", false);
        if(_data.status_pemesanan == "Belum Verifikasi"){
            $("#data-varifikasi").prop("disabled", false);
            if (_data.pegawaipemesanan_id == user_login) {
                $("#data-batal").prop("disabled", false);
                $("#btn-edit").prop("disabled", false);
            } else {
                $("#data-batal").prop("disabled", true);
                $("#btn-edit").prop("disabled", true);
            }
        }else{
            $("#data-batal").prop("disabled", true);
            $("#btn-edit").prop("disabled", true);
            $("#data-varifikasi").prop("disabled", true);
        }
    } else {
        $("#btn-cetak-produksi").prop("disabled", true);
        $("#data-batal").prop("disabled", true);
        $("#data-varifikasi").prop("disabled", true);
        $("#btn-edit").prop("disabled", true);
    }
});

$("#btn-cetak-produksi").click(function (e) { 
    e.preventDefault();
    const tableData = table.row(".selected").data();
    if (typeof tableData !== "undefined") {
        const _data = table.row(".selected").data();
        const pemesananproduksi_id = _data?.pemesananproduksiobat_id;

        let target = $(this).attr("data-target");
        const url = target + "pemesananproduksi_id=" +pemesananproduksi_id;
        window.open(url, "_blank");
    }else{
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
    }
});
