<?php


use yii\web\View;
use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal"></button>
    <h5 class="modal-title">Perubahan Harga Obat</h5>
</div>
<div class="modal-header">
    <h4 class="modal-title text-center">Alert!!! <br> Terdapat Perbedaan Harga !!!!</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="modal-body" style="max-height: 650px; overflow-y: scroll;">
            <div class="table-responsive">
                <form id="form-alert-harga">
                    <table id="tableAlertHarga" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th>No.</th>
                                <th>Nama Obat</th>
                                <th>Harga Netto Transaksi (Rp.)</th>
                                <th>Harga Dasar Sekarang (Rp.)</th>
                                <th>Harga Dasar yang Disarankan (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">Data Kosong</td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="text-center" style="font-size: 16px">
                <p>
                    Apakah anda ingin merubah harga dasar yang digunakan saat ini dengan harga yang disarankan oleh sistem ?
                </p>
                <p>
                    Setelah menekan tombol simpan, harga netto obat yang terpilih akan berubah.
                </p>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-block btn-success" id="btn-simpan-alert" disabled>Ya</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-block btn-danger" id="btn-tidak-alert" data-dismiss="modal">Tidak</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var list_update_harga = [];
var table_alert;

$(document).on('click', "#btn-simpan-alert", function(){
    updateHarga();
});

$(document).on('click', "#btn-tidak-alert", function(){
    window.location.replace('/apotek/inf-produksi-obat/index-produksi');
});

$(document).on("click", "#tableAlertHarga tr", function(){
    var select = table_alert.row('.selected').length;
    if(select >= 1){
        $("#btn-simpan-alert").attr("disabled", false)
    }else{
        $("#btn-simpan-alert").attr("disabled", true)
    }
    
    var tbl = $(this).hasClass('selected');
    var rowData = table_alert.row(this).data();

    if (tbl) {
        list_update_harga.push(rowData);
    }else{
        var index = list_update_harga.indexOf(rowData.obatalkes_id);
        list_update_harga.splice(index, 1);
    }
});

$(document).ready(function () {
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
            ajax: '/apotek/inf-produksi-obat/get-alert-harga?id='+id,
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
                    title: "Harga Netto Transaksi (Rp.)",
                    data: "harga_transaksi",
                    orderable: false,
                    class: "text-right",
                    render: (data) => {
                        return 'Rp.' + docoHelper.convertToRupiah(data)
                    }
                },
                {
                    title: "Harga Dasar Sekarang (Rp.)",
                    data: "harganetto_ygdipakai",
                    orderable: false,
                    class: "text-right",
                    render: (data) => {
                        return 'Rp.' + docoHelper.convertToRupiah(data)
                    }
                },
                {
                    title: "Harga Dasar yang Disarankan (Rp.)",
                    data: "harga_sugesstion",
                    orderable: false,
                    class: "text-right",
                    render: (data) => {
                        return 'Rp.' + docoHelper.convertToRupiah(data)
                    }
                },
            ]
        });
});


/**
 * Global function for update base price.
 */
function updateHarga() {
    var dataPost = JSON.stringify(list_update_harga);
    $().docoForm("click",{
        type: "POST",
        url: "/apotek/inf-produksi-obat/update-harga?id+="+id+"&trace=1",
        data: {
            toPost: dataPost
        },
        success: function(data){
            new PNotify({
                title: data.data.title,
                text: data.data.text,
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: 'success'
            });
            $(".close-modal-pemeriksaan").trigger("click");
            window.location.replace('/apotek/inf-produksi-obat/index-produksi');
        },
    });
}

</script>