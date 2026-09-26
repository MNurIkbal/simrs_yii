<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>

<!-- Asesmen Medis -->
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-12 col-sm-12 col-md-5">
                    <div class="row">
                        <div class="col-sm-12">
                            <?=
                            $form->field($model, 'tgl_pasien_datang')->widget(DateTimePicker::className(), [
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    'startDate' => isset($data_pasien['tgl_pendaftaran']) ? $data_pasien['tgl_pendaftaran'] : 0, // Min Date Tidak boleh lebih dari pendaftaran
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <label class="has-star control-label col-sm-4">Triage</label>
                        <div class="col-sm-8">
                            <div class="row">
                              <div class="col-sm-12 col-md-10 btn-triage triage-sehari">
                                  <button data-value="resusitasi" class="btn btn-danger btn-resusitasi <?= $model->triage == 'resusitasi' ? 'btn-triage--active' : ''?>" type="button" disabled=true>Gawat darurat</button>
                                  <button data-value="emergent" class="btn btn-danger btn-emergent <?= $model->triage == 'emergent' ? 'btn-triage--active' : ''?>" type="button"disabled=true>Membahayakan jiwa</button>
                                  <button data-value="urgent" class="btn btn-warning btn-urgent <?= $model->triage == 'urgent' ? 'btn-triage--active' : ''?>" type="button" disabled=true>Berpotensi membahayakan jiwa</button>
                                  <button data-value="non_urgent" class="btn btn-success btn-non_urgent <?= $model->triage == 'non_urgent' ? 'btn-triage--active' : ''?>" type="button" disabled=true>Dapat menjadi serius</button>
                                  <button data-value="false_emergency" class="btn btn-success btn-false_emergency <?= $model->triage == 'false_emergency' ? 'btn-triage--active' : ''?>" type="button" disabled=true>Tidak berbahaya</button>
                                  <div class="help-block"></div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 ">
                    <div class="row">
                        <div class="col-sm-12">
                            <?=
                            $form->field($model, 'tgl_asesmen')->widget(DateTimePicker::className(), [
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    'startDate' => isset($data_pasien['tgl_pendaftaran']) ? $data_pasien['tgl_pendaftaran'] : 0, // Min Date Tidak boleh lebih dari pendaftaran
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'dikirim_oleh')->textInput(); ?>
                        </div>
                    </div>
                    <div class="row">
                        <label class="has-star control-label col-sm-4">Kasus Polisi</label>
                        <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'kasus_polisi',
                            'colSize' => 2,
                            'data' => $arrayConfig['kasus_polisi']
                        ])
                        ?>
                    </div>
                    <div class="row">
                        <label class="has-star control-label col-sm-4">Kasus Kecelakaan</label>
                        <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'kasus_kecelakaan',
                            'colSize' => 2,
                            'data' => $arrayConfig['kasus_kecelakaan']
                        ])
                        ?>
                    </div>
                    <div class="row">
                        <label for="" class="has-star control-label col-sm-4">Cara Pasien Datang</label>
                        <div class="col-sm-2">
                            <?= Html::activeRadio($model, 'cara_datang', [
                                'label' => 'Sendiri',
                                'id' => 'cara_datang',
                                'data-dependent' => json_encode([
                                    'id' => 'cara-datang-form'
                                ]),
                                'value' => '0'
                            ]) ?>
                        </div>
                        <div class="col-sm-3">
                            <?= Html::activeRadio($model, 'cara_datang', [
                                'label' => 'Diantar Oleh',
                                'id' => 'cara_datang_diantar',
                                'data-dependent' => json_encode([
                                    'id' => 'cara-datang-form'
                                ]),
                                'value' => '1'
                            ]) ?>
                        </div>
                        <div class="col-sm-3">
                            <?=
                            Html::activeTextInput($model, 'cara_datang_diantar', [
                                'class' => 'form-control default-disabled input-tag',
                                'id' => 'cara-datang-form'
                            ]);
                            ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
</div>
