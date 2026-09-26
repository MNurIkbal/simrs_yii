<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">F. Tumbuh Kembang</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'tengkurep_usia')
                            ->label(Yii::t('fe', 'Tengkurap, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'duduk_usia')
                            ->label(Yii::t('fe', 'Duduk, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'tumbuh_gigi_usia')
                            ->label(Yii::t('fe', 'Tumbuh Gigi, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'berdiri_usia')
                            ->label(Yii::t('fe', 'Berdiri, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'bicara_usia')
                            ->label(Yii::t('fe', 'Bicara, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'berjalan_usia')
                            ->label(Yii::t('fe', 'Berjalan, usia'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'makanan')
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'asi')
                            ->radioList(
                                [1 => 'Ya', 0 => 'Tidak'],
                                [
                                    'inline' => true,
                                    'itemOptions' => [
                                        'class' => 'asi'
                                    ]
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'asi_lamanya', ['addon' => ['append' => ['content' => 'Bulan']]])
                            ->label(Yii::t('fe', 'Lamanya'))
                            ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'susu_formula')
                            ->radioList(
                                [1 => 'Ya', 0 => 'Tidak'],
                                [
                                    'inline' => true,
                                    'itemOptions' => [
                                        'class' => 'susu_formula'
                                    ]
                                ]
                            ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var asi_lamanya = "'.$model->asi_lamanya.'"

$(document).ready(function(){
    const asiLamanya = $("#anakform-asi_lamanya");
    asiLamanya.prop("readonly", true);

    if(asesmenMedisId) {
        if(asi_lamanya) {
            asiLamanya.prop("readonly", false);
        }
    }
    
    $(document).on("change", ".asi", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "1") {
                asiLamanya.prop("readonly", false);
            }
            else {
                asiLamanya.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>
