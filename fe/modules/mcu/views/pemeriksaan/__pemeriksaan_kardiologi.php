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
    'id' => 'form-pemeriksaan-kardiologi',
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
              'form_id' => 'form-pemeriksaan-kardiologi', 
              'id' => 'submit-pemeriksaan-kardiologi'
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
      <h6><strong>Leher</strong></h6>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'tumor')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Tumor')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'jvp')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'JVP')); ?>
        </div>
      </div>
      <h6><strong>Thoraks</strong></h6>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'paru')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Paru')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'jantung')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Jantung')); ?>
        </div>
      </div>
      <h6><strong>Perut</strong></h6>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'hati')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Hati')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'limpa')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Limpa')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'lainnya')->textArea([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Lainnya')); ?>
        </div>
      </div>
      <h6><strong>Extremitas</strong></h6>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'edema')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Edema')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'rontgen_thorax')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>Rontgen Thorax</strong></h6>')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'ekg')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>EKG</strong></h6>')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'treadmill')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>TREADMILL</strong></h6>')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'echocardiografi')->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>Echocardiografi</strong></h6>')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'kesimpulan', [
                'labelOptions' => ['style' => 'font-weight:bold;font-size:15px;']
              ])->textArea([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>KESIMPULAN</strong></h6>')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'anjuran', [
                'labelOptions' => ['style' => 'font-weight:bold;font-size:15px;']
              ])->textArea([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', '<h6><strong>ANJURAN</strong></h6>')); ?>
        </div>
      </div>
      <?php ActiveForm::end() ?>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-pemeriksaan-kardiologi").docoForm('submit', {
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

