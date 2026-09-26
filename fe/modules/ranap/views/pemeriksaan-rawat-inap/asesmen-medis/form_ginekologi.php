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
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<br>
<div class="row">
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/data_subjektif',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/riwayat_alergi',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/riwayat_status',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/data_objektif',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/penilaian_tingkat_nyeri',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/penilaian_resiko_jatuh',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/penilaian_risiko_dekubitus',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/penilaian_status_fungsional',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/skrining_nutrisi',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/kebutuhan_edukasi',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/skrining_faktor_risiko',[
        'form' => $form,
        'model' => $model,
    ]); ?>
    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/ginekologi/identifikasi_kebutuhan',[
        'form' => $form,
        'model' => $model,
    ]); ?>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
var isDokumenEklaim = "' . $isDokumenEklaim . '";
var asesmenMedisId = "' . $asesmenMedisId . '";
var _modelIdForm = "ginekologiform";

', View::POS_END);

$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>