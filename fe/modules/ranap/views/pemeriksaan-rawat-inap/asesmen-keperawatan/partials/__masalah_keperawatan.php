<?php

use app\components\DHtml;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Masalah Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'bersihan_jalan')->textarea() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'gangguan_perfusi_cerebral')->textarea() ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'pola_nafas')->textarea() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'gangguan_perfusi_perifer')->textarea() ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'gangguan_gas')->textarea() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'valume_cairan_tubuh')->textarea() ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'nyeri')->textarea() ?>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-4">Keterbatasan Fisik & Kognitif</label>
                        <div class="col-sm-8">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'gangguan_thermoregulasi_tipe',
                                    'data' => [
                                        'hypertermi' => 'Hypertermi',
                                        'hypotermi' => 'Hypotermi'
                                    ],
                                    'colSize' => 4
                                ])
                            ?>
                        </div>
                        <div class="col-sm-8 col-sm-offset-4">
                            <?= $form->field($model, 'gangguan_thermoregulasi_nilai', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textarea(['id' => 'gangguan_thermoregulasi_nilai--form', 'class' => 'default-disabled'])->label(false) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>