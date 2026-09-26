<?php

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
?>

<?php
  $form = ActiveForm::begin([
    'id' => 'form-pemeriksaan-obsgyn',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
  ]);
?>

<div class="panel panel-flat">
  <div class="panel-toolbar clearfix">
      <?=DocoHelpers::generateToolbar([
          'save' => [
            'attributes' => [
              'form_id' => 'form-pemeriksaan-obsgyn', 
              'id' => 'submit-pemeriksaan-obsgyn'
            ]
          ],
          'cetak-mcu' => [
              'type' => 'button',
              'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
              'icon' => 'fa fa-print',
              'method' => '#',
              'attributes' => [
                  'class'=> ($id == '') ? 'disabled btn-cetak-mcu' : 'btn-cetak-mcu',
                  'data-target' => Url::to(['cetak-pemeriksaan-mcu']).'?id=',
                  'data-id' => $id,
                  'data-options'=>'click'
              ]
          ],
      ]);?>
  </div>
  <?= Yii::$app->controller->renderPartial('__komponent_pemeriksaan', [
    'form' => $form,
    'model' => $model,
  ]); ?>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', $title)?></h5>
    </div>
    <div class="panel-body">
      <h5><strong>ANAMNESA</strong></h5>
      <?php foreach ($model->attribute_obsgyn as $key => $value) : ?>
        <div class="row">
          <div class="col-lg-6">
            <?= $form->field($model, $value)->textInput(); ?>
          </div>
        </div>
        <?php if($value == 'lainlain') : ?>
          <h5><strong>RIWAYAT OBSTETRI</strong></h5>
          <div class="row">
            <div class="col-lg-6">
              <?= $form->field($model, 'anak_hidup')->textInput(); ?>
            </div>
            <div class="col-lg-6">
              <?= $form->field($model, 'prematur')->textInput(); ?>
            </div>
          </div>
          <h5><strong>PEMERIKSAAN</strong></h5>
        <?php endif; ?>
      <?php endforeach; ?>
        <div class="row">
            <div class="col-lg-6">
              <?= $form->field($model, 'kesimpulan', [
                'labelOptions' => ['style' => 'font-weight:bold;font-size:15px;']
              ])->textArea([
                  'class' => 'form-control input-sm',
              ]); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <?= $form->field($model, 'anjuran', [
                'labelOptions' => ['style' => 'font-weight:bold;font-size:15px;']
              ])->textArea([
                  'class' => 'form-control input-sm',
              ]); ?>
            </div>
          </div>
      <?php ActiveForm::end() ?>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-pemeriksaan-obsgyn").docoForm('submit', {
      success: function(data) {
        $(".btn-cetak-mcu").removeClass("disabled").attr("data-id", data.response.id);
        $("#tab-pemeriksaan-mcu").trigger('click');
      }
    });

    $(".btn-cetak-mcu").on("click", function(){
        var url = window.location.origin;
        var _target = $(this).attr("data-target");
        var _id = $(this).attr("data-id");

        if(typeof _id != "undefined"){
            window.open(url+_target+_id);
        }
    })
  });
</script>

