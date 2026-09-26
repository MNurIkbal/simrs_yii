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
use dosamigos\ckeditor\CKEditor;
?>

<!-- Status Gizi & Objective -->
<div class="row form-row">

    <!-- Objective -->
    <div class="col-12 col-sm-6 col-md-6">
      <div class="panel panel-default">
          <div class="panel-heading">
            <h5 class="panel-title">Objective</h5>
          </div>
          <div class="panel-body">
            <div class="col-sm-12">
              <?=
                  $form->field($model, 'objective', [
                      'horizontalCssClasses' => [
                          'label' => 'text-left control-label col-sm-2',
                          'wrapper' => 'col-md-12'
                      ],
                  ])->widget(CKEditor::className(), [
                      'options' => ['rows' => 20],
                      'preset' => 'basic',
                      'clientOptions' => [
                          'extraPlugins' => '',
                      ]
                  ])->label(false);
              ?>

            </div>
          </div>
      </div>
    </div>

    <!-- Status Gizi -->
    <div class="col-12 col-sm-6 col-md-6">
      <div class="panel panel-default">
          <div class="panel-heading">
            <h5 class="panel-title">Status Gizi</h5>
          </div>
          <div class="panel-body">
            <div class="col-sm-12">
                <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-decimal-wcomma imt_field']) ?>
                <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-decimal-wcomma imt_field']) ?>
                <?=$form->field($model, 'bb_ideal', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'form-control input-sm berat-badan doco-decimal-wcomma', 'readonly' => 'readonly',]); ?>
                <?= $form->field($model, 'imt', ['addon' => ['append' => ['content' => 'kg/m2']]])->textInput(['class' => 'doco-decimal-wcomma', 'disabled' => true]) ?>
                <?=$form->field($model, 'imt_kategori', [])->textInput(['class' => 'form-control input-sm', 'readonly' => 'readonly',]); ?>
            </div>
          </div>
      </div>
    </div>
</div>
