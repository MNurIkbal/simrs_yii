<?php

use yii\helpers\Html;
use yii\web\View;
?>
<style>
    table.datatable.detail-rsp {
        border-bottom: 0 !important
    }
    #table-copy-ttv tbody tr:hover {
        background-color: #0096FF;
        cursor: pointer;
        color: white
    }
</style>
<div class="modal-header" style="background-color:#37474f; color: #ffffff" id="modal-history">
    <button type="button" class="close close-modal-copy-ttv" style="color: #ffffff" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Copy TTV</h5>
</div>
<div class="modal-body">
    <div class="row">
        <table class="table table-bordered table-hover" style="margin-bottom: 8px;width:100%;" id="table-copy-ttv">
            <thead>
                <tr class="bg-inverse">
                    <th><?=Yii::t('fe', 'No')?></th>
                    <th><?=Yii::t('fe', 'Transaksi')?></th>
                    <th><?=Yii::t('fe', 'Jenis TTV')?></th>
                    <th><?=Yii::t('fe', 'Kesadaran')?></th>
                    <th><?=Yii::t('fe', 'Sistol')?></th>
                    <th><?=Yii::t('fe', 'Diastol')?></th>
                    <th><?=Yii::t('fe', 'HR')?></th>
                    <th><?=Yii::t('fe', 'RR')?></th>
                    <th><?=Yii::t('fe', 'SPO2')?></th>
                    <th><?=Yii::t('fe', 'Suhu')?></th>
                    <th><?=Yii::t('fe', 'TB')?></th>
                    <th><?=Yii::t('fe', 'BB')?></th>
                    <th><?=Yii::t('fe', 'GCS E')?></th>
                    <th><?=Yii::t('fe', 'GCS V')?></th>
                    <th><?=Yii::t('fe', 'GCS M')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="15" class="text-center">Data tidak tersedia.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->registerJs("
    var pendaftaranId = '" . $pendaftaranId . "';
    var url = '" . $url . "';
    var modul = '" . $modul . "/';
", View::POS_END);
$this->registerJs($this->render('copy_ttv.js'), View::POS_END);
?>
