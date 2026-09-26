var list_update_harga;
var table_alert;
var moduleUrl = "<?= $url ?>";
var reloadAfterUpdatePrice = false;

$("#modal_update_harga").on("hidden.bs.modal", function(){
    if(reloadAfterUpdatePrice) {
        location.reload(true);
    }

    table_alert.destroy();
});

$(document).on('click', "#modal_update_harga button#btn-simpan-alert", function(){
    updateHarga();
});

$(document).on("click", "#tableAlertHarga tr", function(){
    var tbl = $(this).hasClass('selected');
    var rowData = table_alert.row(this).data();

    if (tbl) {
        list_update_harga[rowData.obatalkes_id] = rowData;
    }else{
        delete list_update_harga[rowData.obatalkes_id];
    }
});

function alertHarga(id, reload=false) {
    reloadAfterUpdatePrice = reload;
    if (id == '' || id == null) {
        console.log("Transaksi ID Kosong");
    }else{
        list_update_harga = {};
        var count_obat = 0;

        table_alert = $("#tableAlertHarga").docoTabel({
            filter: false,
            columnDefs: [
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }
            ],
            select: {
                style:    "multiple",
                selector: "tr"
            },
            paging: false,
            ajax: baseUrl+"gudang/"+moduleUrl+"/alert-harga?trace=1&id="+id,
            destroy: true,
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: 'Nama Obat Alkes',
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "Harga Netto Transaksi",
                    data: "disp_harga_transaksi",
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Harga Dasar Sekarang",
                    data: "disp_harga_sekarang",
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Harga Dasar yang Disarankan",
                    data: "disp_harga_sugesstion",
                    orderable: false,
                    class: "text-right"
                },
            ],
            createdRow: function(row, data, dataIndex){
                list_update_harga[data.obatalkes_id] = data;
                count_obat++;
                $(row).addClass("selected");
            },
            drawCallback: function(data){
                if (count_obat > 0) {
                    console.log("ada "+count_obat+" yang berbeda harga");
                    $("#modal_update_harga").modal({ backdrop: 'static', keyboard: false }, "show");
                }else{
                    setTimeout(function(){
                        window.location = baseUrl + "gudang/" + moduleUrl + "/index";
                    }, 3000);

                    console.log("tidak ada obat yang harganya berbeda");
                }
            }
        });


    }
}

function updateHarga() {

    var dataPost = JSON.stringify(list_update_harga);

    $.ajax({
        type: "POST",
        url: "/gudang/"+moduleUrl+"/update-harga?trace=1",
        data: {
            toPost: dataPost
        },
        success: function(data){
            new PNotify({
                title: data.response.title,
                text: data.response.text,
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: 'success'
            });

            if(reloadAfterUpdatePrice) {
                location.reload(true);
            }

            $("#modal_update_harga").modal('toggle');
        },
    });
}
