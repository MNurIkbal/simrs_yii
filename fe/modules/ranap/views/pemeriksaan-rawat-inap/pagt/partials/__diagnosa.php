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
            <h5 class="panel-title"><b>Diagnosa Gizi</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <p>(Problem (P)) berkaitan dengan (Etiology (E)) ditandai dengan (sign & symptomp (S))</p>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <label class="control-label has-star">Diagnosa Gizi</label>
                    </div>
                    <div class="col-sm-10">
                        <?= Html::activeTextarea($model, 'diagnosa_gizi', ['class' => 'form-control']); ?>
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
            <h5 class="panel-title"><b>Intervensi Gizi</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>A. Cara Intervensi</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-2">Cara Intervensi</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model' => $model,
                                                'fieldName' => 'cara_intervensi',
                                                'colSize' => 12,
                                                'data' => $arrayConfig['cara_intervensi']
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
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>B. Diet Yang Diberikan</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'diet_diberikan')->textInput(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>C. Nilai Zat Gizi</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                            <?= $form->field($model, 'energi', ['addon' => ['append' => ['content' => 'kalori']]])->textInput(['class' => 'doco-number']) ?>
                            <?= $form->field($model, 'lemak', ['addon' => ['append' => ['content' => 'gram']]])->textInput(['class' => 'doco-number']) ?>
                            <?= $form->field($model, 'protein', ['addon' => ['append' => ['content' => 'gram']]])->textInput(['class' => 'doco-number']) ?>
                            <?= $form->field($model, 'kh', ['addon' => ['append' => ['content' => 'gram']]])->textInput(['class' => 'doco-number']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>D. Tujuan Diet</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <div class="col-sm-2">
                                    <label class="control-label has-star">Tujuan Diet</label>
                                </div>
                                <div class="col-sm-10">
                                    <?= Html::activeTextarea($model, 'tujuan_diet', ['class' => 'form-control']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-row">
                <div class="col-sm-12">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>E. Bentuk Makanan</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-2">Bentuk Makanan</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'bentuk_makanan', [
                                                'value' => 'biasa',
                                                'label' => 'Biasa',
                                                'id' => 'bentuk_makanan-biasa',
                                                'data-fieldname' => 'bentuk_makanan'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'bentuk_makanan', [
                                                'value' => 'lunak',
                                                'label' => 'Lunak',
                                                'id' => 'bentuk_makanan-lunak',
                                                'data-fieldname' => 'bentuk_makanan'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'bentuk_makanan', [
                                                'value' => 'cair/teh/susu',
                                                'label' => 'Cair/Teh/Susu',
                                                'id' => 'bentuk_makanan-cair/teh/susu',
                                                'data-fieldname' => 'bentuk_makanan'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'bentuk_makanan', [
                                                'value' => 'saring/sumsum',
                                                'label' => 'Saring/Sumsum',
                                                'id' => 'bentuk_makanan-saring/sumsum',
                                                'data-fieldname' => 'bentuk_makanan'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= Html::activeCheckbox($model, 'bentuk_makanan', [
                                                'value' => 'sonde_voeding',
                                                'label' => 'Sonde Voeding',
                                                'id' => 'bentuk_makanan-sonde_voeding',
                                                'data-fieldname' => 'bentuk_makanan'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'bentuk_makanan_saji', ['addon' => ['append' => ['content' => 'cc/saji']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'bentuk_makanan_saji--form'])->label(false) ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'bentuk_makanan_hari', ['addon' => ['append' => ['content' => 'kali/hari']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'bentuk_makanan_hari--form'])->label(false) ?>
                                        </div>
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
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>F. Cara Pemberian</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-2">Cara Pemberian</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'cara_pemberian', [
                                                'value' => 'oral',
                                                'label' => 'Oral',
                                                'id' => 'cara_pemberian-oral',
                                                'data-fieldname' => 'cara_pemberian'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'cara_pemberian', [
                                                'value' => 'enteral',
                                                'label' => 'Enteral',
                                                'id' => 'cara_pemberian-enteral',
                                                'data-fieldname' => 'cara_pemberian'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeCheckbox($model, 'cara_pemberian', [
                                                'value' => 'ngt',
                                                'label' => 'NGT',
                                                'id' => 'cara_pemberian-ngt',
                                                'data-fieldname' => 'cara_pemberian'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= Html::activeCheckbox($model, 'cara_pemberian', [
                                                'value' => 'vitamin/mineral',
                                                'label' => 'Vitamin/Mineral',
                                                'id' => 'cara_pemberian-vitamin-mineral',
                                                'data-fieldname' => 'cara_pemberian'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'cara_pemberian_vitamin')->textInput(['class' => 'form-control input-tag', 'id' => 'cara_pemberian_vitamin--form'])->label(false) ?>
                                        </div>
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
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title"><b>G. Pembagian Makan Sehari</b></h5></label>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                  <tr class="bg-inverse">
                                    <th>Waktu Makan</th>
                                    <th>Nasi/Penukar</th>
                                    <th>Hewani</th>
                                    <th>Nabati</th>
                                    <th>Sayur</th>
                                    <th>Buah</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Makan Pagi</td>
                                    <td><?= $form->field($model, 'bagi_makan_pagi_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_pagi_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_pagi_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_pagi_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_pagi_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                  <tr>
                                    <td>Selingan Pagi</td>
                                    <td><?= $form->field($model, 'bagi_selingan_pagi_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_pagi_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_pagi_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_pagi_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_pagi_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                  <tr>
                                    <td>Makan Siang</td>
                                    <td><?= $form->field($model, 'bagi_makan_siang_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_siang_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_siang_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_siang_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_siang_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                  <tr>
                                    <td>Selingan Sore</td>
                                    <td><?= $form->field($model, 'bagi_selingan_sore_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_sore_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_sore_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_sore_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_sore_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                  <tr>
                                    <td>Makan Malam</td>
                                    <td><?= $form->field($model, 'bagi_makan_malam_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_malam_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_malam_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_malam_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_makan_malam_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                  <tr>
                                    <td>Selingan Malam</td>
                                    <td><?= $form->field($model, 'bagi_selingan_malam_nasi')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_malam_hewani')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_malam_nabati')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_malam_sayur')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($model, 'bagi_selingan_malam_buah')->textInput()->label(false); ?></td>
                                  </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


