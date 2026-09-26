<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-14 13:58:13
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-09-07 11:01:49
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Farmasi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                                'id' => 'simpan-penerimaan'
                            ],
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'penerimaan-form',
                        'enableAjaxValidation'=>false,
                        'enableClientValidation'=>false,
                        'action' => '/gudang/penerimaan-obat-supplier/set-list-item',
                        'formConfig' => [
                            'options' => [
                                'tag' => false
                            ]
                        ],
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                    ]);

                    echo Html::hiddenInput('isDonasiHeader', false, ['class' => 'isDonasiHeader']);
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><?= $this->title ?></h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'is_donasi')->checkbox(); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'supplier_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'supplierName',
                                ])->label(Yii::t('fe', 'Nama Supplier')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'no_faktur', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Nomor Faktur'),
                                    'class' => 'form-control input-sm',
                                    'autocomplete' => "off",
                                ])->label(Yii::t('fe', 'Nomor Faktur')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'tgl_penerimaan', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label',
                                        'wrapper' => ''
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Tanggal Penerimaan'),
                                    'class' => 'form-control input-sm',
                                    'readOnly' => true,
                                    'value' => date('d-M-Y'),
                                    'tab-index' => 0,
                                ])->label(Yii::t('fe', 'Tanggal Penerimaan')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'pajak_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['ppn'],'pajak_id','pajak_label'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Tarif Pajak')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'payterm_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['payterm'],'payterm_id','payterm_nama'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Payment Term')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'no_suratjalan', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'No. Surat Jalan'),
                                    'class' => 'form-control input-sm',
                                    'autocomplete' => "off",
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'peg_mengetahui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'peg_mengetahui',
                                ])->label(Yii::t('fe', 'Pegawai Mengetahui')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'sumber_penerimaan',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['sumber_penerimaan'],'lookup_id','lookup_name'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Sumber Penerimaan')); ?>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4">
                              <?= $form->field($model, 'peg_menyetujui',[
                              'horizontalCssClasses' => [
                                      'label' => 'text-left control-label col-sm-4',
                                      'wrapper' => 'col-md-8'
                                  ],
                              ])->dropDownList([],[
                                  'class' => '',
                                  'id' => 'peg_menyetujui',
                              ])->label(Yii::t('fe', 'Pegawai Menyetujui')); ?>
                          </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                <hr>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Detail Penerimaan Barang Manual</h6>
                    </div>
                    <div class="panel-body">
                        <div class="row form-detail">
                            <form action="#" id="detailFormTemp">
                                <div class="row">
                                    <div class="col-sm-4 form-wrapper">
                                        <div class="form-group highlight-addon field-barangInputElement required">
                                            <label class="control-label has-star" for="barangInputElement">Nama Barang</label>
                                            <select id="barangInputElement" class="form-control select2-hidden-accessible" name="PenerimaanSupplierDetailForm[barang_id]" tabindex="-1" aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'satuankonversi_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->widget(DepDrop::classname(), [
                                                'options' => ['id' => 'satuankonversi_id',
                                                    'class' => 'form-control select-satuan'],
                                                'pluginOptions'=>[
                                                    'depends' => ['obatalkes_id'],
                                                    'placeholder' => Yii::t('fe', 'Satuan'),
                                                    'url'=> Url::to(['/gudang/adjustment-obat-alkes/get-satuan-konversi']),
                                                    'prompt' => Yii::t('fe', 'Pilih Satuan'),
                                                ]
                                            ])->label(Yii::t('fe', 'Satuan'));
                                        ?>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'qty_besar', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty Penerimaan'),
                                            'class' => 'form-control input-sm doco-number text-right',
                                            'autocomplete' => "off",
                                        ])->label(Yii::t('fe', 'Qty Penerimaan')); ?>
                                        <span class="text-info text-semibold" id="infoKonversiSection" style="margin-bottom: 0px!important;margin-top: 0px!important;">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i> 
                                            <u><i id="totalKonversiSection"></i></u>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 form-wrapper">
                                        <div class="form-group">
                                            <label class="control-label">Tanggal Kadaluarsa</label>
                                            <div class="input-group inline-datepicker" id="expiredDateWrapper">
                                                <input type="text" id="expiredDate" class="form-control" name="PenerimaanSupplierDetailForm[tgl_kadaluarsa]" data-mask="99-99-9999">
                                                <span class="input-group-addon" id="expiredDateBtn"><i class="fa fa-calendar"></i></span>
                                                <span class="input-group-addon" id="expiredDateClearBtn"><i class="fa fa-remove"></i></span>
                                            </div>
                                            <span class="text-info text-semibold" id="infoKadaluarsaWrapper" style="margin-bottom: 0px!important;margin-top: 0px!important;">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i> 
                                                <u><i>Barang ini tidak ada tanggal kadaluarsa</i></u>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'harga_netto', [
                                            'addon' => ['prepend' => ['content'=>'Rp.']],
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Harga Netto (Satuan Besar)'),
                                            'class' => 'form-control input-sm doco-number text-right total_harga',
                                            'autocomplete' => "off",
                                            'value' => 0
                                        ])->label(Yii::t('fe', 'Harga Total Netto')); ?>
                                        <span class="help-block text-info info-harga text-semibold" style="margin-bottom: 0px!important;margin-top: 0px!important;">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            <u><i class="harga_netto"></i></u>
                                        </span>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'diskon', [
                                        'addon' => ['append' => ['content'=>'%']],
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Diskon'),
                                            'class' => 'form-control input-sm text-right doco-number-wcomma',
                                            'autocomplete' => "off",
                                            'value' => 0
                                        ]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'no_batch', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'No. Batch'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off",
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">
                                        <?= $form->field($modelDetail, 'keterangan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textarea([
                                            'class' => 'form-control input-sm',
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4 form-wrapper">&nbsp;</div>
                                </div>
                            </form>
                            <div class="col-md-12">
                                <button type="button" id="btnAddDetail" class="btn btn-success btn-labeled btn-xs pull-right"><b><i class="fa fa-plus"></i></b>Tambah</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <form action="#" id="detailForm">
                                    <table id="tablePenerimaanTmp" class="table table-striped table-condensed table-hover" style="width:100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th width="1">No</th>
                                                <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                                <th><?=\Yii::t("fe", "Qty Penerimaan");?></th>
                                                <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                                                <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                                <th><?=\Yii::t("fe", "Harga Netto");?></th>
                                                <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                                                <th><?=\Yii::t("fe", "No Batch");?></th>
                                                <th><?=\Yii::t("fe", "Keterangan");?></th>
                                                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="10" class="text-center">Mohon inputkan data.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("let harga_donasi = '".$harga_donasi."'".$this->render('js/index.js'), View::POS_END);
?>