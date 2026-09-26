<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
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
               <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                  <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
               </div>
            </div>
         </div>
         <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
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
               'monitor' => [
                  'title' => 'Monitor',
                  'icon' => 'fa fa-folder-open',
                  'attributes' => [
                     'data-target' => '/penjamin-asuransi/monitoring-pasien-bpjs/monitor?id=',
                     'disabled' => true,
                     'class' => 'monitor',
                  ]
               ],
               'set-diagnosa' => [
                  'title' => 'Set Diagnosa',
                  'icon' => 'fa fa-cogs',
                  'attributes' => [
                     'class' => 'set-diagnosa',
                     'data-width' => '75%',
                     'data-options' => 'modal',
                     'data-target' => '#modal_backdrop',
                     'data-url' => Url::home().('penjamin-asuransi/monitoring-pasien-bpjs/set-diagnosa?id=')
                  ]
               ],
               'pdf-bgprocess' => [
                  'type' => 'button',
                  'title' => 'Cetak PDF',
                  'icon' => 'fa fa-file-pdf-o',
                  'method' => 'not-exist',
                  'attributes' => [
                     'id'=>'excel-bgprocess',
                     'data-options' => 'click',
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'data-url' => '/penjamin-asuransi/monitoring-pasien-bpjs/show-popup?type=1&id=',
                  ]
               ],
               'excel-bgprocess' => [
                  'type' => 'button',
                  'title' => 'Unduh Excel',
                  'icon' => 'fa fa-file-excel-o',
                  'method' => 'not-exist',
                  'attributes' => [
                     'id'=>'excel-bgprocess',
                     'data-options' => 'click',
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'data-url' => '/penjamin-asuransi/monitoring-pasien-bpjs/show-popup?type=2&id=',
                  ]
               ],
            ]);?>
         </div>
         <div class="panel-body">
            <div class="table-wrapper table-scroll-x">
            <div class="col-md-12">
               <div class='legend-index'>
                  <div class='legend-header'>Keterangan</div>
                  <div class="legend-wrapper">
                     <div class="legend-information">
                        <div class="legend-information__color" style="background-color:#57bed6"></div>
                        <div class="legend-information__text" >Stop Akomodasi</div>
                     </div>
                  </div>
               </div>
            </div>
            <br>
            <div class="col-sm-4"><div style="background-color:#B94747;color:white;text-align: center;">Alokasi >= 100%</div></div>
            <div class="col-sm-4"><div style="background-color:#DD972C;color:white;text-align: center;">Alokasi >= 75% & < 100% </div></div>
            <div class="col-sm-4"><div style="background-color:#829232;color:white;text-align: center;">Alokasi < 75% </div></div>
            <br><br>
            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
               <thead>
                  <tr class="bg-inverse">
                     <th width="1"></th>
                     <th>No</th>
                     <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                     <th><?=\Yii::t("fe", "Tanggal Keluar");?></th>
                     <th><?=\Yii::t("fe", "Pasien");?></th>
                     <th><?=\Yii::t("fe", "No SEP");?></th>
                     <th><?=\Yii::t("fe", "Penjamin");?></th>
                     <th><?=\Yii::t("fe", "Ruangan");?></th>
                     <th><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
                     <th><?=\Yii::t("fe", "Diagnosa Utama");?></th>
                     <th><?=\Yii::t("fe", "Diagnosa Penyerta");?></th>
                     <th><?=\Yii::t("fe", "Tindakan");?></th>
                     <th><?=\Yii::t("fe", "Tagihan RS");?></th>
                     <th><?=\Yii::t("fe", "Tarif Inacbg");?></th>
                     <th><?=\Yii::t("fe", "Persentase (%)");?></th>
                     <th><?=\Yii::t("fe", "Status");?></th>
                     <th><?=\Yii::t("fe", "Status Periksa");?></th>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
            </div>
         </div>
      </div>
   </div>
</div>

<?php

$this->registerJs('
   var listStatusPeriksa = ' . json_encode($listStatusPeriksa) . ';
',View::POS_END);
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>

