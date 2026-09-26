<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 11:03:58
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 10:30:58
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
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                    'proses' => [
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'data-target' => Url::to(['detail', 'id' => '']),
                        ]
                    ],
                    'detail' => [
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-target' => Url::to(['detail', 'id' => '']),
                        ]
                    ],
                    'batal' => [
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'data-target' => Url::to(['batal-operasi', 'id' => ''])
                        ]
                    ],
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak Laporan Operasi'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'cetak-laporan',
                            'data-options' => 'link',
                            'target' => '_blank',
                        ]
                    ],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/bedah/informasi-pasien-operasi/show-popup?',
                            'data-conditions' => 'pendaftaran_id,instalasiasal_id'
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <div class="col-md-6">
                        <!-- <div class='legend-index'>
                            <div class='legend-header'>Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: #7efff5"></div>
                                    <div class="legend-information__text">Stop Akomodasi</div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-md-6">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan Cara Bayar</div>
                            <div class="legend-wrapper">
                                <?php foreach($legendCaraBayar as $key=>$value): ?>
                                    <div class="legend-information">
                                        <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                                        <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <table id="example" class="table datatable-basic table-striped table-hover dataTable" style="width: 100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="1">No</th>
                                <th><?= \Yii::t("fe", "Tanggal rujukan"); ?></th>
                                <th><?= \Yii::t("fe", "Detail Diagnosa"); ?></th>
                                <th><?= \Yii::t("fe", "Nomor Operasi"); ?></th>
                                <th><?= \Yii::t("fe", "Tanggal operasi"); ?></th>
                                <th><?= \Yii::t("fe", "No Pendaftaran") . ' / ' . \Yii::t("fe", "No Rekam Medik"); ?></th>
                                <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                                <th><?= \Yii::t("fe", "Instalasi") . ' / ' . \Yii::t("fe", "Ruang perujuk"); ?></th>
                                <th><?= \Yii::t("fe", "Cara Bayar / Penjamin"); ?></th>
                                <th><?= \Yii::t("fe", "Status"); ?></th>
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
const dropdownData = ' . json_encode($filters) . ';
const flashMessage = "' . Yii::$app->session->getFlash('bedah-used-page') . '";
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END); ?>