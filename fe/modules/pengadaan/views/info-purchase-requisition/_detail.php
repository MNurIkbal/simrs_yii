<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Detail Obat</h6>
    </div>
    <div class="panel-body">
      <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-condensed table-hover"
                id="detail-pr-<?= $id ?>" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "UOM");?></th>
                            <th><?=\Yii::t("fe", "DOI");?></th>
                            <th><?=\Yii::t("fe", "SSmin");?></th>
                            <th><?=\Yii::t("fe", "Stok Gudang");?></th>
                            <th><?=\Yii::t("fe", "Stok Farmasi");?></th>
                            <th><?=\Yii::t("fe", "Stok R.Lain");?></th>
                            <th><?=\Yii::t("fe", "Qty Sugesstion");?></th>
                            <th><?=\Yii::t("fe", "Qty PR");?></th>
                            <th><?=\Yii::t("fe", "Qty Final");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Alasan Batal");?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="15"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var id = "'.$id.'";
    var type = "'.$type.'";
    var isVisible = type == "obat" ? true : false;

    $(document).ready(function() {
        var tableDetail;

        tableDetail = $("#detail-pr-"+id).docoTabel({
            filter: false,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"pengadaan/info-purchase-requisition/list-detail?type="+type+"&id="+id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Item")).'",
                    searchable: false,
                    orderable: false,
                    data: "item_kode"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Item")).'",
                    searchable: false,
                    orderable: false,
                    data: "item_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "UOM")).'",
                    searchable: false,
                    orderable: false,
                    data: "uom"
                },
                {
                    title: "'.(\Yii::t("fe", "Doi")).'",
                    searchable: false,
                    orderable: false,
                    data: "doi"
                },
                {
                    title: "'.(\Yii::t("fe", "SSmin")).'",
                    searchable: false,
                    orderable: false,
                    data: "ssmin"
                },
                {
                    title: "'.(\Yii::t("fe", "Stok Gudang")).'",
                    searchable: false,
                    orderable: false,
                    data: "stok_gudang",
                },
                {
                    title: "'.(\Yii::t("fe", "Stok Farmasi")).'",
                    searchable: false,
                    orderable: false,
                    data: "stok_farmasi",
                    visible: isVisible,
                },
                {
                    title: "'.(\Yii::t("fe", "Stok R.Lain")).'",
                    searchable: false,
                    orderable: false,
                    data: "stok_ruanganlain"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Suggestion")).'",
                    searchable: false,
                    orderable: false,
                    data: "qty_sugesstion"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty PR")).'",
                    searchable: false,
                    orderable: false,
                    data: "qty_pr"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Final")).'",
                    searchable: false,
                    orderable: false,
                    data: "qty_input"
                },
                {
                    title: "'.(\Yii::t("fe", "Catatan")).'",
                    searchable: false,
                    orderable: false,
                    data: "catatan"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    searchable: false,
                    orderable: false,
                    data: "status"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor PO")).'",
                    searchable: false,
                    orderable: false,
                    data: "nomor_po"
                },
                {
                    title: "'.(\Yii::t("fe", "Alasan Batal")).'",
                    searchable: false,
                    orderable: false,
                    data: "alasan"
                },
            ],
            drawCallback: function(e) {
                table.columns.adjust();
            }
        });
    });
    ',VIEW::POS_END);
?>
