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

<div class="panel panel-flat">
  <div class="panel-heading">
    <h5 class="panel-title"><?= $title ?></h5>
    <div class="heading-elements">
      <ul class="icons-list">
        <li><a data-action="collapse"></a></li>
      </ul>
    </div>
  </div>
  <div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
      'save' => [
        'attributes' => [
          'form_id' => 'form-pemeriksaan-fisik',
          'id' => 'submit-pemeriksaan-fisik',
        ]
      ],
    ], ''); ?>
  </div>
  <div class="panel-body">
    <div class="col-md-12 mx-auto">
      <?php
      $form = ActiveForm::begin([
        'id' => 'form-pemeriksaan-fisik',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
      ]);
      ?>

      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'berat_badan', [
            'addon' => ['append' => ['content' => 'Kg']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            ]); ?>
          <?= $form->field($modelFisik, 'td_sistolik', [
            'addon' => [
              'append' => ['content' => 'mmHg'],
              'groupOptions' => ['class' => 'input-group-sm'],
              'contentAfter' => '<input type="text" name="PemeriksaanFisikForm[td_diastolik]" value="' . $modelFisik->td_diastolik . '" class="form-control doco-number" placeholder="Diastolik">',
            ],
          ])->textInput(['placeholder' => 'Sistolik', 'class' => 'form-control input-sm doco-number',])->label(Yii::t('fe', 'Tekanan Darah')); ?>
          <?= $form->field($modelFisik, 'detak_nadi', [
            'addon' => ['append' => ['content' => 'x/m']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            ]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'tinggi_badan', [
            'addon' => ['append' => ['content' => 'cm']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            ]); ?>
          <?= $form->field($modelFisik, 'pernafasan', [
            'addon' => ['append' => ['content' => 'x/m']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            ]); ?>
          <?= $form->field($modelFisik, 'suhu', [
            'addon' => ['append' => ['content' => '&#8451;']],
          ])->textInput([
            'class' => 'form-control input-sm doco-decimal',
            ])->label(Yii::t('fe', 'Suhu Tubuh')); ?>
        </div>
      </div>
      <hr />
      <h5><strong>Mata</strong></h5>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'mata_kanan')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'mata_kanan_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'mata_kiri')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'mata_kiri_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <h5><strong>Telinga</strong></h5>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'telinga_kanan')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'telinga_kanan_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'telinga_kiri')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'telinga_kiri_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <h5><strong>Thorax</strong></h5>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'jantung')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'jantung_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'ekg')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'ekg_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'paru')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'paru_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <h5><strong>Abdomen</strong></h5>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'hepar')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'hepar_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'lien')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'lien_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'ginjal')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'ginjal_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <h5><strong>Ektremitas</strong></h5>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'motorik')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'motorik_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'sensorik')->radioList($option, ['inline' => true]); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'sensorik_catatan')->label(Yii::t('fe', 'Catatan')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($modelFisik, 'pemeriksaan_lainnya')->textArea(['rows' => 5]); ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-pemeriksaan-fisik").docoForm('submit', {
      success: function(data) {

      }
    });
  });
</script>