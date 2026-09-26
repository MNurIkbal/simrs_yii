<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-25 14:40:38
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket"></div>
                </div>
                <table id="tabel-detail-kegiatan-<?= $id_encrypt ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
var tabel;
var id_enkrip = "<?= $id_encrypt ?>";

$(document).ready(function() {
    var emptyTable = '<?= (\Yii::t("fe", "Tidak ada data yang tersedia")) ?>';
    var info = '<?= (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) ?>';
    var infoEmpty = '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) ?>';
    var infoFiltered = '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)")) ?>';
    var lengthMenu = '<?= (\Yii::t("fe", "Menampilkan _MENU_ data")) ?>';
    var loadingRecords = '<?= (\Yii::t("fe", "Memuat...")) ?>';
    var processing = '<?= (\Yii::t("fe", "Memproses...")) ?>';
    var search = '<?= (\Yii::t("fe", "Cari:")) ?>';
    var zeroRecords = '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan")) ?>';
    var sortAscending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) ?>';
    var sortDescending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) ?>';

    tabelDetailTindakan = $("#tabel-detail-kegiatan-"+id_enkrip).docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax:'/master/tindakan/get-data-detail-kegiatan?id='+id_enkrip,
        columns: [
            {title: "No", data:"rowNum", searchable: false, orderable: false},
            {title: "Tindakan", data: "daftartindakan_nama", searchable: false}
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
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        }
    });
});
</script>