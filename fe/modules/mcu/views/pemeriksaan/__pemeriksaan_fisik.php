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
use app\components\DocoConstants;
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
        // 'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
      ]);
      ?>

      <div class="row">
        <?=Html::activeHiddenInput($modelFisik, 'pendaftaran_id')?>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'berat_badan', [
            'addon' => ['append' => ['content' => 'Kg']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number imt_field',
            'tabindex' => '1'
            ]); ?>
          <?= $form->field($modelFisik, 'td_sistolik', [
            'addon' => [
              'append' => ['content' => 'mmHg'],
              'groupOptions' => ['class' => 'input-group-sm'],
              'contentAfter' => '<input type="text" id="form-td_diastolik" name="PemeriksaanFisikDefaultForm[td_diastolik]" value="' . $modelFisik->td_diastolik . '" class="form-control doco-number td_field" tabindex="3" placeholder="Diastolik">',
            ],
          ])->textInput(['placeholder' => 'Sistolik', 'class' => 'form-control input-sm doco-number td_field','tabindex' => '3'])->label(Yii::t('fe', 'Tekanan Darah (Sistolik dan Diastolik)')); ?>
          <?= $form->field($modelFisik, 'detak_nadi', [
            'addon' => ['append' => ['content' => 'x/m']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            'tabindex' => '6'
            ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'tinggi_badan', [
            'addon' => ['append' => ['content' => 'cm']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number imt_field',
            'tabindex' => '2'
            ]); ?>
          <?= $form->field($modelFisik, 'pernafasan', [
            'addon' => ['append' => ['content' => 'x/m']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
            'tabindex' => '5'
            ]); ?>
          <?= $form->field($modelFisik, 'suhu', [
            'addon' => ['append' => ['content' => '&#8451;']],
          ])->textInput([
            'class' => 'form-control input-sm doco-decimal-wcomma',
            'tabindex' => '7'
            ])->label(Yii::t('fe', 'Suhu Tubuh')); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'imt', [])->textInput([
            'class' => 'form-control input-sm doco-decimal',
            'readonly'=>true
            ]); ?>
          <?= $form->field($modelFisik, 'kategori_bb', [])->textInput([
            'class' => 'form-control input-sm',
            'readonly'=>true
            ]); ?>
          <?= $form->field($modelFisik, 'td_kategori', [])->textInput([
            'class' => 'form-control input-sm doco-decimal hasil-td',
            'readonly'=>true
            ]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'kulit')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kulit">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kulit">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true, 
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_kulit')->textInput([
            'class' => 'form-control input-sm note_kulit',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'limfonodi')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_limfonodi">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_limfonodi">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_limfonodi')->textInput([
            'class' => 'form-control input-sm note_limfonodi',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'kepala_rambut')->radioList($option_kepala_rambut, [
            'inline' => true
          ]); ?>
        </div>
      </div>
      <!-- <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'tato')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_tato">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_tato">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_tato')->textInput([
            'class' => 'form-control input-sm note_tato',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'tindik')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_tindik">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_tindik">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_tindik')->textInput([
            'class' => 'form-control input-sm note_tindik',
            ])->label(false); ?>
        </div>
      </div> -->
      <h5><strong>Mata</strong></h5>
      <hr />
      <div class="row">
        <p>Visus Dengan Kaca Mata</p>
        <div class="col-md-3">
          <?= $form->field($modelFisik, 'visus_dengan_kaca_mata_kanan')->label(Yii::t('fe', 'Kanan')); ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($modelFisik, 'visus_dengan_kaca_mata_kiri')->label(Yii::t('fe', 'Kiri')); ?>
        </div>
      </div>
      <div class="row">
        <p>Koreksi</p>
        <div class="col-md-3">
          <?= $form->field($modelFisik, 'koreksi_kanan')->label(Yii::t('fe', 'Kanan')); ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($modelFisik, 'koreksi_kiri')->label(Yii::t('fe', 'Kiri')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'buta_warna')->radioList($option_yesorno, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'kelopak_mata')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelopak_mata">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelopak_mata">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_kelopak_mata')->textInput([
            'class' => 'form-control input-sm note_kelopak_mata',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'kanjungtiva')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kanjungtiva">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kanjungtiva">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ])->label(Yii::t('fe', 'Konjungtiva')); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_kanjungtiva')->textInput([
            'class' => 'form-control input-sm note_kanjungtiva',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'sklera')->radioList($option_sklera, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'pupil')->radioList($option_pupil, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'gerakan_bola_mata')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_gerakan_bola_mata">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_gerakan_bola_mata">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_gerakan_bola_mata')->textInput([
            'class' => 'form-control input-sm note_gerakan_bola_mata',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'lensa_mata')->radioList($option_lensa, ['inline' => true]); ?>
        </div>
      </div>
      <h5><strong>Telinga</strong></h5>
      <hr />
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'kelainan_daun_telinga')->radioList($option_adadantiada, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelainan_daun_telinga">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelainan_daun_telinga">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ])->label(Yii::t('fe', 'Kelainan Daun Telinga')); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_kelainan_daun_telinga')->textInput([
            'class' => 'form-control input-sm note_kelainan_daun_telinga',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'serumen_prop')->radioList($option_adadantiada, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_serumen_prop">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_serumen_prop">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_serumen_prop')->textInput([
            'class' => 'form-control input-sm note_serumen_prop',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'liang_telinga_luar')->radioList($option_liang_telinga, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'membran_timpani')->radioList($option_timpani, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_membran_timpani">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_membran_timpani">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_membran_timpani')->textInput([
            'class' => 'form-control input-sm note_membran_timpani',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'hidung')->radioList($option_hidung, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_hidung">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_hidung">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_hidung')->textInput([
            'class' => 'form-control input-sm note_hidung',
            ])->label(false); ?>
        </div>
      </div>
      <h5><strong>Mulut dan Tenggorokan</strong></h5>
      <hr />
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'gigi')->radioList($option_gigi, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'faring')->radioList($option_faring, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'tonsil')->radioList($option_tonsil, ['inline' => true]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'tonsil_text')->label(Yii::t('fe', '')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'kelenjar_tiroid')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelenjar_tiroid">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelenjar_tiroid">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_kelenjar_tiroid')->textInput([
            'class' => 'form-control input-sm note_kelenjar_tiroid',
            ])->label(false); ?>
        </div>
      </div>
      <h5><strong>PARU-PARU</strong></h5>
      <hr />
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'inspeksi_paru')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_inspeksi_paru">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_inspeksi_paru">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_inspeksi_paru')->textInput([
            'class' => 'form-control input-sm note_inspeksi_paru',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'palpasi_paru')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_palpasi_paru">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_palpasi_paru">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_palpasi_paru')->textInput([
            'class' => 'form-control input-sm note_palpasi_paru',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'perkusi_paru')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_perkusi_paru">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_perkusi_paru">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_perkusi_paru')->textInput([
            'class' => 'form-control input-sm note_perkusi_paru',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'auskultasi_paru')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_auskultasi_paru">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_auskultasi_paru">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_auskultasi_paru')->textInput([
            'class' => 'form-control input-sm note_auskultasi_paru',
            ])->label(false); ?>
        </div>
      </div>
      <h5><strong>Jantung</strong></h5>
      <hr />
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'ictus_cordis')->radioList($option_ictus, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'batas_jantung')->radioList($option_batas_jantung, ['inline' => true]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= $form->field($modelFisik, 'irama')->radioList($option_irama, ['inline' => true]); ?>
        </div>
      </div>
      <!-- <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'bunyi_jantung')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_bunyi_jantung">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_bunyi_jantung">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_bunyi_jantung')->textInput([
            'class' => 'form-control input-sm note_bunyi_jantung',
            ])->label(false); ?>
        </div>
      </div> -->
      <h5><strong>Abdomen</strong></h5>
      <hr />
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'inspeksi_abdomen')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_inspeksi_abdomen">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_inspeksi_abdomen">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_inspeksi_abdomen')->textInput([
            'class' => 'form-control input-sm note_inspeksi_abdomen',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'auskultasi_abdomen')->radioList($option_auskultasi, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_auskultasi_abdomen">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_auskultasi_abdomen">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_auskultasi_abdomen')->textInput([
            'class' => 'form-control input-sm note_auskultasi_abdomen',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'perkusi_abdomen')->radioList($option_perkusi, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_perkusi_abdomen">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_perkusi_abdomen">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_perkusi_abdomen')->textInput([
            'class' => 'form-control input-sm note_perkusi_abdomen',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'palpasi_abdomen')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_palpasi_abdomen">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_palpasi_abdomen">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_palpasi_abdomen')->textInput([
            'class' => 'form-control input-sm note_palpasi_abdomen',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'rectal_toucher')->radioList($option_toucher, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_rectal_toucher">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_rectal_toucher">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_rectal_toucher')->textInput([
            'class' => 'form-control input-sm note_rectal_toucher',
            ])->label(false); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($modelFisik, 'genital')->radioList($option, [
            'item' => function($index, $label, $name, $checked, $value) {
              $return = '<label class="modal-radio">';
              if($checked) {
                $return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_genital">';
              }
              else {
                $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_genital">';
              }
              $return .= '<i></i>';
              $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
              $return .= '</label>';
              return $return;
            },
            'inline' => true
          ]); ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'note_genital')->textInput([
            'class' => 'form-control input-sm note_genital',
            ])->label(false); ?>
        </div>
      </div>
      <h5><strong>EKSTREMITAS</strong></h5>
      <hr />
      <div class="row">
        <h5>Deformitas</h5>
        <div class="col-md-2">
          <label for="">Kanan Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_deformitas_kanan_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[deformitas_kanan_atas]" class="opt_deformitas_kanan_atas">&nbsp;&nbsp;Ada']]
            ])->textInput(['class' => 'form-control input-sm note_deformitas_kanan_atas', 'readonly' => true])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kanan Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_deformitas_kanan_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[deformitas_kanan_bawah]" class="opt_deformitas_kanan_bawah">&nbsp;&nbsp;Ada']]
            ])->textInput(['class' => 'form-control input-sm note_deformitas_kanan_bawah', 'readonly' => true])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_deformitas_kiri_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[deformitas_kiri_atas]" class="opt_deformitas_kiri_atas">&nbsp;&nbsp;Ada']]
            ])->textInput(['class' => 'form-control input-sm note_deformitas_kiri_atas', 'readonly' => true])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_deformitas_kiri_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[deformitas_kiri_bawah]" class="opt_deformitas_kiri_bawah">&nbsp;&nbsp;Ada']]
            ])->textInput(['class' => 'form-control input-sm note_deformitas_kiri_bawah', 'readonly' => true])->label(false);
          ?>
        </div>
      </div>
      <div class="row">
        <h5>Fungsi Motorik</h5>
        <div class="col-md-2">
          <label for="">Kanan Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_motorik_kanan_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_motorik_kanan_atas]" class="opt_fungsi_motorik_kanan_atas">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_motorik_kanan_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kanan Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_motorik_kanan_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_motorik_kanan_bawah]" class="opt_fungsi_motorik_kanan_bawah">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_motorik_kanan_bawah'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_motorik_kiri_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_motorik_kiri_atas]" class="opt_fungsi_motorik_kiri_atas">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_motorik_kiri_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_motorik_kiri_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_motorik_kiri_bawah]" class="opt_fungsi_motorik_kiri_bawah">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_motorik_kiri_bawah'])->label(false);
          ?>
        </div>
      </div>
      <div class="row">
        <h5>Fungsi Sensorik</h5>
        <div class="col-md-2">
          <label for="">Kanan Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_sensorik_kanan_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_sensorik_kanan_atas]" class="opt_fungsi_sensorik_kanan_atas">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_sensorik_kanan_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kanan Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_sensorik_kanan_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_sensorik_kanan_bawah]" class="opt_fungsi_sensorik_kanan_bawah">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_sensorik_kanan_bawah'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_sensorik_kiri_atas', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_sensorik_kiri_atas]" class="opt_fungsi_sensorik_kiri_atas">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_sensorik_kiri_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_fungsi_sensorik_kiri_bawah', [
                'addon' => ['prepend' => ['content' => '<input type="checkbox" name="PemeriksaanFisikDefaultForm[fungsi_sensorik_kiri_bawah]" class="opt_fungsi_sensorik_kiri_bawah">&nbsp;&nbsp;Normal']]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_sensorik_kiri_bawah'])->label(false);
          ?>
        </div>
      </div>
      <div class="row">
        <h5>Refleks Fisiologis</h5>
        <div class="col-md-2">
          <label for="">Kanan Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_fisiologis_kanan_atas', [
                'addon' => [
                  'prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kanan_atas]" class="opt_refleks_fisiologis_kanan_atas" id="refleks_fisiologis_kanan_atas_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kanan_atas]" class="opt_refleks_fisiologis_kanan_atas" id="refleks_fisiologis_kanan_atas_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_fisiologis_kanan_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kanan Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_fisiologis_kanan_bawah', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kanan_bawah]" class="opt_refleks_fisiologis_kanan_bawah" id="refleks_fisiologis_kanan_bawah_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kanan_bawah]" class="opt_refleks_fisiologis_kanan_bawah" id="refleks_fisiologis_kanan_bawah_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_fisiologis_kanan_bawah'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_fisiologis_kiri_atas', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kiri_atas]" class="opt_refleks_fisiologis_kiri_atas" id="refleks_fisiologis_kiri_atas_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kiri_atas]" class="opt_refleks_fisiologis_kiri_atas" id="refleks_fisiologis_kiri_atas_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_fisiologis_kiri_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_fisiologis_kiri_bawah', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kiri_bawah]" class="opt_refleks_fisiologis_kiri_bawah" id="refleks_fisiologis_kiri_bawah_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_fisiologis_kiri_bawah]" class="opt_refleks_fisiologis_kiri_bawah" id="refleks_fisiologis_kiri_bawah_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_fisiologis_kiri_bawah'])->label(false);
          ?>
        </div>
      </div>
      <div class="row">
        <h5>Refleks Patologis</h5>
        <div class="col-md-2">
          <label for="">Kanan Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_patologis_kanan_atas', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_patologis_kanan_atas]" class="opt_refleks_patologis_kanan_atas" id="refleks_patologis_kanan_atas_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_patologis_kanan_atas]" class="opt_refleks_patologis_kanan_atas" id="refleks_patologis_kanan_atas_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_patologis_kanan_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kanan Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_patologis_kanan_bawah', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_patologis_kanan_bawah]" class="opt_refleks_patologis_kanan_bawah" id="refleks_patologis_kanan_bawah_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_patologis_kanan_bawah]" class="opt_refleks_patologis_kanan_bawah" id="refleks_patologis_kanan_bawah_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_patologis_kanan_bawah'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Atas</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_patologis_kiri_atas', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_patologis_kiri_atas]" class="opt_refleks_patologis_kiri_atas" id="refleks_patologis_kiri_atas_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_patologis_kiri_atas]" class="opt_refleks_patologis_kiri_atas" id="refleks_patologis_kiri_atas_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_refleks_patologis_kiri_atas'])->label(false);
          ?>
        </div>
        <div class="col-md-2">
          <label for="">Kiri Bawah</label>
        </div> 
        <div class="col-md-4" style="margin-bottom: 40px;">
          <?= $form->field($modelFisik, 'note_refleks_patologis_kiri_bawah', [
                'addon' => ['prepend' => [
                    'content' => '<input type="radio" value="0" checked name="PemeriksaanFisikDefaultForm[refleks_patologis_kiri_bawah]" class="opt_refleks_patologis_kiri_bawah" id="refleks_patologis_kiri_bawah_positif"><label for="html">+</label>&nbsp;
                    <input type="radio" value="1" name="PemeriksaanFisikDefaultForm[refleks_patologis_kiri_bawah]" class="opt_refleks_patologis_kiri_bawah" id="refleks_patologis_kiri_bawah_negatif"><label for="html">-</label>'
                  ]
                ]
            ])->textInput(['class' => 'form-control input-sm note_fungsi_motorik_kiri_bawah'])->label(false);
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <?= $form->field($modelFisik, 'pemeriksaan_lainnya')->textArea([
            'class' => 'form-control input-sm',
            'rows' => 5
          ])->label(Yii::t('fe', 'Lain-Lain')); ?>
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


