<?php

use yii\helpers\Html;
use yii\web\View;

$classFormNumber = 'form-control doco-number';
$classForm = 'form-control input-sm';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">D. Data Obyektif</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;">1. Keadaan Umum </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesadaran')->checkboxList(
                            [
                                'Composmentis' => 'Composmentis',
                                'Apatis' => 'Apatis',
                                'Soporocoma' => 'Soporocoma',
                                'Somnolen' => 'Somnolen',
                                'Delirium' => 'Delirium',
                                'Coma' => 'Coma',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;margin-bottom:10px;">GCS </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_e')
                            ->textInput(['class' => $classFormNumber])->label(Yii::t('fe', 'Eye')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_m')
                            ->textInput(['class' => $classFormNumber])->label(Yii::t('fe', 'Motorik')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_v')
                            ->textInput(['class' => $classFormNumber])->label(Yii::t('fe', 'Verbal')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'Kg']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;margin-bottom:10px;">Vital Sign </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                            ->textInput(
                                [
                                    'class' => 'form-control input-sm',
                                    'id' => 'tekanan_darah',
                                    'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                    'oninput' => "validateInput(this)",
                                ])->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suhu_badan', ['addon' => ['append' => ['content' => '°C']]])
                            ->textInput(['class' => 'form-control doco-decimal-wcomma']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;">2. Pemeriksaan Fisik </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pf_mata')
                            ->label(Yii::t('fe', 'Mata'))
                            ->checkboxList(
                            [
                                '1' => 'Pandangan Kabur',
                                '2' => 'Adanya Pemandangan Dua',
                                '3' => 'Conjungtiva Pucat',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pf_dada')
                            ->label(Yii::t('fe', 'Dada dan Axylia'))
                            ->checkboxList(
                            [
                                '1' => 'Mammae Simestris',
                                '2' => 'Mammae Asimestris',
                                '3' => 'Areola Hiperpigmentasi',
                                '4' => 'Tumor',
                                '5' => 'Kolostrum',
                                '6' => 'Puting Susu Menonjol',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pf_ekstremitas')
                            ->label(Yii::t('fe', 'Ekstremitas'))
                            ->checkboxList(
                            [
                                '1' => 'Tungkai Simestris',
                                '2' => 'Tungkai Asimestris',
                                '3' => 'Edema',
                                '4' => 'Refleks',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pf_sistem_kardiopulmonal')
                            ->label(Yii::t('fe', 'Sistem Kardiopulmonal'))
                            ->checkboxList(
                            [
                                '1' => 'Dyspneu',
                                '2' => 'Orthopneu',
                                '3' => 'Tachypneu',
                                '4' => 'Wheez Nyeri Dada',
                                '5' => 'Batuk',
                                '6' => 'Sputum',
                                '7' => 'Batuk Darah',
                                '8' => 'Nyeri Dada',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;">3. Pemeriksaan Khusus dan Nifas </p>
                        <p style="margin-left:20px;margin-top:10px;">a. Obstetric </p>
                        <p style="margin-left:20px;margin-top:10px;">Abdomen : </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_inspeksi')
                            ->label(Yii::t('fe', 'Inspeksi'))
                            ->checkboxList(
                            [
                                '1' => 'Membesar Dengan Arah Memanjang',
                                '2' => 'Melebar',
                                '3' => 'Linea Alba',
                                '4' => 'Linea Nigra',
                                '5' => 'Striae Livide',
                                '6' => 'Striae Albican',
                                '7' => 'Luka Bekas Post Op',
                                '8' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_inspeksi_lainnya')
                            ->textInput(['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_palpasi')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Palpasi TFU')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_taksiran_janin', ['addon' => ['append' => ['content' => 'Gram']]])
                            ->textInput(['class' => $classFormNumber])->label(Yii::t('fe', 'Taksiran Berat Janin')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'letak_punggung')->checkboxList([
                                '1' => 'Puka',
                                '2' => 'Puki',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'presentasi')->checkboxList([
                                '1' => 'Kepala',
                                '2' => 'Bokong',
                                '3' => 'U'
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_auskultasi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Auskultasi DJJ')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_auskultasi_teratur')
                            ->radioList(['1' => 'Teratur', '0' => 'Tidak Teratur'], ['inline' => true])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_kontraksi', ['addon' => ['append' => ['content' => 'Menit']]])
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'HIS/Kontraksi')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'obstetric_kontraksi_teratur')
                            ->radioList(['1' => 'Teratur', '0' => 'Tidak Teratur'], ['inline' => true])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:10px;">b. Ginekologi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ginekologi_genital')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Ano Genital')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-inspeksi">Inspeksi</label>
                                <div class="col-sm-9">
                                    <span>Pengeluaran Pervulva</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ginekologi_inspeksi')
                            ->checkboxList(['1' => 'Darah', '2' => 'Lendir', '3' => 'Air Ketuban'], ['inline' => true])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ginekologi_vaginal')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Vaginal Toucher')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:10px;">c. Nifas </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nifas_tfu')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'TFU')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nifas_kontraksi')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Kontraksi Uterus')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nifas_luka')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Luka Jalan Lahir')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nifas_lochea')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Lochea')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:10px;">4. Pemeriksaan Penunjang </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lab')
                            ->textArea(['class' => $classForm])->label(Yii::t('fe', 'Laboratorium')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'usg')
                            ->textArea(['class' => $classForm])->label(Yii::t('fe', 'USG')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var obstetric_inspeksi_lainnya = "'.$model->obstetric_inspeksi_lainnya.'"
function validateInput(input) {
    input.value = input.value.replace(/[^0-9/]/g, "");
    const parts = input.value.split("/");
    if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
        input.value = input.value.slice(0, -1);
    }
}

$(document).ready(function(){
    $("#tekanan_darah").on("input", function () {
        validateInput(this);
    });

    const checkboxObsInspeksi = $("input[name=\'GinekologiForm[obstetric_inspeksi][]\'][value=\'8\']");
    const inputObsInspeksi = $(`#${_modelIdForm}-obstetric_inspeksi_lainnya`);
    inputObsInspeksi.prop("readonly", true);
    if(asesmenMedisId) {
        if(obstetric_inspeksi_lainnya) {
            inputObsInspeksi.prop("readonly", false);
        }
    }
    checkboxObsInspeksi.change(function () {
        if(checkboxObsInspeksi.is(":checked")) {
            inputObsInspeksi.prop("readonly", false);
        }
        else {
            inputObsInspeksi.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);
?>
