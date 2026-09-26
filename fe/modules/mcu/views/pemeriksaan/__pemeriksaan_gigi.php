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

$dataGigi = [];
foreach ($model->attribute_gigi as $key => $value) {
  if(!strstr($value, 'catatan_' )) {
    $dataGigi['non_catatan'][] = $value;
  }
}
?>

<?php
  $form = ActiveForm::begin([
    'id' => 'form-pemeriksaan-gigi',
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
              'form_id' => 'form-pemeriksaan-gigi', 
              'id' => 'submit-pemeriksaan-gigi'
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
      <h5><strong>ANAMNESIS</strong></h5>
      <div class="row">
        <div class="col-lg-8">
          <?= $form->field($model, 'keluhan')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <h5><strong>PEMERIKSAAN</strong></h5>
          <?php if(isset($dataGigi['non_catatan'])) : ?>
          <?php foreach ($dataGigi['non_catatan'] as $key => $value) : ?>
          <div class="row">
            <div class="col-lg-2">
              <?= $form->field($model, $value)->checkbox([
                'class' => 'check',
                'data-val' => $value
              ])
              ->label($model->getAttributeLabel($value)); ?>
            </div>
            <div class="col-lg-6">
              <?= $form->field($model, 'catatan_' . $value)->textInput([
                'readonly' => true,
              ])->label(false); ?>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        <div class="row">
          <div class="col-lg-8">
            <?= $form->field($model, 'kesimpulan')->textArea([
                'class' => 'form-control input-sm',
            ]); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-8">
            <?= $form->field($model, 'anjuran')->textArea([
                'class' => 'form-control input-sm',
            ]); ?>
          </div>
        </div>
      <?php ActiveForm::end() ?>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-pemeriksaan-gigi").docoForm('submit', {
      success: function(data) {
        $(".btn-cetak-mcu").removeClass("disabled").attr("data-id", data.response.id);
        $("#tab-pemeriksaan-mcu").trigger('click');
      }
    });

    $(".check").each(function(i, obj){
      var dataVal = $(this).attr('data-val');
      if($("#pemeriksaangigiform-" + dataVal).is(":checked")) {
        $("#pemeriksaangigiform-catatan_" + dataVal).prop("readonly", false);
      }
      else {
        $("#pemeriksaangigiform-catatan_" + dataVal).prop("readonly", true);
      }
    });

    $(".check").on("change", function(){
      var dataVal = $(this).attr('data-val');
      if($(this).is(":checked")) {
        $("#pemeriksaangigiform-catatan_" + dataVal).prop("readonly", false);
      }
      else {
        $("#pemeriksaangigiform-catatan_" + dataVal).prop("readonly", true);
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

