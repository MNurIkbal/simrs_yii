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
    'id' => 'form-pemeriksaan-tht',
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
              'form_id' => 'form-pemeriksaan-tht', 
              'id' => 'submit-pemeriksaan-tht'
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
        <div class="col-lg-6">
          <?= $form->field($model, 'keluhan')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <h5><strong>PEMERIKSAAN</strong></h5>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'daun_telinga_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Daun Telinga')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'daun_telinga_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'liang_telinga_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Liang Telinga')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'liang_telinga_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'membran_tympani_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Membran Tympani')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'membran_tympani_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'audiogram_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Audiogram')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'audiogram_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'hidung')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'tenggorokan')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'nasoendoskopi')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'leher')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
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
    $("#form-pemeriksaan-tht").docoForm('submit', {
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

