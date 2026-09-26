<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\date\DatePicker;

$classForm = 'form-control';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'margin-top:10px;margin-bottom:10px;';
?>
<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-asmed-ranap',
                'id' => 'submit-asmed-ranap',
            ]
        ],
    ]); ?>
</div>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-bottom:25px;">
<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<br>
<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemeriksaan Umum</h5>
            </div>
            <div class="panel-body">
                <br>
                <div class="row" style="text-align:center;">
                    <div class="image-frame">
                        <?php
                            echo Html::img( '@web/media/img/img-pemeriksaan/mata-custom.jpg', [
                                'width'=> 600,
                                'height'=> 520,
                            ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table style="width:100%" class="table-kebidanan">
                            <thead>
                                <tr>
                                    <th style="<?= $styleTable?>" id="header_kehamilan1">No</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan2">Parameter</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan2">Oculus Dexter</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan3">Oculus Sinister</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">1</td>
                                    <td>Palpebra</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[palpebra_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[palpebra_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">2</td>
                                    <td>Silia</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[silia_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[silia_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">3</td>
                                    <td>APP.Laksimal</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[laksimal_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[laksimal_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">4</td>
                                    <td>Konjungtiva</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[konjungtiva_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[konjungtiva_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">5</td>
                                    <td>Kornea</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[kornea_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[kornea_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">6</td>
                                    <td>Bilik Mata Depan</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[bilik_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[bilik_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">7</td>
                                    <td>Iris</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[iris_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[iris_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">8</td>
                                    <td>Pupil</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[pupil_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[pupil_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">9</td>
                                    <td>Lensa</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[lensa_dexter]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="MataForm[lensa_sinister]" class="<?=$classForm?>" style="<?=$styleCells?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'visus')
                            ->label(Yii::t('fe', '1. Visus'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tonometri')
                            ->label(Yii::t('fe', '2. Tonometri'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'funduskopi')
                            ->label(Yii::t('fe', '3. Funduskopi'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa')
                            ->label()->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'ringkasan')
                            ->label()->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa_diferensial')
                            ->label()->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'kesan')
                            ->label(Yii::t('fe', 'Kesan/Diagnosa Kerja'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
var isDokumenEklaim = "' . $isDokumenEklaim . '";
var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"
var _modelForm = "MataForm"
var asesmenMedisId = "'.$asesmenMedisId.'"

var palpebra_dexter = "' . $model->palpebra_dexter . '";
var palpebra_sinister = "' . $model->palpebra_sinister . '";
var silia_dexter = "' . $model->silia_dexter . '";
var silia_sinister = "' . $model->silia_sinister . '";
var laksimal_dexter = "' . $model->laksimal_dexter . '";
var laksimal_sinister = "' . $model->laksimal_sinister . '";
var konjungtiva_dexter = "' . $model->konjungtiva_dexter . '";
var konjungtiva_sinister = "' . $model->konjungtiva_sinister . '";
var kornea_dexter = "' . $model->kornea_dexter . '";
var kornea_sinister = "' . $model->kornea_sinister . '";
var bilik_dexter = "' . $model->bilik_dexter . '";
var bilik_sinister = "' . $model->bilik_sinister . '";
var iris_dexter = "' . $model->iris_dexter . '";
var iris_sinister = "' . $model->iris_sinister . '";
var pupil_dexter = "' . $model->pupil_dexter . '";
var pupil_sinister = "' . $model->pupil_sinister . '";
var lensa_dexter = "' . $model->lensa_dexter . '";
var lensa_sinister = "' . $model->lensa_sinister . '";

$(document).ready(function(){
    $("input[name=\'MataForm[palpebra_dexter]\']").val(palpebra_dexter);
    $("input[name=\'MataForm[palpebra_sinister]\']").val(palpebra_sinister);
    $("input[name=\'MataForm[silia_dexter]\']").val(silia_dexter);
    $("input[name=\'MataForm[silia_sinister]\']").val(silia_sinister);
    $("input[name=\'MataForm[laksimal_dexter]\']").val(laksimal_dexter);
    $("input[name=\'MataForm[laksimal_sinister]\']").val(laksimal_sinister);
    $("input[name=\'MataForm[konjungtiva_dexter]\']").val(konjungtiva_dexter);
    $("input[name=\'MataForm[konjungtiva_sinister]\']").val(konjungtiva_sinister);
    $("input[name=\'MataForm[kornea_dexter]\']").val(kornea_dexter);
    $("input[name=\'MataForm[kornea_sinister]\']").val(kornea_sinister);
    $("input[name=\'MataForm[bilik_dexter]\']").val(bilik_dexter);
    $("input[name=\'MataForm[bilik_sinister]\']").val(bilik_sinister);
    $("input[name=\'MataForm[iris_dexter]\']").val(iris_dexter);
    $("input[name=\'MataForm[iris_sinister]\']").val(iris_sinister);
    $("input[name=\'MataForm[pupil_dexter]\']").val(pupil_dexter);
    $("input[name=\'MataForm[pupil_sinister]\']").val(pupil_sinister);
    $("input[name=\'MataForm[lensa_dexter]\']").val(lensa_dexter);
    $("input[name=\'MataForm[lensa_sinister]\']").val(lensa_sinister);
})
', View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
