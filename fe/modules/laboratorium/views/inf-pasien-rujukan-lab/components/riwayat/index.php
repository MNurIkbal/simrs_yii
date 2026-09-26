<?php

/**
 * @author Randy Vianda Putra
 * @todo View Informasi Pasien Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;
?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
               'search' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                     'data-table-id' => 'table-riwayat',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset-riwayat',
                     'data-table-id' => 'table-riwayat',
                     'data-options' => 'click',
                  ]
               ],
               'hasil' => [
                  'title' => \Yii::t('fe', 'Hasil Pemeriksaan'),
                  'icon' => 'fa fa-eye',
                  'attributes' => [
                     'class' => 'spa',
                     'data-options' => 'click',
                     'data-render' => 'hasil-lab?id=', 
                     'data-tab' => 'tab-riwayat',
                     'data-target' => '#view-riwayat',
                     'data-type' => 'wp',
                     'id' => 'btn-hasil'
                  ]
               ],
               'cetak' => [
                  'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
                  'icon' => 'fa fa-print',
                  'attributes' => [
                     'id' => 'btn-cetak',
                     'data-target' => $cetakPemeriksaan,
                     'data-pages' => '_blank',
                  ]
               ],
               'tagihan' => [
                  'title' => \Yii::t('fe', 'Rincian Tagihan'),
                  'icon' => 'fa fa-money',
                  'attributes' => [
                     'id'=>'cetak-tagihan',
                     'data-target' => '/laboratorium/inf-pasien-rujukan-lab/print-rincian?id=',
                     'data-pages' => '_blank',
                  ]
               ],
            ],'#table-riwayat');
            ?>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="table-wrapper table-scroll-x">
                  <div class="col-md-6"></div>
                  <?= Yii::$app->controller->renderPartial('legend_cara_bayar', [ 'legendCaraBayar' => $legendCaraBayar ]) ?>
                  <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="table-riwayat" style="width: 100%;">
                     <thead>
                        <tr class="bg-inverse">
                           <th width="5%"></th>
                           <th><?=Yii::t('fe', 'No'); ?></th>
                           <th><?=Yii::t('fe', 'Status Periksa'); ?></th>
                           <th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
                           <th><?=Yii::t('fe', 'Pasien'); ?></th>
                           <th><?=Yii::t('fe', 'Pemeriksaan'); ?></th>
                           <th><?=Yii::t('fe', 'Dokter'); ?></th>
                           <th><?=Yii::t('fe', 'Cara Bayar / Penjamin'); ?></th>
                           <th><?=Yii::t('fe', 'Status Bayar'); ?></th>
                           <th><?=Yii::t('fe', 'Asal Rujukan'); ?></th>
                           <th><?=Yii::t('fe', 'No Lab'); ?></th>
                        </tr>    
                     </thead>
                     <tbody>
                     </tbody>
                  </table>
               </div>
               <!-- <audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio> -->
            </div>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs('
var data = '.json_encode($data).'
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
