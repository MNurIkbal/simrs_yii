<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>

<style>
    .datepicker>div {
        display: block;
    }

    .form-row {
        margin-bottom: 4px;
    }

    .panel-heading {
        padding: 3px 20px !important;
    }

    hr {
        margin-top: 3px;
        margin-bottom: 3px;
    }

    .panel {
        margin-bottom: 10px;
    }

    .box-scale {
        margin-top: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    p {
        margin-bottom: 3px;
    }

    td {
        padding-top: 0px !important;
    }

    .form-horizontal .control-label {
        padding-bottom: 3px;
        padding-top: 3px !important;
    }

    .box-scale-header {
        margin-bottom: 3px !important;
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-md-8">
                        <?= $form->field($model, 'usia_gestasi', ['addon' => ['append' => ['content' => 'Minggu']]])->textInput(['class' => 'form-control input-sm doco-number']); ?>
                    </div>
                    <div class="col-md-8">
                        <?= $form->field($model, 'diagnosa_medis')->label()->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
            <hr>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/alasan_masuk',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/riwayat_kesehatan',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/riwayat_alergi',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/riwayat_status',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/keadaan_umum',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/pemeriksaan_fisik',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/penilaian_nyeri',[
                'form' => $form,
                'model' => $model,
                'asesmenMedisId' => $asesmenMedisId,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/kebutuhan_edukasi',[
                'form' => $form,
                'model' => $model,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/neonatus/pemulangan_pasien',[
                'form' => $form,
                'model' => $model,
            ]); ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
var isDokumenEklaim = "' . $isDokumenEklaim . '";
var asesmenMedisId = "' . $asesmenMedisId . '";

', View::POS_END);

$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>