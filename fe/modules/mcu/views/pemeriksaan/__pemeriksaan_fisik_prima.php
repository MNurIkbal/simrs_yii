<?php

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use app\components\DocoConstants;
use app\widgets\DHAnatomiWidget;

$classForm = 'form-control input-sm';

$this->registerCss('
  .radio,
  .checkbox {
      display: block;
      min-height: @line-height-computed;
      label {
          display: inline;
          font-weight: normal;
          cursor: pointer;
      }
  }.col-header {
      margin-top: 5px;
  }.col-glasgow {
      margin-bottom: 0px;
      padding-bottom: 50px;
  }.col-kesadaran {
      margin-top: 20px;
  }.col-thoraks {
      padding-bottom: 65px;
  }.input-data {
      width: 65%;
  }.detail-ui {
      width: 30%;
  }.detail {
      padding-right: 0px;
  }.add-caption-gigi{
    text-transform: lowercase;
  }.select2-results__options{
    height:130px;
  }.badge{
    padding : 1px 5px 1px 5px;
    font-size: 9px;
    letter-spacing: 0px;
    border: 0px;
  }
')
?>

<div class="panel panel-flat">
  <div class="panel-heading">
      <div class="row">
          <div class="col-md-7">
              <h5 class="panel-title"><?= $title ?></h5>
          </div>
          <div class="col-md-4">
              <?= Html::dropDownList('template_id', '', array(),
                  [
                      'id'  => 'list-pemeriksaan-fisik',
                      'class' => 'form-control select2  input-sm',
                      'prompt' => Yii::t('fe', '-- Pilih Template --'),
                  ]
              ); ?>
          </div>
          <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-info" id="pilih-pemeriksaan-fisik">Pilih</button>
          </div>
      </div>
  </div>
  <div class="panel-toolbar clearfix">
    <div class="col-md-8">
      <?= DocoHelpers::generateToolbar([
        'save' => [
          'attributes' => [
            'form_id' => 'form-pemeriksaan-fisik-prima',
            'id' => 'submit-pemeriksaan-fisik-prima',
          ]
        ],
      ], ''); ?>
    </div>
    <div class="col-md-4 template" style="text-align:right;">
      <button  type="button" class="btn btn-sm btn-danger" id="hapus-template-pemeriksaan-fisik" disabled>
      <li class="fa fa-trash"></li> Hapus Template</button>
      <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
          'class'       => 'btn btn-primary save-template btn-sm',
          'id'          => 'save-template-pemeriksaan-fisik',
          'data-toggle' => 'modal',
          'data-target' => '#modal_backdrop',
          'action'      => '/mcu/pemeriksaan/modal-template?id='.$pendaftaran_id.'&type=pemeriksaan_fisik&modal=is_modal',true,
          'disabled'    => 'disabled'
      ]); ?>
    </div>
  </div>
  <div class="panel-body">
    <?php
      $form = ActiveForm::begin([
        'id' => 'form-pemeriksaan-fisik-prima',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
      ]);
    ?>
    <!-- Pemeriksaan Fisik -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_pemeriksaan_fisik',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
    ]); ?>
    <!-- Keadaan Umum & Kelenjar Getah Bening -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_keadaan_umum',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_yesorno' => $option_yesorno,
      'option_leher' => $option_leher,
      'classForm' => $classForm,
    ]); ?>
    <!-- Kepala & Syaraf -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_kepala',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- provokasi radiks cervical & provokasi radiks lumbar -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_provokasi',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_cervical' => $option_cervical,
    ]); ?>
    <!-- Mata -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_mata',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'classForm' => $classForm,
      'buta_parsial' => $buta_parsial,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- Telinga -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_telinga',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- Hidung & Tenggorokan -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_hidung',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- Mulut -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_mulut',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- Gigi -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_gigi',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'optBagianTubuhGigi' => $optBagianTubuhGigi,
      'dataAnatomiGigi' => $dataAnatomiGigi,
      'counterGigi' => $counterGigi,
    ]); ?>
    <!-- Leher & Thorax -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_leher',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
    ]); ?>
    <!-- Paru -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_paru',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
    ]); ?>
    <!-- Jantung -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_jantung',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
    ]); ?>
    <!-- Abdomen & Ekstremitas -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_abdomen',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'option_adadantiada' => $option_adadantiada,
      'option_leher' => $option_leher,
      'optBagianTubuhabdomen' => $optBagianTubuhabdomen,
      'dataAnatomiAbdomen' => $dataAnatomiAbdomen,
      'counterAbdomen' => $counterAbdomen,
      'option_range_of_motion' => $option_range_of_motion,
      'option_extremitas' => $option_extremitas,
    ]); ?>
    <!-- Kulit -->
    <?= Yii::$app->controller->renderPartial('partials_prima/_kulit',[
      'form' => $form,
      'modelFisikNew' => $modelFisikNew,
      'optBagianTubuh' => $optBagianTubuh,
      'dataAnatomi' => $dataAnatomi,
      'counter' => $counter,
    ]); ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-pemeriksaan-fisik-prima").docoForm('submit', {
      success: function(data) {
      }
    });
  });
</script>

<?php
    $jsonBagianTubuh = json_encode(ArrayHelper::map($optBagianTubuh, 'bagiantubuh_id', 'namabagtubuh'));
    $jsonBagianTubuhGigi = json_encode(ArrayHelper::map($optBagianTubuhGigi, 'bagiantubuhdetail_id', 'nama_bagiantubuh'));
    $jsonBagianTubuhAbdomen = json_encode(ArrayHelper::map($optBagianTubuhabdomen, 'bagiantubuhdetail_id', 'nama_bagiantubuh'));
    $this->registerJs('
    var pendaftaran_decrypt = '.$pendaftaran_decrypt.';
    var pasien_id = '.$pasien_id.';
    var type = "pemeriksaan-fisik";
    var id_form = "form-pemeriksaan-fisik-prima";
    var detail_type = "pemeriksaan_fisik";
    var id = '.$pendaftaran_decrypt.';
    var tab = "tab-pemeriksaan-fisik";
  '.$this->render('js/__pemeriksaan_fisik_prima.js'), View::POS_END);
$this->registerJs($this->render('js/_template.js'), View::POS_END);
?>
