<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 10:22:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Data Pasien')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <?php $form = ActiveForm::begin([
            'id' => 'form-reseptur', 
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableClientValidation' => false,
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
        ]) ?>
        <?= Html::activeHiddenInput($modelReseptur, 'reseptur_id') ?>
        <?= Html::activeHiddenInput($modelReseptur, 'pasienadmisi_id') ?>
        <?= Html::hiddenInput('ruangan_id', $modelReseptur->ruangan_id, ['readonly' => 'readonly']) ?>

        <div class="panel-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'is_hamil')->radioList(
                            [1 => Yii::t('fe', 'Ya'), 0 => Yii::t('fe', 'Tidak')],
                            ['inline'=>true]
                        )->label(Yii::t('fe', 'Hamil')) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'berat_badan', [
                            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Kg</span></div>{error}{hint}'
                        ])->textInput(['class' => 'form-control input-sm doco-decimal-wcomma bb_tb', 'id' => 'berat_badan']) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'tinggi_badan', [
                            'inputOptions' => ['class' => 'form-control input-sm doco-decimal-wcomma bb_tb'],
                            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Cm</span></div>{error}{hint}'
                        ])->textInput(['class' => 'form-control input-sm doco-decimal-wcomma bb_tb', 'id' => 'tinggi_badan']) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'luas_tubuh')->textInput([
                            'id' => 'luas_tubuh',
                            'class' => 'form-control input-sm doco-decimal-wcomma',
                        ])->label(Yii::t('fe', 'Luas Permukaan Tubuh')) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                        <?=Html::label(Yii::t('fe', 'Diagnosa'), '', [
                                'class' => 'control-label has-star'
                            ]);?>
                                <?= Html::textInput(
                            'diagnosa_id', 
                            isset($diagnosa_nama) ? $diagnosa_nama : '', [
                                'class' => 'form-control input-sm',
                                'id' => 'diagnosa_id',
                                'readonly' => 'readonly',
                            ]) ?>
                        <?= Html::activeHiddenInput($modelReseptur, 'diagnosa_id') ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group required">
                            <?=Html::label(Yii::t('fe', 'Dokter'), '', [
                                'class' => 'control-label has-star'
                            ]);?>
                            <?= Html::textInput(
                                'nama_dokter', 
                                isset($pegawai['nama_pegawai']) ? $pegawai['nama_pegawai'] : '', [
                                    'class' => 'form-control input-sm',
                                    'id' => 'nama_dokter_cppt',
                                    'readonly' => 'readonly',
                                ]) ?>
                            <?= Html::activeHiddenInput($modelReseptur, 'pegawai_id') ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'tglreseptur')->textInput([
                            'class' => 'form-control input-sm date',
                            'disabled' => 'disabled',
                        ])->label(Yii::t('fe', 'Tanggal Resep')) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'ruangan_id')->widget(Select2::classname(), [
                            'data' => $listDataApotek,
                            'options' => [
                                'id' => 'select_depo',
                                'class' => 'form-control input-sm select2',
                                'placeholder' => '-- Pilih Depo --',
                                'disabled' => $isEditReseptur == true ? 'disabled' : false
                            ],
                        ])->label(Yii::t('fe', 'Depo Tujuan')) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($modelReseptur, 'iter')->textInput([
                            'id' => 'reseptur_iter',
                            'class' => 'form-control input-sm docoNumberOnly',
                            'disabled' => $isEditReseptur == true ? 'disabled' : false,
                        ])->label(Yii::t('fe', 'Iterasi')) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

