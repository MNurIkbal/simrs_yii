<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\View;

// Yii::error(ArrayHelper::map($list_data_signa, 'signa_id', 'signa_nama'));
?>
<style>
    .checker {
        position: static !important;
        top: auto !important;
        left: auto !important;
    }
</style>

<div class="row panel panel-default" id="non-racikan">
    <a id="info-heading" data-toggle="collapse" href="#non-racikan-colapse" role="button" aria-expanded="true" aria-controls="non-racikan-colapse" class="">
        <div class="panel-heading flex-container">
            <h5 class="panel-title"><?= Yii::t('fe', 'Non Racikan') ?></h5>
            <ul class="icons-list" style="padding-top: 3px">
                <li><i id="chevron" class="fa fa-chevron-up"></i></li>
            </ul>
        </div>
    </a>

    <div class="panel-body multi-collapse label-information collapse" id="non-racikan-colapse" aria-expanded="true" style="">
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-nonracikan',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'enableClientValidation' => false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[stok_tersedia]', '', ['id' => 'stok_tersedia']) ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_id]', null, ['id' => 'satuandefault_id']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_nama]', null, ['id' => 'satuandefault_nama']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[satuankecil_nama]', null, ['id' => 'satuankecil_nama']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[nilai_konversi]', null, ['id' => 'nilai_konversi']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[harga]', null, ['id' => 'harga']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[harga_konversi]', null, ['id' => 'harga_konversi']); ?>
                <?= Html::hiddenInput('ResepturNrDetailForm[harganetto_reseptur]', null, ['id' => 'harganetto_reseptur']); ?>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3 select2-md">
                            <?= $form->field($modelResepturDetailNonRacikan, 'obatalkes_id', [
                                'labelOptions' => ['class' => 'text-right required-reseptur'],
                            ])->dropDownList([], [
                                'class' => 'form-control autoObat obatalkes select2-selffocus',
                                'id' => 'obatalkes_id',
                                'prompt' => '-'
                            ]) ?>
                        </div>
                        <div class="col-md-3" style="display:none">
                            <?= $form->field($modelResepturDetailNonRacikan, 'hargasatuan_reseptur', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'harga_reseptur',
                                    'class' => 'form-control input-sm',
                                    'readonly' => 'readonly',
                                ]); ?>
                        </div>
                        <div class="col-md-4 select2-md">
                            <?= $form->field($modelResepturDetailNonRacikan, 'signa_reseptur', [
                                'labelOptions' => ['class' => 'text-right required-reseptur'],
                            ])->dropDownList([], [
                                'class' => 'form-control select2-selffocus docoSelect2SignaFormatOnly',
                                'id' => 'signa_reseptur',
                                'prompt' => '-'
                            ]) ?>

                        </div>
                        <div class="col-md-3" id="div_stok_tersedia">
                            <div class="row">
                                <div class="col-md-5">
                                    <p>
                                        Stok Tersedia Per <span id="satuan_terpilih"></span>
                                    </p>
                                </div>
                                <div class="col-md-7">
                                    <strong><span id="text_stok_tersedia"></span></strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <p>
                                        Nilai Konversi
                                    </p>
                                </div>
                                <div class="col-md-7">
                                    <strong><span id="text_konversi"></span></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 select2-md">
                            <?= $form->field($modelResepturDetailNonRacikan, 'jumlah_hari', [
                                'labelOptions' => [
                                    'class' => 'text-right'
                                ],
                            ])->widget(Select2::classname(), [
                                'data' => array_combine(range(1, 31), range(1, 31)),
                                'size' => Select2::LARGE,
                                'options' => [
                                    'id' => 'jumlah_hari',
                                    'placeholder' => 1,
                                    'class' => 'select2-selffocus'
                                ],
                                'pluginOptions' => [
                                    'tokenSeparators' => [',', '_'],
                                    'maximumInputLength' => 2,
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <?= $form->field($modelResepturDetailNonRacikan, 'qty_reseptur', [
                                'labelOptions' => ['class' => 'text-right required-reseptur', 'id' => 'label_qty'],
                                'addon' => ['append' => ['content' => '<span class="konversi"></span>']],
                            ])->textInput([
                                    'id' => 'qty_reseptur',
                                    'class' => 'form-control input-sm doco-decimal',
                                ])->label(Yii::t('fe', 'Qty')); ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($modelResepturDetailNonRacikan, 'is_kronis', [])->checkbox([
                                'class' => 'form-control is-kronis-nr'
                            ])->label(Yii::t('fe', 'Obat Kronis')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <?= $form->field($modelResepturDetailNonRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right', 'id' => 'label_catatan']])
                                ->textArea([
                                    'cols' => 5,
                                    'rows' => 5,
                                    'id' => 'etiket',
                                    'class' => 'form-control input-sm',
                                ])->label(Yii::t('fe', 'Catatan')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 select2-md" style="display:none">
                            <div class="form-group highlight-addon has-size-sm field-satuankecil_id required">
                                <label class="text-right has-star">Satuan</label>
                                <?= DepDrop::widget([
                                    'name' => 'ResepturNrDetailForm[satuankecil_id]',
                                    'options' => [
                                        'id' => 'satuankecil_id',
                                        'class' => 'form-control select2'
                                    ],
                                    'pluginOptions' => [
                                        'depends' => ['obatalkes_id'],
                                        'placeholder' => \Yii::t('fe', '--Pilih Satuan--'),
                                        'url' => Url::to(['/rajal/allow/list-satuan-besar'])
                                    ],
                                    'pluginEvents' => [
                                        'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                        var _id = $(this).val();
                                        var _response = $(\'#satuankecil_id\').depdrop(\'getAjaxResults\');

                                        _group = {};
                                        $.each(_response.output, function (x,y) {
                                            _group[y.id] = y.konversi;
                                        });

                                        var _nilai_konversi = _group[_id];
                                        $("#nilai_konversi").val(_nilai_konversi);
                                    }'
                                    ]
                                ]); ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="pull-right" style="margin-right:5px">
                        <button type='button' id="btn-tambah-non-racikan" class="btn btn-labeled btn-info btn-xs save-non-racikan" disabled="true">
                            <b><i class='fa fa-plus'></i></b> Tambah
                        </button>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_signa.js'), View::POS_HEAD);
$this->registerJs($this->render('js/_non_racikan.js'), View::POS_END);
?>