<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>
<div class="panel panel-default long-form">
    <div class="panel-heading">
        <h5 class="panel-title">Asesmen Medis</h5>
    </div>
    <div class="panel-toolbar clearfix sticky">
        <?= DocoHelpers::generateToolbar([
            'custom-save' => [
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'btn-save-asesmen-medis',
                    'disabled' => ($is_perawat) ? true : false,
                ],
            ],
            'custom-print' => [
                'title' => Yii::t('fe', 'Cetak'),
                'icon' => 'fa fa-print',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'btn-print-asesmen-dokter',
                    'disabled' => !empty($model->asesmenmedisrd_id) ? false : true,
                ],
            ],
        ]) ?>
        <span id="draft" class="ml-3" style="color:red;display:none">Draft : anda harus menyimpan terlebih dulu</span>
    </div>
    <div class="panel-body rajal-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-asesmen-medis',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_SMALL,
            ],
        ]);
        ?>
        <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/__asesmen_medis.php', compact('model', 'form', 'arrayConfig', 'data')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/__pemeriksaan_fisik.php', compact('model', 'form', 'arrayConfig', 'data', 'data_bmi')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/__secondary_survey_terbaru.php', compact(
            'model', 'form', 'arrayConfig',
            'radio_button_normal',
            'radio_button_pergerakan',
            'radio_button_ada',
            'radio_button_reguler',
            'radio_button_tidak'
            )); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/__status_lokalis.php', compact('model', 'arrayConfig', 'data', 'pendaftaran_id', 'data_bagiantubuh', 'data_detailbagiantubuh', 'data_anatomiPasien', 'jsonAnatomi', 'counter')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/__pemeriksaan_penunjang.php', compact('model', 'form', 'arrayConfig', 'data', 'optDiagnosa')); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJsFile(
    '/js/jquery.mask.min.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ]
    ]
);
$jsonBagianTubuh = json_encode(ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'));
$jsonDetailBagianTubuh = json_encode($data_detailbagiantubuh);
$this->registerJs("
    var data = " . json_encode($data) . "

    $(document).ready(function(){
        addRow();
        $('#form-asesmen-medis :input').prop('disabled', ".$status_update.");
        $('#btn-save-asesmen-medis, .data-reset').prop('disabled', ".$status_update.");
    });

    var tabel_anggotatubuh = $('.tabel-anggotatubuh').DataTable({
        filter: false,
        bLengthChange: false,
        bInfo: false,
        processing: true,
        paging: false,
    });

    // define data master map
    var tmpData = " . $jsonAnatomi . "
    var bagianTubuh = " . $jsonBagianTubuh . "
    var detailBagianTubuh = " . $jsonDetailBagianTubuh . "
    var counter = " . $counter . "
    
    // define data master bmi
    var data_bmi = " . $data_bmi . ";
    // define data pasien jeniskelamin
    var jeniskelamin = '" . $jeniskelamin . "';
    var is_perawat = " . $is_perawat . ";
    var is_draft = " . $is_draft .";

", View::POS_END, 'js2');
$this->registerJs($this->render('__form.js'), View::POS_END, 'js')
?>
