<?php

/**
 * @Author: Ardi
 * @Date:   2024-05-21 01:10
 */

use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .modal-body {
        position: relative;
        padding-top: 5px !important;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= \Yii::t("fe", "Hasil Pemeriksaan") ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="table-responsive">
            <table id="tabel-riwayat-hasil-usg" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Dokter Pemeriksa') ?></th>
                        <th><?= Yii::t('fe', 'Tindakan Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Hasil') ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var getRiwayatUsgParams = {
        "id": "' . $id . '",
        "instalasi_id": "' . (isset($instalasi_id) ? $instalasi_id : "") . '"
    };

    var table_riwayat_hasil_usg = $("#tabel-riwayat-hasil-usg").docoTabel({
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        ajax: baseUrl+"api/usg/get-riwayat-usg?"+new URLSearchParams(getRiwayatUsgParams).toString()+"&mode=list",
        columns:[
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "' . (\Yii::t("fe", "Tanggal Pemeriksaan")) . '", data: "tgl_pemeriksaan"},
            {title: "' . (\Yii::t("fe", "Dokter Pemeriksa")) . '", data: "dokter"},
            {title: "' . (\Yii::t("fe", "Tindakan Pemeriksaan")) . '", data: "tindakan"},
            {title: "' . (\Yii::t("fe", "Hasil")) . '", data: "hasilusg", searchable: false, orderable: false},
        ]
    });
');
?>