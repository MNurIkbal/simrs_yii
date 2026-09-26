<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
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
                                    'todayBtn' => true
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="">
                            <label class="has-star col-sm-4">Triage</label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'triage',
                                        'colSize' => 12,
                                        'data' => $arrayConfig['triage']
                                    ])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
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
                                    'todayBtn' => true
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
                        <label class="has-star col-sm-4">Kasus Polisi</label>
                        <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'kasus_polisi',
                            'colSize' => 4,
                            'data' => $arrayConfig['kasus_polisi']
                        ])
                        ?>
                    </div>
                    <div class="row">
                        <label class="has-star col-sm-4">Kasus Kecelakaan</label>
                        <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'kasus_kecelakaan',
                            'colSize' => 4,
                            'data' => $arrayConfig['kasus_kecelakaan']
                        ])
                        ?>
                    </div>
                    <div class="row">
                        <label for="" class="has-star col-sm-4">Cara Pasien Datang</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= Html::activeRadio($model, 'cara_datang', [
                                        'label' => 'Sendiri',
                                        'id' => 'cara_datang',
                                        'data-dependent' => json_encode([
                                            'id' => 'cara-datang-form'
                                        ]),
                                        'value' => '0'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= Html::activeRadio($model, 'cara_datang', [
                                        'label' => 'Diantar Oleh',
                                        'id' => 'cara_datang_diantar',
                                        'data-dependent' => json_encode([
                                            'id' => 'cara-datang-form'
                                        ]),
                                        'value' => '1'
                                    ]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                    Html::activeTextInput($model, 'cara_datang_diantar', [
                                        'class' => 'form-control default-disabled input-tag',
                                        'id' => 'cara-datang-form'
                                    ]);
                                    ?>
                                </div>
                            </div>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="row">
                        <label for="" class="control-label has-star col-sm-4">Anamnesis</label>
                        <div class="col-sm-8">
                            <?= Html::activeRadio($model, 'allo_or_auto', [
                                'value' => '0',
                                'id' => 'allo_or_auto-0-asmed',
                                'label' => 'Auto Anamnesa',
                                'data-dependent' => [
                                    'id' => 'asesmen_allo_text--dependent'
                                ],
                                'data-fieldname' => 'allo_or_auto'
                            ]) ?>
                        </div>
                    </div> 
                    <div class="row">   
                        <label for="" class="has-star col-sm-4"></label>
                        <div class="col-sm-4">
                            <?= Html::activeRadio($model, 'allo_or_auto', [
                                'value' => '1',
                                'id' => 'allo_or_auto-1-asmed',
                                'label' => 'Allo Anamnesa',
                                'data-dependent' => [
                                    'id' => 'asesmen_allo_text--dependent'
                                ],
                                'data-fieldname' => 'allo_or_auto'
                            ]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'asesmen_allo_anamnesa', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'default-disabled', 'id' => 'asesmen_allo_text--dependent'])->label(false) ?>
                        </div>
                    </div>
                    <div class="row form-row">
                        <label class="has-star col-sm-4">Keluhan Utama</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                Html::activeTextarea($model, 'keluhan_utama', ['class' => 'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <label class="has-star col-sm-4">Riwayat Penyakit Sekarang</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                Html::activeTextarea($model, 'riwayat_penyakit_sekarang', ['class' => 'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row form-row">
                        <label class="has-star col-sm-4">Riwayat Penyakit Dahulu</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                Html::activeTextarea($model, 'riwayat_penyakit_dahulu', ['class' => 'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <label class="has-star col-sm-4">Riwayat Terapi Sebelumnya</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                Html::activeTextarea($model, 'riwayat_terapi_sebelumnya', ['class' => 'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <label class="has-star col-sm-4">Alergi</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                Html::activeTextarea($model, 'alergi', ['class' => 'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
