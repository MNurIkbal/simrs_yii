<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-14 13:58:13
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-10-25 16:35:29
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .btn-custom {
        padding : 3.5px 12px!important;
    }
    .consignment{
        background-color: #FDB7B7;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-simpan'
                            ],
                        ],
                        // 'reset'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><?= $this->title ?></h6>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'po-form',
                                'enableAjaxValidation'=>false,
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                                'options' => [
                                    'skip-confirm' => "true"
                                ]
                        ]);
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_pomanual', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Otomatis'),
                                    'class' => 'form-control input-sm',
                                    'readonly' => true,
                                ])->label(Yii::t('fe', 'Nomor PO')); ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $model->tgl_pomanual = !empty($model->tgl_pomanual)
                                        ? date('d-M-Y',strtotime($model->tgl_pomanual)) : date('d-M-Y');
                                ?>
                                <?= $form->field($model, 'tgl_pomanual', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Tanggal PO'),
                                    'class' => 'form-control input-sm',
                                    'id' => 'tgl_pomanual',
                                    'readOnly' => true,
                                    'value' => date('d-M-Y'),
                                ])->label(Yii::t('fe', 'Tanggal PO')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'instalasi_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList($request['instalasi'],[
                                    'class' => 'form-control select2',
                                    'id' => 'instalasi_id',
                                    'prompt' => Yii::t('fe', 'Pilih Instalasi'),
                                ])->label(Yii::t('fe', 'Instalasi')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'ruangan_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->widget(DepDrop::classname(), [
                                        'options' => ['id'=>'ruangan_id',
                                         'class' => 'form-control select2'],
                                        'pluginOptions'=>[
                                            'depends' => ['instalasi_id'],
                                            'placeholder' => Yii::t('fe', '-- Pilih Ruangan --'),
                                            'url'=> Url::to(['/pengadaan/purchase-order-manual/get-ruangan']),
                                            'prompt' => Yii::t('fe', '-- Pilih Ruangan --'),
                                        ]
                                    ])->label(Yii::t('fe', 'Ruangan'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'supplier_id',[
                                    'addon' => [
                                        'append' => [
                                            'content' => Html::button('<i class="fa fa-plus"></i>', [
                                                'class'=>'btn btn-info btn-custom',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Tambah Supplier',
                                                'id' => 'add-supplier'
                                            ]),
                                            'asButton' => true
                                        ]
                                    ],
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'form-control selectSupplier select2',
                                    'prompt' => Yii::t('fe', 'Pilih Supplier'),
                                    'id' => 'supplier_id',
                                ])->label(Yii::t('fe', 'Supplier')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'diorder_oleh', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->textInput([
                                    'class' => 'form-control input-sm',
                                    'readonly' => true,
                                    'value' => $request['pegawaiLogin']
                                ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $model->tgl_rencanaterima = date('d-M-Y'); ?>
                                <?= $form->field($model, 'tgl_rencanaterima', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->widget(DatePicker::classname(), [
                                    'value' => date('Y-m-d'),
                                    'readonly' => true,
                                    'language' => 'en',
                                    'pluginOptions' => [
                                        'startDate' => '0d',
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                    ]
                                ])->label(Yii::t('fe', 'Tanggal Rencana Terima')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'peg_menyetujui_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'form-control selectPegawai select2',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai Menyetujui'),
                                    'id' => 'peg_menyetujui_id',
                                ])->label(Yii::t('fe', 'Menyetujui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'payterm_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList($request['payterm'],[
                                    'class' => 'form-control select2',
                                    'prompt' => Yii::t('fe', 'Pilih Payment Term'),
                                    'id' => 'payterm_id',
                                ])->label(Yii::t('fe', 'Payment Term')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'peg_mengetahui_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'form-control selectPegawai select2',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai Mengetahui'),
                                    'id' => 'peg_mengetahui_id',
                                ])->label(Yii::t('fe', 'Mengetahui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'pajak_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList($request['pajak'],[
                                    'class' => 'form-control select2',
                                    'prompt' => Yii::t('fe', 'Pilih Tarif Pajak'),
                                    'id' => 'pajak_id',
                                ])->label(Yii::t('fe', 'Tarif Pajak')); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group checkbox-consignment">
                                    <label for="is_active" class="col-sm-2 control-label">
                                        <?= Yii::t('fe', 'Consignment'); ?>
                                    </label>
                                    <div class="col-md-8">
                                        <?= $form->field($model, 'is_consigment')->checkbox()->label(false); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="form-section">Data Barang/Obat</h3>
                        <div class="row kode-warna">
                            <div class="col-md-12">
                                <div class="legend-index">
                                    <div class="legend-header">Keterangan</div>
                                    <div class="legend-wrapper">
                                        <div class="legend-information">
                                            <div class="legend-information__color consignment"></div>
                                            <div class="legend-information__text">Item Consignment</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="po" class="table table-condensed">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="" width="15%">
                                            <?=\Yii::t("fe", "Nama Obat");?></th>
                                            <th class="text-center" width="11%"><?=\Yii::t("fe", "Qty");?></th>
                                            <th class="text-center" width="15%"><?=\Yii::t("fe", "Satuan");?></th>
                                            <th class="text-center" width="10%"><?=\Yii::t("fe", "Konversi");?></th>
                                            <th class="text-center" width="15%"><?=\Yii::t("fe", "Harga Satuan");?></th>
                                            <th class="text-center" width="15%"><?=\Yii::t("fe", "Discount (%)");?></th>
                                            <th class="text-center" width="20%"><?=\Yii::t("fe", "Discount (Rp)");?></th>
                                            <th class="text-center"><?=\Yii::t("fe", "Jumlah");?></th>
                                            <th class="text-center"><?=\Yii::t("fe", "Aksi");?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="container">
                                        <tr id="tr-default" data-row="0" data-last="0">
                                           <td colspan="8" class="text-center">
                                           </td>
                                           <td class="" style="height: 50px!important;">
                                                <button type="button" class="addrow btn btn-info btn-custom">
                                                    <span class="fa fa-plus"></span>
                                                </button>
                                           </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="8" class="text-right"><h5>Sub Total</h5></th>
                                            <th colspan="2"><h5><span class="sub_total"></span></h5></th>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right"><h5> Total Diskon </h5></th>
                                            <th colspan="2"><h5><span class="total_diskon"></span></h5></th>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right"><h5> PPN(%) </h5></th>
                                            <th colspan="2"><h5><span class="ppn"></span></h5></th>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right"><h5> Total </h5></th>
                                            <th colspan="2"><h5><span class="grand_total"></span></h5></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'catatan1',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->textArea([
                                    'rows' => 5,
                                ])->label(Yii::t('fe', 'Catatan Internal')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'catatan2',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->textArea([
                                    'rows' => 5,
                                ])->label(Yii::t('fe', 'Catatan Eksternal')); ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var dataUser = '.json_encode($dataUser).'
    var _mapPajak = '. json_encode($request['mapValue']) .';
');
$this->registerJs($this->render('js/index.js'));
?>
