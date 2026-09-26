<?php
use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Alasan Masuk IGD</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="form-group required">
                        <label for="" class="control-label has-star col-sm-4">Asesmen Keperawatan</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= Html::activeRadio($model, 'allo_or_auto', [
                                        'value' => '0',
                                        'id' => 'allo_or_auto-0',
                                        'label' => 'Auto Anamnesa',
                                        'data-dependent' => [
                                            'id' => 'asesmen_allo_text--dependent'
                                        ],
                                        'data-fieldname' => 'allo_or_auto'
                                    ]) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= Html::activeRadio($model, 'allo_or_auto', [
                                        'value' => '1',
                                        'id' => 'allo_or_auto-1',
                                        'label' => 'Allo Anamnesa',
                                        'data-dependent' => [
                                            'id' => 'asesmen_allo_text--dependent'
                                        ],
                                        'data-fieldname' => 'allo_or_auto'
                                    ]) ?>
                                </div>
                                <div class="col-sm-8">
                                    <?= $form->field($model, 'asesmen_allo_text', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'default-disabled', 'id' => 'asesmen_allo_text--dependent'])->label(false) ?>
                                </div>
                            </div>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <?= $form->field($model, 'keluhan')->textarea() ?>
                    <?= $form->field($model, 'r_penyakitsaatini')->textarea() ?>
                    <?= $form->field($model, 'r_penyakitdahulu')->textarea() ?>
                </div>
                <div class="col-sm-6">
                    <div class="form-group required">
                        <label for="" class="control-label has-star col-sm-4">Riwayat Alergi</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                    DHtml::trueFalseRadio($model, 'is_alergi', [
                                        'childDependent' => [
                                            'class' => 'alergi--dependent'
                                        ],
                                        'colSize' => 12
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-4">
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeCheckbox($model, 'is_alergiobat', [
                                            'value' => '1',
                                            'label' => 'Obat',
                                            'class' => 'alergi--dependent '.(is_null($model->is_alergiobat) ? 'default-disabled' : ''),
                                            'data-dependent' => [
                                                'class' => 'alergi_obat--dependent'
                                            ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                        Html::activeTextInput($model, 'alergi_obat', [
                                            'class' => 'form-control input-tag alergi_obat--dependent default-disabled'
                                        ]);
                                    ?>
                                    <div class="help-block"></div>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeCheckbox($model, 'is_alergilainnya', [
                                            'value' => '1',
                                            'label' => 'Lainnya',
                                            'class' => 'alergi--dependent '.(is_null($model->is_alergilainnya) ? 'default-disabled' : ''),
                                            'data-dependent' => [
                                                'class' => 'alergi_lainnya--dependent'
                                            ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                        Html::activeTextInput($model, 'alergi_lainnya', [
                                            'class' => 'form-control input-tag alergi_lainnya--dependent default-disabled'
                                        ]);
                                    ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= $form->field($model, 'r_pengobatan')->textarea() ?>
                </div>
            </div>
        </div>
    </div>
</div>