<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
<style>
    .gizi {width: 57px}
</style>

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><b>Proses Asuhan Gizi Terstandar (PAGT)</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <?=
                                $form->field($model, 'tgl_kajian')->widget(DateTimePicker::className(), [
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd-MM-yyyy HH:mm:ss',
                                        'autoclose' => true,
                                        'todayBtn' => true
                                    ]
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'diagnosa_medis')->textInput(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'diet')->textInput(); ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'pandangan_alergi')->textInput(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><b>Asesmen Gizi</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>A. Anthropometri</b></h5></label>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'bb', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'imt_anak', ['addon' => ['append' => ['content' => 'kg/m2']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'tb', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'bb_anak', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'imt_dewasa', ['addon' => ['append' => ['content' => 'kg/m2']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'ulna', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'lila', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="">
                        <label class="control-label has-star col-sm-1">Status Gizi</label>
                        <div class="col-sm-2">
                            <div class="row">
                                <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'status_gizi',
                                        'colSize' => 12,
                                        'data' => $arrayConfig['status_gizi']
                                    ])
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>B. Biokimia</b></h5></label>
                    <label class="control-label has-star col-sm-12">Hasil Lab Abnormal</label>
                    <label class="control-label has-star col-sm-12"><b>Profil Lemak</b></label>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'trigliserida', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'hdl', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'ldl', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'kolesterol', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>

                    <label class="control-label has-star col-sm-12"><b>Fungsi Ginjal</b></label>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'ureum', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'kalsium', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'kreatinin', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'phospor', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'kalium', ['addon' => ['append' => ['content' => 'meg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'natrium', ['addon' => ['append' => ['content' => 'meg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>

                    <label class="control-label has-star col-sm-12"><b>Fungsi Hati</b></label>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'sgot', ['addon' => ['append' => ['content' => 'u/l']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'sgpt', ['addon' => ['append' => ['content' => 'u/l']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'bilirubin', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>

                    <label class="control-label has-star col-sm-12"><b>Fungsi Ginjal</b></label>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'gd_sewaktu', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'hb', ['addon' => ['append' => ['content' => 'g/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'gd_puasa', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'albumin', ['addon' => ['append' => ['content' => 'g/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'hba1c', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'ht', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'dua_jam_pp', ['addon' => ['append' => ['content' => 'mg/dl']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>C. Pemeriksaan Fisik</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-1">Pemeriksaan Fisik</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model' => $model,
                                                'fieldName' => 'pemeriksaan_fisik',
                                                'colSize' => 12,
                                                'data' => $arrayConfig['pemeriksaan_fisik']
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>D. Gangguan Pencernaan</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-1">Gangguan Pencernaan</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleCheckbox([
                                                'model' => $model,
                                                'fieldName' => 'gangguan_pencernaan',
                                                'colSize' => 12,
                                                'data' => $arrayConfig['gangguan_pencernaan']
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>E. Riwayat Diet</b></h5></label>
                    <label class="control-label has-star col-sm-12">Food recall 1x24 jam yang lalu:</label>
                    <div class="col-sm-12">
                        <table class="table table-bordered">
                            <thead>
                              <tr class="bg-inverse">
                                <th rowspan="2" class="text-center">Waktu Makan</th>
                                <th colspan="5" class="text-center">Porsi yang Dihabiskan (1 P, 3/4 P, 1/2 P)</th>
                                <th colspan="4" class="text-center">Asupan Gizi</th>
                              </tr>
                              <tr class="bg-inverse">
                                <td>Makanan Pokok</td>
                                <td>Hewani</td>
                                <td>Nabati</td>
                                <td>Sayur</td>
                                <td>Buah</td>
                                <td>Energi</td>
                                <td>Protein</td>
                                <td>Lemak</td>
                                <td>KH</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>Makan Pagi</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_pagi_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_pagi_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_pagi_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_pagi_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_pagi_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_pagi_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_pagi_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_pagi_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_pagi_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Selingan Pagi</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_pagi_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_pagi_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_pagi_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_pagi_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_pagi_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_pagi_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_pagi_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_pagi_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_pagi_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Makan Siang</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_siang_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_siang_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_siang_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_siang_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_siang_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_siang_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_siang_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_siang_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_siang_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Selingan Sore</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_sore_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_sore_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_sore_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_sore_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_sore_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_sore_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_sore_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_sore_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_sore_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Makan Malam</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_malam_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_malam_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_malam_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_malam_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'makan_malam_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_malam_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_malam_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_malam_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'makan_malam_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Selingan Malam</td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_malam_pokok',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_malam_hewani',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_malam_nabati',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_malam_sayur',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'selingan_malam_buah',
                                            'colSize' => 12,
                                            'data' => $arrayConfig['porsi_makan']
                                        ])
                                    ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_malam_energi')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_malam_protein')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_malam_lemak')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'selingan_malam_kh')->textInput(['class' => 'doco-number gizi'])->label(false); ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <?= $form->field($model, 'total_energi')->textInput(['class' => 'doco-number gizi', 'disabled' => true])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'total_protein')->textInput(['class' => 'doco-number gizi', 'disabled' => true])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'total_lemak')->textInput(['class' => 'doco-number gizi', 'disabled' => true])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'total_kh')->textInput(['class' => 'doco-number gizi', 'disabled' => true])->label(false); ?>
                                </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'alergi')->textInput(); ?>
                        <?= $form->field($model, 'diet_dijalankan')->textInput(); ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'olah_raga_hari', ['addon' => ['append' => ['content' => 'kali/hari']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'olah_raga_menit', ['addon' => ['append' => ['content' => 'menit']]])->textInput(['class' => 'doco-number'])->label(false); ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kebiasaan_merokok', ['addon' => ['append' => ['content' => 'batang/hari']]])->textInput(['class' => 'doco-number']) ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="">
                        <label class="control-label has-star col-sm-2">Aktifitas Fisik</label>
                        <div class="col-sm-6">
                            <div class="row">
                                <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'aktifitas_fisik',
                                        'colSize' => 12,
                                        'data' => $arrayConfig['aktifitas_fisik']
                                    ])
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

