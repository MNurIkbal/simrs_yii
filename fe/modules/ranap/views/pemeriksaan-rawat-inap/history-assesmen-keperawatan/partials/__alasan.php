<?php
use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row" id="__alasan_ranap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Alasan Masuk Rawat Inap</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="form-group required">
                        <label for="" class="control-label has-star col-sm-4">Asesmen Keperawatan</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeCheckbox($model, 'asesmen_auto', [
                                            'label' => 'Auto Anamnesa',
                                            'disabled' => true,
                                            'data-dependent' => [
                                                'class' => 'asesmen_auto_text--dependent'
                                            ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php
                                        // Html::activeTextInput($model, 'asesmen_auto_anamnesa', [
                                        //     'class' => 'form-control input-tag  default-disabled asesmen_auto_text--dependent',
                                        // ]);
                                    ?>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeCheckbox($model, 'asesmen_allo', [
                                            'label' => 'Allo Anamnesa',
                                            'disabled' => true,
                                            'data-dependent' => [
                                                'class' => 'asesmen_allo_text--dependent'
                                            ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                        Html::activeTextInput($model, 'asesmen_allo_text', [
                                            'class' => 'form-control input-tag',
                                            'disabled' => true,
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <?= $form->field($model, 'keluhan')->textarea(['disabled' => true]) ?>
                    <?= $form->field($model, 'r_penyakitsaatini')->textarea(['disabled' => true]) ?>
                    <?= $form->field($model, 'r_penyakitdahulu')->textarea(['disabled' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <div class="form-group required">
                        <label for="" class="control-label has-star col-sm-4">Riwayat Alergi</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <?=
                                    DHtml::trueFalseRadio($model, 'is_alergi', [
                                        'childDependent' => [
                                            'class' => 'alergi--dependent',
                                        ],
                                        'colSize' => 12,
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
                                            'disabled' => true,
                                            'data-dependent' => [
                                                'class' => 'alergi_obat--dependent'
                                            ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                        Html::activeTextInput($model, 'alergi_obat', [
                                            'class' => 'form-control input-tag alergi_obat--dependent default-disabled',
                                            'disabled' => true
                                        ]);
                                    ?>
                                    <div class="help-block"></div>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeCheckbox($model, 'is_alergilainnya', [
                                            'value' => '1',
                                            'label' => 'Lainnya',
                                            // 'class' => 'alergi--dependent '.(is_null($model->is_alergilainnya) ? 'default-disabled' : ''),
                                            'disabled' => true,
                                            // 'data-dependent' => [
                                            //     'class' => 'alergi_lainnya--dependent'
                                            // ]
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?=
                                        Html::activeTextInput($model, 'alergi_lainnya', [
                                            'class' => 'form-control input-tag alergi_lainnya--dependent default-disabled',
                                            'disabled' => true
                                        ]);
                                    ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= $form->field($model, 'r_pengobatan')->textarea([ 'disabled' => true]) ?>
                </div>
            </div>
        </div>
    </div>
</div>