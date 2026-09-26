<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

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
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
      ]);
      ?>
      <div class="flex-container">
        <div class="flex-50">
          <?= $form->field($model, 'keluhan'); ?>
          <h5><strong>Riwayat Penyakit Terdahulu</strong></h5>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_diderita')->radioList($option, [
                'inline' => true, 'class' => 'riwayat_diderita']); ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_diderita_catatan')->label(false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_alergi')->radioList($option, ['inline' => true]); ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_alergi_catatan')->label(false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_dirawat_rs')->radioList($option, ['inline' => true]); ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_dirawat_rs_catatan')->label(false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_operasi')->radioList($option, ['inline' => true]); ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_operasi_catatan')->label(false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_imunisasi')->radioList($option, ['inline' => true]); ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'riwayat_imunisasi_catatan')->label(false); ?>
            </div>
          </div>
          
          <h5><strong>Khusus Perempuan</strong></h5>

          <?= $form->field($model, 'menstruasi'); ?>
          <?= $form->field($model, 'riwayat_kontrasepsi'); ?>
          <?= $form->field($model, 'riwayat_melahirkan'); ?>
          <?= $form->field($model, 'riwayat_keguguran'); ?>
          <?= $form->field($model, 'sedang_hamil'); ?>
          <?= $form->field($model, 'riwayat_pap_smear'); ?>
          <?= $form->field($model, 'riwayat_penyakit_keluarga'); ?>

          <h5><strong>Kebiasaan</strong></h5>

          <?= $form->field($model, 'rokok')->radioList($option, ['inline' => true]); ?>
          <?= $form->field($model, 'alkohol')->radioList($option, ['inline' => true]); ?>
          <?= $form->field($model, 'kopi')->radioList($option, ['inline' => true]); ?>
          <?= $form->field($model, 'olahraga'); ?>
          <?= $form->field($model, 'diet')->radioList($optionDiet, ['inline' => true]); ?>
          <?= $form->field($model, 'tidur')->radioList($optionTidur, ['inline' => true]); ?>
          <?= $form->field($model, 'obat_rutin'); ?>

        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    setRadio('riwayat_diderita', 'riwayat_diderita_catatan');
    setRadio('riwayat_alergi', 'riwayat_alergi_catatan');
    setRadio('riwayat_dirawat_rs', 'riwayat_dirawat_rs_catatan');
    setRadio('riwayat_operasi', 'riwayat_operasi_catatan');
    setRadio('riwayat_imunisasi', 'riwayat_imunisasi_catatan');

    function setRadio(name, id) {
      var attributeName = document.getElementsByName('RiwayatPenyakitForm['+name+']');
      for (var i = 0, length = attributeName.length; i < length; i++) {
        if (attributeName[i].checked) {
          if(attributeName[i].value == 0) {
            $('#riwayatpenyakitform-'+id+'').prop("readonly", true);
          }
          break;
        }
      }
    }

    $(function() {
      $('input:radio[name="RiwayatPenyakitForm[riwayat_diderita]"]').change(function() {
          if ($(this).val() == 0) {
              $('#riwayatpenyakitform-riwayat_diderita_catatan').prop("readonly", true);
          } else {
              $('#riwayatpenyakitform-riwayat_diderita_catatan').prop("readonly", false);
          }
      });
      $('input:radio[name="RiwayatPenyakitForm[riwayat_alergi]"]').change(function() {
          if ($(this).val() == 0) {
              $('#riwayatpenyakitform-riwayat_alergi_catatan').prop("readonly", true);
          } else {
              $('#riwayatpenyakitform-riwayat_alergi_catatan').prop("readonly", false);
          }
      });
      $('input:radio[name="RiwayatPenyakitForm[riwayat_dirawat_rs]"]').change(function() {
          if ($(this).val() == 0) {
              $('#riwayatpenyakitform-riwayat_dirawat_rs_catatan').prop("readonly", true);
          } else {
              $('#riwayatpenyakitform-riwayat_dirawat_rs_catatan').prop("readonly", false);
          }
      });
      $('input:radio[name="RiwayatPenyakitForm[riwayat_operasi]"]').change(function() {
          if ($(this).val() == 0) {
              $('#riwayatpenyakitform-riwayat_operasi_catatan').prop("readonly", true);
          } else {
              $('#riwayatpenyakitform-riwayat_operasi_catatan').prop("readonly", false);
          }
      });
      $('input:radio[name="RiwayatPenyakitForm[riwayat_imunisasi]"]').change(function() {
          if ($(this).val() == 0) {
              $('#riwayatpenyakitform-riwayat_imunisasi_catatan').prop("readonly", true);
          } else {
              $('#riwayatpenyakitform-riwayat_imunisasi_catatan').prop("readonly", false);
          }
      });
    });
    $("#form-riwayat-penyakit").docoForm('submit', {
      success: function(data) {

      }
    });
  });
</script>