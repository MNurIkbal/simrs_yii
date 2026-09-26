<?php

use yii\web\View;
use kartik\widgets\ActiveForm;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:800px;margin-top:10px';
$dateFormat = "yyyy-mm-dd";
?>

<style>
    .datepicker>div {
        display: block;
    }

    .box-scale {
        margin-top: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .box-scale-header {
        margin-bottom: 3px !important;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }

    .input-margin {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .table-vaksinasi {
        width: 100%;
        border-collapse: collapse;
    }

    .table-vaksinasi th,
    .table-vaksinasi td {
        border: 1px solid #000;
        padding: 8px;
    }

    .table-vaksinasi th {
        background-color: #f2f2f2;
    }
    .table-vaksinasi tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .table-vaksinasi tr:hover {
        background-color: #f1f1f1;
    }
    .table-vaksinasi input[type="checkbox"] {
        cursor: pointer;
    }

    .modal-open .modal {
        overflow-y: hidden !important;
    }
    .modal-body {
        height: 100%;
        max-height: 600px;
        overflow-y: auto;
    }
    
</style>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);

$diagnosaKode = $model->diagnosaKode;
$diagnosaNama = $model->diagnosaNama;
if (is_array($model->diagnosaNama)) {
    $model->diagnosaNama = null;
}
if (is_array($model->diagnosaKode)) {
    $model->diagnosaKode = null;
}

?>

<br>
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row" style="margin-bottom:10px;">
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/kesehatan-anak/pemeriksaan',[
                        'form' => $form,
                        'model' => $model,
                    ]); ?>
                    <br>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/kesehatan-anak/lingkar',[
                        'form' => $form,
                        'model' => $model,
                    ]); ?>
                    <br>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/kesehatan-anak/riwayat-penyakit',[
                        'form' => $form,
                        'model' => $model,
                    ]); ?>
                    <br>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/kesehatan-anak/informasi-anak',[
                        'form' => $form,
                        'model' => $model,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
var isDokumenEklaim = "' . $isDokumenEklaim . '";
var asesmenMedisId = "' . $asesmenMedisId . '";
var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"

var _lingkarKepalaTanggal = '.json_encode($model->lingkarKepalaTanggal).'
var _lingkarKepalaUkuran = '.json_encode($model->lingkarKepalaUkuran).'
var _lingkarDadaTanggal = '.json_encode($model->lingkarDadaTanggal).'
var _lingkarDadaUkuran = '.json_encode($model->lingkarDadaUkuran).'
var _lingkarPerutTanggal = '.json_encode($model->lingkarPerutTanggal).'
var _lingkarPerutUkuran = '.json_encode($model->lingkarPerutUkuran).'
var _diagnosaNama = '.json_encode($diagnosaNama).'
var _diagnosaKode = '.json_encode($diagnosaKode).'

var _sex = '.json_encode($model->sex).'
var _umur = '.json_encode($model->umur).'
var _sehatSakit = '.json_encode($model->sehatSakit).'
var _karena = '.json_encode($model->karena).'

', View::POS_END);

$this->registerJs($this->render('js/kesehatan-anak.js'), View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
