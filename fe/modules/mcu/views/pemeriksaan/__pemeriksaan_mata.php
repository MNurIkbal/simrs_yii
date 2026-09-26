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
    'id' => 'form-pemeriksaan-mata',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
  ]);
?>


<div class="panel panel-flat">
  <div class="panel-toolbar clearfix">
      <?=DocoHelpers::generateToolbar([
          'save' => ['attributes' => ['form_id' => 'form-pemeriksaan-mata', 'id' => 'submit-pemeriksaan-mata']],
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
          <?= $form->field($model, 'visus_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Visus')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'visus_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'koreksi_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Koreksi')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'koreksi_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'adisi_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Adisi')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'adisi_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'gerakan_mata_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Gerakan Bola Mata')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'gerakan_mata_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'kedudukan_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Kedudukan')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'kedudukan_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'palpebra_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Palpebra')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'palpebra_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'conjuctiva_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Conjuctiva')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'conjuctiva_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'cornea_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Cornea')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'cornea_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'coa_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'COA')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'coa_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'pupil_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Pupil')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'pupil_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'iris_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Iris')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'iris_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'lensa_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Lensa')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'lensa_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'vitreous_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Vitreous')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'vitreous_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'fundus_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'Fundus')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'fundus_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'tio_kanan', [
            'addon' => ['append' => ['content' => 'Kanan']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(Yii::t('fe', 'T.I.O')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'tio_kiri', [
            'addon' => ['append' => ['content' => 'Kiri']],
          ])->textInput([
              'class' => 'form-control input-sm',
          ])->label(""); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'test_buta_warna')->textInput([
              'class' => 'form-control input-sm',
          ]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'lapang_pandang')->textInput([
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
    $("#form-pemeriksaan-mata").docoForm('submit', {
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

