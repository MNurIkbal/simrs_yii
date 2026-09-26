<?php

/**
 * @Author: Budi
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
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
                    'tambah-gabung' => [
                        'title' => \Yii::t('fe', 'Gabung No. Invoice'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'id' => 'tambah-gabung',
                            'data-popup'=>'tooltip',
                            'data-toggle'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'action' => '/kasir/inf-gabung-invoice/tambah-invoice',
                            'data-width' => "85%",
                        ]
                    ],
                    'cetak-kwitansi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Kwitansi'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'cetak-kwitansi',
                            'data-options'=>'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/inf-gabung-invoice/cetak-kwitansi?id=',
                        ]
                    ],
                    'invoice' => $btnInvoiceGabung,
                    'detailinvoice' => $btnDetailInvoiceGabung,
                    'batal-invoice' => [
                        'type' => 'button',
                        'title' => 'Batal Invoice',
                        'icon' => 'fa fa-times-circle-o',
                        'method' => '#',
                        'attributes' => [
                            'id'=>'batal-invoice',
                            'class'=>'data-batal-invoice',
                            'data-options' => 'click',
                            'data-additional' => 'data-rm',
                            'data-target' => '/kasir/inf-gabung-invoice/batal-invoice',
                        ]
                    ],
                    'detail-invoice' => [
                        'type' => 'button',
                        'title' => 'Detail',
                        'icon' => 'fa fa-search',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'detail-invoice',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '85%',
                            'data-url' => '/kasir/inf-gabung-invoice/detail-invoice?id=',
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <table id="example" class="table datatable-basic table-hover dataTable" style="width: 100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="1">No</th>
                                <th><?= \Yii::t("fe", "No. Invoice"); ?></th>
                                <th><?= \Yii::t("fe", "Nama Pasien / No. RM"); ?></th>
                                <th><?= \Yii::t("fe", "No. Pendaftaran"); ?></th>
                                <th><?= \Yii::t("fe", "Tanggal Invoice"); ?></th>
                                <th><?= \Yii::t("fe", "Tanggal Gabung Invoice "); ?></th>
                                <th><?= \Yii::t("fe", "Penjamin"); ?></th>
                                <th><?= \Yii::t("fe", "Total Invoice"); ?></th>
                                <th><?= \Yii::t("fe", "Status"); ?></th>
                                <th><?= \Yii::t("fe", "Ref. Pendaftaran"); ?></th>
                                <th><?= \Yii::t("fe", "Ref. Invoice"); ?></th>
                                <th><?= \Yii::t("fe", "Ref. Penjamin"); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
',View::POS_END,'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END); 
?>