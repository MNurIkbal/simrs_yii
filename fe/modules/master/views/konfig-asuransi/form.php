<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-white">
      <div class="panel-heading">
        <!-- breadcrumbs replace with this -->
        <div class="row">
          <div class="column-1">
            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="logo">
          </div>
          <div class="column-2">
            <h3 class="panel-title"><b><?= $this->title ?></b></h3>
            <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
          </div>
        </div>
        <!-- end -->
      </div>

      <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([

          'back' => [
            'attributes' => [
              'href' => '/master/konfig-asuransi/index'
            ]
          ],
          'save' => [
            'title' => \Yii::t('fe', 'Simpan'),
            'icon' => 'fa fa-check-square-o',
            'attributes' => [
              'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
              'id' => 'btn-save',
              'onClick' => null,
            ]
          ],
          'cek-koneksi' => [
            'title' => 'Cek Koneksi',
            'icon' => 'fa fa-refresh',
            'attributes' => [
              'data-options' => 'click',
              'id' => 'cek-koneksi',
            ]
          ],
        ]); ?>
      </div>

      <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
          'id' => 'konfig-asuransi-form',
          'action' => '/master/konfig-asuransi/tambah',
          'enableAjaxValidation' => false,
          'enableClientValidation' => false,
          'type' => ActiveForm::TYPE_HORIZONTAL,
          'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
          ],
        ]);
        ?>
        <?= $form->field($model, 'konfigasuransi_id')->hiddenInput(['id' => 'konfigasuransi_id'])->label(false); ?>
        <?= $form->field($model, 'provider_code')->hiddenInput(['id' => 'provider_code'])->label(false); ?>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'provider_id')
                ->dropDownList(
                  [],
                  ['id' => 'provider_id', 'class' => 'select2', 'prompt' => '-- Pilih Provider --']
                )
                ->label();
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'base_url')
                ->textInput([
                  'class' => 'form-control input-sm'
                ])->label();
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'auth')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model, 'url_referensi_benefit')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'url_cek_eligibilitas')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model, 'url_pengesahan')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>

          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'url_pendaftaran')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model, 'url_jaminan')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'url_kunjungan')
                ->textArea([
                  'class' => 'form-control input-sm',
                  'rows' => 5,
                ])->label();
              ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model, 'session_expired')
                ->textInput([
                  'class' => 'form-control input-sm doco-number'
                ])->label();
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model, 'is_active')->radioList($statusAktif, [
                'inline' => true,
              ])->label(Yii::t("fe", "Status")); ?>
            </div>
          </div>
        </div>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>

  <?php
  $this->registerJs('
  const _id = "' . $id . '";
  const optionProvider = ' . $optionProvider . ';
  ', View::POS_END, 'b-index');
  $this->registerJs($this->render('form.js'), View::POS_END);
  ?>