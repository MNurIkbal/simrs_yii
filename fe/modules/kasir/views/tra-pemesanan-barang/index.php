<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Transaksi Pemesanan Barang');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
            

                <fieldset class="scheduler-border">
                    <legend class="text-bold"><?=Yii::t('fe', 'Data Pemesanan') ?></legend>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?=Yii::t('fe', 'Total closing') ?></label>
                                <?=Html::dropDownList('instalasi_nama', '', 
                                    ArrayHelper::map($instalasi, 'instalasi_nama', 'instalasi_nama'), 
                                    [
                                        'id' => 'filter_instalasi', 
                                        'class' => 'form-control no-select2', 
                                        'prompt' => \Yii::t('fe', 'Instalasi akhir')
                                    ]
                                );?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?=Yii::t('fe', 'Total closing') ?></label>
                                <?=DepDrop::widget([
                                    'name' => 'ruangan_nama',
                                    'options' => [
                                        'disabled' => false,
                                        'class' => 'form-control no-select2'
                                    ],
                                    'pluginOptions' => [
                                       'depends'  => ['filter_instalasi'],
                                       '-placeholder' => \Yii::t('fe', 'Ruangan akhir'),
                                       'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan'
                                    ]
                                ]);?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?=Yii::t('fe', 'Total closing') ?></label>
                                <div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-single" value="" placeholder="<?=(\Yii::t('fe', 'Periode Stok'));?>"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?=Yii::t('fe', 'Total closing') ?></label>
                                <div class="input-group"><?=(preg_replace("/[\n\t\r]/i", '', 
                                    Select2::widget([
                                        'name' => 'barang_nama',
                                        'options' => ['-placeholder' => \Yii::t('fe', 'Nama barang'), 'class' => 'barang_nama'],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 3,
                                            'language' => [
                                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                            ],
                                            'ajax' => [
                                                'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-data-barang?assign_id=',
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                            ],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                        ],
                                    ])
                                    // Html::dropDownList('barang_nama', '', array(), 
                                        // [
                                            // 'class' => 'form-control select2 barang_nama', 
                                            // 'prompt' => \Yii::t('fe', 'Nama barang'), 
                                        // ]
                                    // )
                                ));?><span class="input-group-addon"><span class="cursor-pointer" action="<?=Url::home().(Yii::$app->controller->module->id);?>/modal-search/modal-barang" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?=Yii::t('fe', 'Qty') ?></label>
                                <input type="number" class="form-control daterange-single" value="" placeholder="<?=(\Yii::t('fe', 'Qty'));?>">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br />

                <fieldset id="formLieur" class="scheduler-border" -style="display:none;">
                    <?php $form = ActiveForm::begin([
                        'id' => 'ajax-form',
                        'action' => 'tra-pemesanan-barang/ajax-form',
                        'options' => ['class' => 'form-horizontal'],
                    ]) ?>
                    <legend class="scheduler-border"><?=\Yii::t('fe', 'Tabel Pemesanan');?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Nama barang");?></th>
                                <th><?=\Yii::t("fe", "Qty");?></th>
                                <th><?=\Yii::t("fe", "Satuan");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe', 'Simpan').'', [
                        'class' => 'btn btn-info btn-xs',
                    ]);?>
                    <?=Html::resetButton('<i class="fa fa-repeat"></i> '.\Yii::t('fe', 'Ulang').'', [
                        'class' => 'btn btn-info btn-xs',
                    ]);?>
                    <?=Html::button('<i class="fa fa-print"></i> '.\Yii::t('fe', 'Cetak').'', [
                        'class' => 'btn btn-info btn-xs',
                    ]);?>
                    <?=Html::button('<i class="fa fa-list"></i> '.\Yii::t('fe', 'Petunjuk').'', [
                        'class' => 'btn btn-info btn-xs',
                    ]);?>
                    <?php ActiveForm::end(); ?>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
    });
', View::POS_END, 'b-index');
?>
