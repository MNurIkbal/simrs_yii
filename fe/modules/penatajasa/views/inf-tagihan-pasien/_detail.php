<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use app\modules\penatajasa\models\PenjaminForm;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Penata Jasa', 'url' => ['/informasi-tagihan-pasien']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
   .mr-1{
      margin-top: 1%
   }
   .modal-dialog.modal-lg {
      width: 40%;
   }
</style>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-heading">
            <div class="row">
               <div class="column-1">
                  <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
               </div>
               <div class="column-2">
                  <h3 class="panel-title"><b><?= Yii::t('fe', 'Detail'). ' ' .Yii::t('fe', $title); ?></b></h3>
                  <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
               </div>
            </div>
         </div>
         <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
               'back' => [
                  'attributes' => [
                        'href' => '/penatajasa/pencarian-pasien/#',
                  ]
               ],
               // 'save' => [
               //     'attributes' => [
               //         'onClick' => null,
               //         'id' => 'simpan-detail',
               //         'disabled' => true
               //     ]
               // ],
               // 'rincian' => [
               //     'title' => Yii::t('fe', 'Rincian Tagihan'),
               //     'icon' => 'fa fa-print',
               //     'attributes' => [
               //         'id'=>'cetak-rincian-tagihan',
               //         'data-options' => 'link',
               //         'class'=>'spa',
               //         'data-target' => '/penatajasa/inf-tagihan-pasien/export-pdf-rincian-tagihan?pendaftaran_id=',
               //         'disabled'=>'true',
               //         'target'=>'_blank',
               //         'disabled' => true
               //     ]
               // ],
               // 'rekap-tagihan' => [
               //     'type' => 'button',
               //     'title' =>  Yii::t('fe', 'Rekap Tagihan'),
               //     'icon' => 'fa fa-print',
               //     'attributes' => [
               //         'class' => 'data-lihat print-tagihan',
               //         'id' => 'print-tagihan',
               //         'method' => 'json',
               //         'data-options' => 'link',
               //         'disabled' => true
               //     ]
               // ],
               // 'riwayat-pasien' => [
               //     'title' => Yii::t('fe', 'Riwayat Pasien'),
               //     'icon' => 'fa fa fa-square',
               //     'attributes' => [
               //         'id'=>'riwayat-pasien',
               //         'data-options' => 'link',
               //         'class'=>'spa',
               //         'data-target' => '/penatajasa/inf-tagihan-pasien/export-pdf-riwayat-pasien?pendaftaran_id=',
               //         'disabled'=>'true',
               //         'target'=>'_blank',
               //         'disabled' => true
               //     ]
               // ],
               'log-activity' => [
                  'type' => 'button',
                  'title' => Yii::t('fe', 'Log Activity'),
                  'icon' => 'fa fa-list',
                  'attributes' => [
                     'id' => 'log-activity',
                     'data-popup'=>'tooltip',
                     'data-toggle'=>'modal',
                     'data-target'=>'#modal_backdrop',
                     'action' => '/penatajasa/inf-tagihan-pasien/log-activity?pendaftaran_id='.$pendaftaran_id,
                     'data-width'=>"65%",
                  ],
               ],
            ]);?>
         </div>
         <div class="panel-body">
            <?=Html::activeHiddenInput($model, '_total_', ['id' => '_total_'])?>
            <?=Yii::$app->controller->renderPartial('partial/_informasi_pasien', [
               'data_pasien' => $data_pasien
            ]);?>
            <?=Yii::$app->controller->renderPartial('partial/tindakan/_list_tindakan', [
               'pendaftaran_id' => $pendaftaran_id
               ]
            );?>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs("
   $(document).ready(function() {
      $('.table-tagihan-tindakan').attr('style', 'width: 1190px !important;');
      $('.flex-1').css('display', 'none');
   });
   var tblPenjamin;
   pendaftaran_id = '".DocoHelpers::encrypt($pendaftaran_id)."';
   id_pendaftaran = '".$pendaftaran_id."';
   nama_pasien = '';
   penjamin_id = '".$data_pasien['penjamin_id']."';
   pasien_id = '".$data_pasien['pasien_id']."';
   instalasi_id = '".$data_pasien['instalasi_id']."';
   kelaspelayanan_id = '".$data_pasien['kelaspelayanan_id']."';
        
".$this->render('js/_detail.js'), View::POS_END, 'js');
?>