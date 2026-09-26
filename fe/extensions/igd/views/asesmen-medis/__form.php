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

<style media="screen">
  .btn-resusitasi{
    border-color: #FD2A25 !important;
    background-color: #fff !important;
    color: #FD2A25 !important;
    margin: 6px 4px;
  }
  .btn-resusitasi:hover, .btn-resusitasi.btn-triage--active{
    color: #fff !important;
    background-color: #FD2A25 !important;
  }

  .btn-emergent{
    border-color: #FF661F !important;
    background-color: #fff !important;
    color: #FF661F !important;
    margin: 6px 4px;
  }
  .btn-emergent:hover, .btn-emergent.btn-triage--active{
    color: #fff !important;
    background-color: #FF661F !important;
  }

  .btn-urgent{
    border-color: #558E3D !important;
    background-color: #fff !important;
    color: #558E3D !important;
    margin: 6px 4px;
  }
  .btn-urgent:hover, .btn-urgent.btn-triage--active{
    color: #fff !important;
    background-color: #558E3D !important;
  }

  .btn-non_urgent{
    border-color: #46C8FF !important;
    background-color: #fff !important;
    color: #46C8FF !important;
    margin: 6px 4px;
  }
  .btn-non_urgent:hover, .btn-non_urgent.btn-triage--active{
    color: #fff !important;
    background-color: #46C8FF !important;
  }

  .btn-false_emergency{
    border-color: #606060 !important;
    background-color: #fff !important;
    color: #606060 !important;
    margin: 6px 4px;
  }
  .btn-false_emergency:hover, .btn-false_emergency.btn-triage--active{
    color: #fff !important;
    background-color: #606060 !important;
  }


</style>

<div class="panel panel-default long-form">
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
        <?= Yii::$app->controller->renderPartial('@app/extensions/igd/views/asesmen-medis/partials/__asesmen_medis.php', compact('model', 'form', 'arrayConfig', 'data', 'data_pasien')); ?>
        <?= Yii::$app->controller->renderPartial('@app/extensions/igd/views/asesmen-medis/partials/__anamnesis.php', compact('model', 'form', 'arrayConfig', 'data')); ?>
        <?= Yii::$app->controller->renderPartial('@app/extensions/igd/views/asesmen-medis/partials/__pemeriksaan_fisik.php', compact('model', 'form', 'arrayConfig', 'data', 'data_bmi')); ?>
        <?= Yii::$app->controller->renderPartial('@app/extensions/igd/views/asesmen-medis/partials/__status_lokalis.php', compact('model', 'arrayConfig', 'data', 'pendaftaran_id', 'data_bagiantubuh', 'data_detailbagiantubuh', 'data_anatomiPasien', 'jsonAnatomi', 'counter')); ?>
        <?= Yii::$app->controller->renderPartial('@app/extensions/igd/views/asesmen-medis/partials/__pemeriksaan_penunjang.php', compact('model', 'form', 'arrayConfig', 'data', 'opt_diagnosa_primary', 'opt_diagnosa_secondary', 'diagnosa_primary_text', 'diagnosa_secondary_text', 'is_perawat')); ?>
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
    var optDiagnosaSecondary = ".json_encode($opt_diagnosa_secondary).";

", View::POS_END, 'js2');
$this->registerJs($this->render('@app/extensions/igd/views/asesmen-medis/__form.js'), View::POS_END, 'js')
?>
