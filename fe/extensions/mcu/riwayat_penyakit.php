<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 17:31:57
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-01 09:14:35
 * @Description: 
 */

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
          'form_id' => 'form-riwayat-penyakit',
          'id' => 'submit-riwayat-penyakit',
        ]
      ],
    ], ''); ?>
  </div>
  <div class="panel-body">
    <div class="row">
      <?php
      $form = ActiveForm::begin([
        'id' => 'form-riwayat-penyakit',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
      ]);
      ?>
      <div class="container">
        <p>
          <h4>Riwayat Penyakit Terdahulu</h4>
        </p>
        <div class="row" style="margin: 0 2rem;margin-top: 5px;">
          <?php $no = 1;
          foreach ($masterPenyakit as $key => $value) :
            $defaultValue = isset($value['flag']) ? $value['flag'] : 0;
          ?>
            <div class="col-md-4">
              <label for=""><?= $no++ . '. ' . $value['riwayat_nama'] ?></label>
            </div>

            <?php if ($value["riwayat_id"] != $default_lain_lain) : ?>
              <div class="col-md-8">
                <?= Html::radioList('RiwayatPenyakitKramatForm[riwayat][' . $value["riwayat_nama"] . ']', $defaultValue, [1 => 'Ya', 0 => 'Tidak']); ?>
              </div>
            <?php else : ?>
              <div class="col-md-4">
                <?= Html::activeInput('text', $model, 'riwayat[' . $value['riwayat_nama'] . ']', [
                  'class' => 'form-control',
                  'value' => $riwayat_lainnya
                ]); ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#form-riwayat-penyakit").docoForm('submit', {
      success: function(data) {}
    });
  });
</script>