<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
  #filter-section__example {
    z-index: 900 !important;
  }

  .switch-container {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    margin: 0;
  }

  .switch-container input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
  }

  .switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  .switch-container input:checked+.switch-slider {
    background-color: #2196F3;
  }

  .switch-container input:focus+.switch-slider {
    box-shadow: 0 0 1px #2196F3;
  }

  .switch-container input:checked+.switch-slider:before {
    transform: translateX(26px);
  }

  .switch-active {
    background-color: #2196F3 !important;
  }

  .switch-inactive {
    background-color: #f44336 !important;
  }

  /* Custom styling for delete button */
  .data-delete {
    background-color: #f44336 !important;
    border-color: #f44336 !important;
  }

  .data-delete:hover {
    background-color: #d32f2f !important;
    border-color: #d32f2f !important;
  }

  .data-delete:focus {
    background-color: #d32f2f !important;
    border-color: #d32f2f !important;
  }

  /* Disabled button styling */
  .btn.disabled,
  .btn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
  }

  .data-edit.disabled,
  .data-edit:disabled,
  .data-delete.disabled,
  .data-delete:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
  }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-white">
      <div class="panel-heading">
        <div class="row">
          <div class="column-1">
            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="logo">
          </div>
          <div class="column-2">
            <h3 class="panel-title"><b><?= $this->title ?></b></h3>
            <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
          </div>
        </div>
      </div>

      <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'search' => [
                'attributes' => [
                    'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                    'data-table-id' => 'example',
                    'data-options' => 'click',
                ]
            ],
            'reset' => [
                'attributes' => [
                    'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                    'data-table-id' => 'example',
                    'data-options' => 'click',
                ]
            ],
          'add' => [
            'attributes' => [
              'data-toggle' => 'modal',
              'data-target' => '#modal_backdrop',
              'data-width' => '75%',
              'action' => '/master/kategori-obat/create',
            ]
          ],
          'edit' => [
            'attributes' => [
              'data-options' => 'modal',
              'data-target' => '#modal_backdrop',
              'data-width' => '75%',
              'action' => '/master/kategori-obat/create?id=',
              'data-url' => '/master/kategori-obat/create?id=',
            ]
          ],
          // 'delete' => [
          //   'method' => '#',
          //   'attributes' => [
          //     // 'data-options' => 'click',
          //     'data-additional' => 'data-rm',
          //     'data-target' => '/master/kategori-obat/delete?id='
          //   ]
          // ],
        ], '#example'); ?>
      </div>

      <div class="panel-body">
        <div class="table-wrapper table-scroll-x">
          <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
              <tr class="bg-inverse">
                <th width="5%"></th>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Instalasi</th>
                <th>Penjamin</th>
                <th>Jumlah Obat</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center" colspan="7"><?= Yii::t("fe", "Data tidak ditemukan."); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php
    $this->registerJs('
        var instalasiOption = ' . json_encode($instalasi) . ';
        var penjaminOption = ' . json_encode($penjamin) . ';
    ',View::POS_END,'b-index');
    $this->registerJs($this->render('index.js'), View::POS_END);
  ?>