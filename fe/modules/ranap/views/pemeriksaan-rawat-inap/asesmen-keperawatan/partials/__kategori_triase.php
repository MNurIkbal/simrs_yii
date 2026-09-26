<?php

use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Kategori Triase</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="" class="control-label has-star col-sm-1">Sehari-hari</label>
                        <div class="col-sm-8 btn-triage triage-sehari">
                            <button data-value="resusitasi" class="btn btn-danger" type="button">Resusitasi</button>
                            <button data-value="emergent" class="btn btn-danger" type="button">Emergency</button>
                            <button data-value="urgent" class="btn btn-warning" type="button">Urgent</button>
                            <button data-value="non_urgent" class="btn btn-success" type="button">Non Urgent</button>
                            <button data-value="false_emergency" class="btn btn-success" type="button">Less Urgent</button>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="" class="control-label has-star col-sm-1">Disaster</label>
                        <div class="col-sm-8 btn-triage triage-disaster">
                            <button data-value="merah" class="btn btn-danger" type="button">Merah</button>
                            <button data-value="kuning" class="btn btn-warning" type="button">Kuning</button>
                            <button data-value="hijau" class="btn btn-success" type="button">Hijau</button>
                            <button data-value="hitam" class="btn btn-black" type="button">Hitam</button>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <p class="header-form">Disability / Ketidakmampuan</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'gcseye_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcseyeForm']) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'gcsverbal_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsverbalForm']) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'gcsmotorik_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsmotorikForm']) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'hasil_gcs', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-2']])->textInput(['class' => 'default-disabled']) ?>
                     <hr>
                </div>
                <!--
                <div class="col-sm-8 col-sm-offset-2">
                    <?= Html::activeCheckbox($model, 'is_kapitis', [
                        'value' => '1',
                        'label' => 'Kapitis'
                    ]) ?>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="" class="control-label col-sm-2">Keterangan GCS</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control default-disabled" name="keterangan_gcs">
                        </div>
                    </div>
                    <hr>
                </div>
                -->
                <div class="col-sm-12">
                    <?= $form->field($model, 'pupil', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->radioList(['isokor' => 'Isokor', 'unisokort' => 'Unisokort'], ['inline' => true]) ?>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <label for="" class="control-label col-sm-12">Reaksi Pupil</label>
                    </div>
                    <div class="row">
                        <?=
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'reaksi_pupil_lainnya',
                                'data' => $arrayConfig['reaksi_pupil'],
                                'colSize' => 12,
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <br>
                    <div class="row">
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'reaksi_pupil', ['label' => 'OS', 'value' => 'os', 'id' => 'reaksi_pupil-1']) ?>
                        </div>
                        <div class="col-sm-4 append-osod <?=$model->pupil == 'unisokort' ? '' : 'hidden'?>">
                            <?=Html::activeTextInput($model, 'pupil_os', ['class' => 'form-control', 'id' => 'pupil-os-text', 'disabled' => $model->reaksi_pupil == 'os' ? false : true])?>
                            <br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'reaksi_pupil', ['label' => 'OD', 'value' => 'od', 'id' => 'reaksi_pupil-0']) ?>
                        </div>
                        <div class="col-sm-4 append-osod <?=$model->pupil == 'unisokort' ? '' : 'hidden'?>">
                            <?=Html::activeTextInput($model, 'pupil_od', ['class' => 'form-control', 'id' => 'pupil-od-text', 'disabled' => $model->reaksi_pupil == 'od' ? false : true])?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>