<?php
// Author : Budi

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <div class="row">
                  <div class="column-1">
                     <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                     <h3 class="panel-title"><b><?= $title; ?></b></h3>
                     <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
               <div class="heading-elements">
                  <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                  </ul>
               </div>
            </div>
            <div class="panel-toolbar clearfix">
               <div class="btn-group pull-left">
                  <?=DocoHelpers::generateToolbar([
                     'search' => [
                        'attributes' => [
                           'data-parent'=>'.filter-pendapatan',
                           'class' => 'btn btn-info btn-labeled btn-xs cari-pendapatan'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                           'data-parent'=>'.filter-pendapatan',
                           'class' => 'btn btn-info btn-labeled btn-xs reset-pendapatan'
                        ]
                     ],
                     'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                           'id' => 'excel-bgprocess',
                           'data-parent' => '.filter-pendapatan',
                           'data-options' => 'excel-serconn',
                           'data-target' => '#modal_backdrop',
                           'data-width' => '75%',
                           'data-url' => '/kasir/lap-pendapatan-kasir/show-popup?',
                        ]
                     ],
                     // 'excel' => [
                     //    'attributes' => [
                     //       'data-parent' => '.filter-pendapatan',
                     //       'class' => 'btn btn-info btn-labeled btn-xs excel-pendapatan'
                     //    ]
                     // ]
                  ]);?>
               </div>
            </div>
            <div class="panel-body">
               <form action="" id="search-form">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="col-md-3 div-date-range">
                           <label>Tanggal Pembayaran: </label>
                           <div class="input-group">
                              <input type="text" id="rangeDemoStart" value="" class="form-control startDate input-xs"/>
                              <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                              <input type="text" id="rangeDemoFinish" value="" readonly="true" class="form-control endDate input-xs"/>
                              <input type="text" style="display:none" class="targetDate">
                           </div>
                        </div>
                        <div class="col-md-3 div-date-range">
                           <label>Tanggal Pulang: </label>
                           <div class="input-group">
                              <input type="text" id="rangeDemoStartPulang" value="" class="form-control startDatePulang input-xs"/>
                              <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                              <input type="text" id="rangeDemoFinishPulang" value="" readonly="true" class="form-control endDatePulang input-xs"/>
                              <input type="text" style="display:none" class="targetDatePulang">
                           </div>
                        </div>
                        <div class="col-md-3">
                           <label>Pilih Unit Pelayanan : </label>
                           <?= Html::dropDownList('unit', '', [
                                 'RAJAL' => 'RAWAT JALAN',
                                 'RANAP' => 'RAWAT INAP'
                              ], [
                              'class' => 'form-control select2',
                              'id' => 'filter_unit',
                              'prompt' => Yii::t('fe', '----Pilih Semua----'),
                           ]); ?>
                        </div>
                        <div class="col-md-3">
                           <label>Pilih Kelas Tagihan : </label>
                           <?= Html::dropDownList('kelas', '', [], [
                              'class' => 'form-control select2',
                              'id' => 'filter_kelas',
                              'prompt' => Yii::t('fe', '----Pilih Semua----'),
                           ]); ?>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="col-md-3">
                           <label>Pilih Kelompok Tindakan : </label>
                           <?= Html::dropDownList('kelompok', '', [], [
                              'class' => 'form-control select2',
                              'id' => 'filter_kelompok',
                              'multiple' => true,
                           ]); ?>
                        </div>
                        <div class="col-md-3">
                           <label>Pilih Nama Tindakan : </label>
                           <?= Html::dropDownList('tindakan', '', [], [
                              'class' => 'form-control select2',
                              'id' => 'filter_tindakan',
                              'multiple' => true,
                           ]); ?>
                        </div>
                     </div>
                  </div>
               </form><br>
               <table id="example" class="table table-condensed" style="width:100%">
                  <thead>
                     <tr class="bg-inverse">
                        <th><?=\Yii::t("fe", "Kode Tindakan");?></th>
                        <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                        <th><?=\Yii::t("fe", "Jumlah");?></th>
                        <th><?=\Yii::t("fe", "Sub Total");?></th>
                        <th></th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                     <tr style="background-color: #FDEDEC;">
                        <th>GRAND TOTAL </th>
                        <th></th>
                        <th></th>
                        <th style="font-size: 13px;"></th>
                     </tr>
                  </tfoot>
               </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs($this->render("index.js"), View::POS_END, 'index');
?>
