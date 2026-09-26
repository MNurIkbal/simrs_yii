<?php

use app\components\DHtml;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Asesmen Medis') ?></h5>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'custom-save' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-save',
                'attributes' => [
                    'id' => 'submit-fisik',
                    'disabled' => empty($pendaftaran_id),
                    'data-options' => 'click'
                ],
            ],
            // 'reset',
            'custom-print' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Cetak'),
                'icon' => 'fa fa-print',
                'attributes' => [
                    'class' => 'print',
                    'method' => 'json',
                    'data-options' => 'link',
                    'disabled' => empty($model) ? true : false
                ],
            ],
        ]); ?>
    </div>
    <div class="panel-body rajal-form">
        <?php
        if (!empty($pendaftaran_id)) {
            $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
                'id' => 'form-fisik',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],

            ]);
        ?>

            <p class="header-form">Data Medis (Diisi Oleh Dokter)</p>

            <p class="header-form">1. Anamnesa</p>
            <div class="row form-row">
                <?php
                foreach ($configVal['anamnesa'] as $keyAnamnesa => $anamnesa) :
                ?>
                    <div class="col-sm-2">
                        <?=
                            Html::activeRadio($modelFisik, 'anamnesa', [
                                'value' => $keyAnamnesa,
                                'id' => 'anamnesa-' . $keyAnamnesa,
                                'label' => '<span class="">' . $anamnesa . '</span>',
                                'data-fieldname' => 'anamnesa',
                                'inline' => true
                            ]);
                        ?>
                    </div>
                <?php
                endforeach;
                ?>
            </div>

            <br>

            <p class="header-form">2. Keluhan Utama</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'keluhan_utama', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">3. Riwayat Penyakit</p>
            <p class="header-form">A. Riwayat Penyakit Sekarang</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'riwayat_penyakit_sekarang', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">B. Riwayat Penyakit Dahulu</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'riwayat_penyakit_dahulu', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">C. Riwayat Penyakit Keluarga</p>
            <div class="row form-row">
                <?= DHtml::multipleCheckbox([
                    'model' => $modelFisik,
                    'fieldName' => 'riwayat_penyakit_keluarga',
                    'data' => $configVal['riwayat_penyakit_keluarga']['first_row'],
                    'colSize' => '1'
                ]);
                ?>
            </div>
            <div class="row form-row">
                <?= DHtml::multipleCheckbox([
                    'model' => $modelFisik,
                    'fieldName' => 'riwayat_penyakit_keluarga',
                    'data' => $configVal['riwayat_penyakit_keluarga']['second_row'],
                    'colSize' => '1',
                ]);
                ?>
                <div class="col-sm-4">
                    <?=
                        Html::activeTextInput($modelFisik, 'riyawat_penyakit_lainnya', [
                            'class' => 'form-control input-tag',
                            'id' => 'rpl'
                        ]);
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <p class="header-form">4. Pemeriksaan Umum</p>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-2">
                    <label for="control-label text-label">Kesadaran</label>
                </div>
                <?= DHtml::multipleRadio([
                    'model' => $modelFisik,
                    'fieldName' => 'kesadaran',
                    'data' => $configVal['kesadaran'],
                    'colSize' => '1'
                ]);
                ?>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'tekanandarah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class' => 'doco-number']); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'suhutubuh', ['addon' => ['append' => ['content' => '^C']]])->textInput(['class' => 'doco-number']); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'pernapasan', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'detaknadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'tinggibadan_cm', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-number']); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'beratbadan_kg', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-number']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($modelFisik, 'imt')->textInput(['class' => 'doco-number']); ?>
                </div>
            </div>

            <br>

            <p class="header-form">5. Status Lokalis</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'status_lokalis', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">6. Pemeriksaan Penunjang</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'pemeriksaan_penunjang', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">7. Diagnosa kerja</p>
            <div class="row form-row">
                <?=
                    Html::activeTextarea($modelFisik, 'diagnosa_kerja', ['class' => 'form-control']);
                ?>
                <div class="help-block">
                </div>
            </div>

            <br>

            <p class="header-form">8. Daftar Masalah Medis</p>
            <div class="row formr">
                <div class="col-sm-12">
                    <table class="table table-condensed" id="table-diagnosa-fisik">
                        <thead>
                            <tr class="bg-inverse">
                                <th class="text-center">Masalah / Diagnosa Medis</th>
                                <th class="text-center">Rencana / Tata Laksana Medis</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" name="masalah_diagnosa_medis" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="rencana_laksana_medis" class="form-control">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-xs btn-action btn-add-diagnosa"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <?php ActiveForm::end(); ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php
if (!empty($pendaftaran_id)) {
    $this->registerJs('

    // define id
    var id = "' . $pendaftaran_id . '";
    // define no_pendaftaran
    var fisikData = ' . json_encode($fisikData) . '

', View::POS_END);
    $this->registerJs($this->render('js/_asesmen_medis.js'), View::POS_END);
}
?>