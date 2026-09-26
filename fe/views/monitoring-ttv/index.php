<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DatePicker;

?>

<style>
    .dataTables_scroll {
        height: 100% !important;
        max-height: 100%;
    }

    .select2-selection__clear:after {
        content: '' !important;
    }

    .range_custom{
		height:20px;
		padding:13px 12px;
	}
    .select2-selection__clear{
        position: absolute;
        top: 0;
        left: -10px;
        height: 100%;
        padding: 0 12px;
        display: inline-block;
    }

    .btnSearch,
    .btnClear{
        display: inline-block;
        vertical-align: top;
    }


</style>

<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title text-bold"><?= Yii::t('fe', $title) ?></h6>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'search' => [
                    'attributes' => [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                        'data-table-id' => 'example',
                        'data-options' => 'click',
                    ]
                ],
                'reset' => [
                    'attributes' => [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                        'data-table-id' => 'example',
                        'data-options' => 'click',
                    ]
                ],
                'add' => [
                    'title' => 'Input TTV',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'input-ttv',
                        'data-width' => '75%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => '/'.$modul.'/'.$url.'/input-ttv?pendaftaran_id=' . $pendaftaranId,
                    ],
                ],
                'pdf' => [
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-cetak-ttv',
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="panel-body">
            <div class="col-md-12" style="width: 100%;">
                <div class="table-wrapper table-scroll-x">
                    <table id="example" class="table datatable-basic table-striped table-hover dataTable no-footer" width="100%">
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
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="16" style="text-align:center;">Data Tidak Ditemukan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var pendaftaran_id = '" . $pendaftaranId . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var pendaftaranIdDecrypt = '".$pendaftaranIdDecrypt."';
        var sumberTtv = '".json_encode($sumber)."';
        var sumberTtvEws = '" . DocoConstants::SUMBER_EWS_MONITORING_ID . "';
        var sumberTtvSbar = '" . DocoConstants::SUMBER_SBAR_MONITORING_ID . "';
    ", View::POS_END);
    $this->registerJs($this->render('index.js'), View::POS_END);
?>