<?php
    
    $this->registerJs("
      var data_bmi = ".json_encode($data_bmi)."
      var jeniskelamin = '".$jenis_kelamin."'
      var lakilaki = ".DocoConstants::LOOKUP_LAKI."
      var deformitas_kanan_atas = '".$modelFisik->deformitas_kanan_atas."';
      var deformitas_kanan_bawah = '".$modelFisik->deformitas_kanan_bawah."';
      var deformitas_kiri_atas = '".$modelFisik->deformitas_kiri_atas."';
      var deformitas_kiri_bawah = '".$modelFisik->deformitas_kiri_bawah."';

      var fungsi_motorik_kanan_atas = '".$modelFisik->fungsi_motorik_kanan_atas."';
      var fungsi_motorik_kanan_bawah = '".$modelFisik->fungsi_motorik_kanan_bawah."';
      var fungsi_motorik_kiri_atas = '".$modelFisik->fungsi_motorik_kiri_atas."';
      var fungsi_motorik_kiri_bawah = '".$modelFisik->fungsi_motorik_kiri_bawah."';

      var fungsi_sensorik_kanan_atas = '".$modelFisik->fungsi_sensorik_kanan_atas."';
      var fungsi_sensorik_kanan_bawah = '".$modelFisik->fungsi_sensorik_kanan_bawah."';
      var fungsi_sensorik_kiri_atas = '".$modelFisik->fungsi_sensorik_kiri_atas."';
      var fungsi_sensorik_kiri_bawah = '".$modelFisik->fungsi_sensorik_kiri_bawah."';

      var refleks_fisiologis_kanan_atas = '".$modelFisik->refleks_fisiologis_kanan_atas."';
      var refleks_fisiologis_kanan_bawah = '".$modelFisik->refleks_fisiologis_kanan_bawah."';
      var refleks_fisiologis_kiri_atas = '".$modelFisik->refleks_fisiologis_kiri_atas."';
      var refleks_fisiologis_kiri_bawah = '".$modelFisik->refleks_fisiologis_kiri_bawah."';

      var refleks_patologis_kanan_atas = '".$modelFisik->refleks_patologis_kanan_atas."';
      var refleks_patologis_kanan_bawah = '".$modelFisik->refleks_patologis_kanan_bawah."';
      var refleks_patologis_kiri_atas = '".$modelFisik->refleks_patologis_kiri_atas."';
      var refleks_patologis_kiri_bawah = '".$modelFisik->refleks_patologis_kiri_bawah."';

  ".$this->render('js/__pemeriksaan_fisik.js'), View::POS_END);
    
?>