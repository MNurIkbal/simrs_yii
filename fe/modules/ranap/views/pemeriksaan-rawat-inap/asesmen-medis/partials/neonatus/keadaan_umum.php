<?php

use yii\helpers\Html;
use yii\web\View;

$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">E. Keadaan Umum</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-10">
                            <?= $form->field($model, 'kesadaran')->checkboxList(
                            [
                                'Composmentis' => 'Composmentis',
                                'Apatis' => 'Apatis',
                                'Soporocoma' => 'Soporocoma',
                                'Somnolen' => 'Somnolen',
                                'Delirium' => 'Delirium',
                                'Coma' => 'Coma',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>

                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">GCS </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_e', ['addon' => ['append' => ['content' => 'E']]])
                            ->textInput(['class' => $classFormNumber])->label(false); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_m', ['addon' => ['append' => ['content' => 'M']]])
                            ->textInput(['class' => $classFormNumber])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gcs_v', ['addon' => ['append' => ['content' => 'V']]])
                            ->textInput(['class' => $classFormNumber])->label(false); ?>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ballard_score')->textInput(['class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                            ->textInput(
                                [
                                    'class' => 'form-control input-sm',
                                    'id' => 'tekanan_darah',
                                    'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                    'oninput' => "validateInput(this)",
                                ])->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suhu_badan', ['addon' => ['append' => ['content' => '°C']]])
                            ->textInput(['class' => 'form-control doco-decimal-wcomma']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

function validateInput(input) {
    input.value = input.value.replace(/[^0-9/]/g, "");
    const parts = input.value.split("/");
    if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
        input.value = input.value.slice(0, -1);
    }
}

$(document).ready(function(){
    $("#tekanan_darah").on("input", function () {
        validateInput(this);
    });
})

', View::POS_END);
?>
