<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\modules\kasir\components\widget\CloseBillWidget;
use app\components\DocoConstants;

$this->title = DHtml::getTitleMenu();
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
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
                        // 'detail' =>[
                        //   'title' => \Yii::t('fe', 'Lihat'),
                        //   'icon' => 'fa fa-eye',
                        // ],
                        'payment' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Pembayaran'),
                            'icon' => 'fa fa-shopping-cart',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-payment',
                                'data-target' => Url::home() . (Yii::$app->controller->module->id) . '/inf-pasien-pulang/view?id=',
                            ]
                        ],
                        'plafon-bpjs' => [
							'title' => \Yii::t('fe', 'Plafon BPJS'),
							'icon' => 'fa fa-eye',
							'attributes' => [
								'id' => 'plafon-bpjs',
								'data-popup'=>'tooltip',
								'data-toggle'=>'modal',
								'data-target'=>'#modal_backdrop',
								'data-width' => "50%",
							]
						],
                        // 'print-edit-tagihan' => $btnEditTagihan,
                        // 'print-edit-tagihan-detail' => $btnEditTagihanDetail,
                        // 'rincian' => [
                        //     'title' => Yii::t('fe', 'Cetak Rincian'),
                        //     'icon' => 'fa fa-file-pdf-o',
                        //     'attributes' => [
                        //         'id'=>'cetak-rincian-tagihan',
                        //         'data-options' => 'link',
                        //         'class'=>'spa',
                        //         'data-target' => '/ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=',
                        //         'target'=>'_blank'
                        //     ]
                        // ],
                    ]); ?>
                </div>
            </div>

            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <div class="col-md-6">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information btn-is_stopakomodasi" data-group="keterangan" data-type="is_stopakomodasi" data-val="1">
                                    <div class="legend-information__color" style="background-color: #7efff5"></div>
                                    <div class="legend-information__text">Stop Akomodasi</div>
                                    <input type="hidden" class="filter-is_stopakomodasi" id="is_stopakomodasi" value="0">
                                </div>
                                <div class="legend-information btn-proses-persetujuan" data-group="keterangan" data-type="status_approve_id" data-val="1323">
                                    <div class="legend-information__color" style="background-color: #cce9b5"></div>
                                    <div class="legend-information__text">Proses Persetujuan</div>
                                </div>
                                <div class="legend-information btn-ditolak" data-group="keterangan" data-type="status_approve_id" data-val="1325">
                                    <div class="legend-information__color" style="background-color: #a8c6ff"></div>
                                    <div class="legend-information__text">Ditolak</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (isset($resMaster['carabayar'])) : ?>
                        <div class="col-md-6">
                            <div class='legend-index'>
                                <div class='legend-header'>Keterangan Cara Bayar</div>
                                <div class="legend-wrapper">
                                    <?php
                                    foreach ($resMaster['carabayar'] as $key => $value) { ?>
                                        <div class="legend-information" id="<?= $value['carabayar_id']; ?>" data-group="keterangan-cara-bayar" data-type="<?= $value['carabayar_id']; ?>">

                                            <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                                            <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="70">No</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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
var instalasiRanap = "'.DocoConstants::INSTALASI_ID_RI.'";
var groupBpjs = "'.DocoConstants::GROUP_BPJS.'";
const cetakRincian = "/ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=";
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>